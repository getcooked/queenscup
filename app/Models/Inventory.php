<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'regular_price',
        'large_price',
        'stock',
        'description',
        'image_path',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'large_price' => 'decimal:2',
        'stock' => 'integer',
    ];
}
