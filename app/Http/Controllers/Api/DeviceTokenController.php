<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Registration of FCM device tokens. The Android app calls this on launch and
 * whenever Firebase rotates the token.
 */
class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:4096'],
            'platform' => ['nullable', Rule::in(['android', 'ios', 'web'])],
            'reservation_reference' => ['nullable', 'string', 'max:20'],
        ]);

        DeviceToken::register($data['token'], [
            'platform' => $data['platform'] ?? 'android',
            'user_id' => $request->user()?->id,
            'reservation_reference' => isset($data['reservation_reference'])
                ? strtoupper($data['reservation_reference'])
                : null,
        ]);

        return response()->json(['registered' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:4096']]);

        DeviceToken::where('token_hash', hash('sha256', $data['token']))->delete();

        return response()->json(['registered' => false]);
    }
}
