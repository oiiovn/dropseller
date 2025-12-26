<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name', 
        'quantity_sold',
        'original_quantity',
        'sku',
        'product_image',
        'stock',
        'category',
        'status',
        'picked_at'
    ];

    protected $casts = [
        'picked_at' => 'datetime',
    ];

    // Scope để lấy đơn chưa nhặt
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope để lấy đơn đã nhặt
    public function scopePicked($query)
    {
        return $query->where('status', 'picked');
    }
}
