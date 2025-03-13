<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shops_name';

    protected $fillable = [
        'shop_id',
        'shop_name',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shop_id', 'shop_id'); // Một shop có nhiều order
    }
    public function revenue()
    {
        return $this->relatedOrders()->sum('total_bill');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'shop_id', 'shop_id');
    }
}
