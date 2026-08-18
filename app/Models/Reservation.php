<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    public const SERVICE_DINE_IN = 'dine_in';
    public const SERVICE_TAKE_OUT = 'take_out';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_GCASH = 'gcash';
    public const PAYMENT_PAYMAYA = 'paymaya';

    public const SERVICE_TYPES = [self::SERVICE_DINE_IN, self::SERVICE_TAKE_OUT];

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PREPARING,
        self::STATUS_READY,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    public const PAYMENT_METHODS = [self::PAYMENT_CASH, self::PAYMENT_GCASH, self::PAYMENT_PAYMAYA];

    /**
     * A reservation may only move forward along the counter workflow, or be
     * cancelled while it has not been handed over yet. Completed and cancelled
     * are terminal.
     */
    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
        self::STATUS_PREPARING => [self::STATUS_READY, self::STATUS_CANCELLED],
        self::STATUS_READY => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_COMPLETED => [],
        self::STATUS_CANCELLED => [],
    ];

    protected $fillable = [
        'reference',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_contact',
        'branch',
        'service_type',
        'status',
        'cup_count',
        'subtotal',
        'takeout_fee',
        'total',
        'payment_method',
        'payment_status',
        'paid_by',
        'paid_at',
        'ready_at',
        'completed_at',
        'cancelled_at',
        'source',
        'notes',
    ];

    protected $casts = [
        'cup_count' => 'integer',
        'subtotal' => 'decimal:2',
        'takeout_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Short, unambiguous code the customer quotes at the counter. Excludes
     * characters that are easy to misread out loud (0/O, 1/I).
     */
    public static function generateReference(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $reference = 'QC-'.$code;
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function isTakeOut(): bool
    {
        return $this->service_type === self::SERVICE_TAKE_OUT;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::ALLOWED_TRANSITIONS[$this->status] ?? [], true);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PREPARING, self::STATUS_READY]);
    }

    public function statusLabel(): string
    {
        return [
            self::STATUS_PENDING => 'Reservation received',
            self::STATUS_PREPARING => 'Being prepared',
            self::STATUS_READY => 'Ready for pick up',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ][$this->status] ?? Str::headline($this->status);
    }
}
