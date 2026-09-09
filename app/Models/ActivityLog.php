<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * A record of what staff did.
 *
 * Written as a side effect of the action itself, never edited afterwards, so
 * the panel can answer "who marked this paid?" or "who changed that price?"
 * long after the fact.
 */
class ActivityLog extends Model
{
    use HasFactory;

    public const ACTION_LABELS = [
        'order.status' => 'Order status changed',
        'order.payment' => 'Payment recorded',
        'sale.recorded' => 'Sale rung up',
        'inventory.created' => 'Product added',
        'inventory.updated' => 'Product updated',
        'inventory.deleted' => 'Product removed',
        'staff.login' => 'Signed in',
        'staff.logout' => 'Signed out',
        'staff.created' => 'Staff account created',
        'settings.qr' => 'Payment QR replaced',
    ];

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_role',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Records one action.
     *
     * Deliberately forgiving: a log that throws would take the actual work down
     * with it, and losing a line here is far less bad than refusing a sale.
     */
    public static function record(
        string $action,
        string $description,
        ?User $actor = null,
        array $properties = [],
        ?string $subjectType = null,
        string|int|null $subjectId = null,
    ): ?self {
        try {
            return static::create([
                'user_id' => $actor?->id,
                'actor_name' => $actor?->name,
                'actor_role' => $actor?->role,
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId === null ? null : (string) $subjectId,
                'description' => mb_substr($description, 0, 255),
                'properties' => $properties ?: null,
                'ip' => static::clientIp(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function label(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }

    private static function clientIp(): ?string
    {
        return app()->bound('request') && app('request') instanceof Request
            ? app('request')->ip()
            : null;
    }
}
