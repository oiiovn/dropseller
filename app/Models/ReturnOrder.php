<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_code',
        'shop_id',
        'ngay',
        'filter_date',
        'sku',
        'tong_tien',
        'payment_status',
        'transaction_id',
        'ket_qua',
        'note',
    ];

    protected $casts = [
        'sku' => 'array', // nếu dùng JSON
    ];
    public function order()
{
    return $this->belongsTo(Order::class); // hoặc key khác
}
public function shop()
{
    return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
}


}
