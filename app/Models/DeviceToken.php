<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'token_hash',
        'platform',
        'user_id',
        'reservation_reference',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    /**
     * Registers or refreshes a device. Re-registering the same token updates
     * the row rather than creating duplicates, which is what the Android app
     * does on every launch since FCM may rotate tokens at any time.
     */
    public static function register(string $token, array $attributes = []): self
    {
        return static::updateOrCreate(
            ['token_hash' => hash('sha256', $token)],
            array_merge($attributes, [
                'token' => $token,
                'last_seen_at' => now(),
            ])
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
