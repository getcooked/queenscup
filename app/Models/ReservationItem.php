<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    use HasFactory;

    public const SIZE_REGULAR = 'regular';
    public const SIZE_LARGE = 'large';

    public const SIZES = [self::SIZE_REGULAR, self::SIZE_LARGE];

    protected $fillable = [
        'reservation_id',
        'inventory_id',
        'name',
        'size',
        'unit_price',
        'quantity',
        'line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'line_total' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function sizeLabel(): string
    {
        return $this->size === self::SIZE_LARGE ? '22oz' : '16oz';
    }
}
