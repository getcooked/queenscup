<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends Firebase Cloud Messaging pushes over the HTTP v1 API.
 *
 * The legacy "server key" endpoint Google shut down in 2024 is not used. v1
 * needs an OAuth2 bearer token from a service account, which is minted here by
 * signing a JWT with openssl so the project needs no extra Composer package.
 *
 * With no credentials configured every call is a silent no-op, so the rest of
 * the app works untouched until Firebase is set up.
 */
class PushNotifier
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';
    private const TOKEN_URI = 'https://oauth2.googleapis.com/token';
    private const CACHE_KEY = 'fcm.access_token';

    public function isConfigured(): bool
    {
        $path = config('queenscup.fcm.credentials');

        return ! empty(config('queenscup.fcm.project_id')) && ! empty($path) && is_readable($path);
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, string>  $data  Delivered to the app for deep linking.
     * @return int Number of devices the message reached.
     */
    public function send(array $tokens, string $title, string $body, array $data = []): int
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if ($tokens === [] || ! $this->isConfigured()) {
            return 0;
        }

        $accessToken = $this->accessToken();

        if (! $accessToken) {
            return 0;
        }

        $projectId = config('queenscup.fcm.project_id');
        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $delivered = 0;

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)
                    ->timeout(10)
                    ->post($endpoint, [
                        'message' => [
                            'token' => $token,
                            'notification' => ['title' => $title, 'body' => $body],
                            // Values must be strings for FCM data payloads.
                            'data' => array_map('strval', $data),
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'channel_id' => 'reservation_status',
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $delivered++;
                    continue;
                }

                // The device uninstalled the app or the token rotated; drop it
                // so the table does not fill with dead registrations.
                if (in_array($response->status(), [404, 400], true)) {
                    DeviceToken::where('token_hash', hash('sha256', $token))->delete();
                }

                Log::warning('FCM send failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
            } catch (\Throwable $exception) {
                Log::warning('FCM send threw', ['message' => $exception->getMessage()]);
            }
        }

        return $delivered;
    }

    /**
     * Exchanges the service account key for a short lived bearer token, cached
     * just under Google's one hour expiry.
     */
    private function accessToken(): ?string
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(50), function () {
            $credentials = json_decode((string) file_get_contents(config('queenscup.fcm.credentials')), true);

            if (! is_array($credentials) || empty($credentials['client_email']) || empty($credentials['private_key'])) {
                Log::error('FCM credentials file is missing client_email or private_key.');

                return null;
            }

            $now = time();
            $claims = [
                'iss' => $credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URI,
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $segments = [
                $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])),
                $this->base64Url(json_encode($claims)),
            ];

            $signature = '';
            if (! openssl_sign(implode('.', $segments), $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
                Log::error('FCM: could not sign the service account assertion.');

                return null;
            }

            $segments[] = $this->base64Url($signature);

            $response = Http::asForm()->timeout(10)->post(self::TOKEN_URI, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => implode('.', $segments),
            ]);

            if (! $response->successful()) {
                Log::error('FCM token exchange failed', ['body' => $response->body()]);

                return null;
            }

            return $response->json('access_token');
        });
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
