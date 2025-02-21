<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','image','shop_id', 'title', 'message', 'is_read'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id'); // Chỉ dùng nếu `shop_id` trong `shops_name` là UNIQUE
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id'); // Một Notification thuộc về một Order
    }
}