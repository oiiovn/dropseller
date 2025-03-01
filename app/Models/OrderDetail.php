<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'shop_id',
        'sku',
        'image',
        'product_name',
        'quantity',
        'unit_cost',
        'total_cost',
    ];
    public function order()
{
    return $this->belongsTo(Order::class, 'order_id', 'id');
}

public function shop()
{
    return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
}
}
