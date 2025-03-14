<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'bank',
        'account_number',
        'transaction_date',
        'transaction_id',
        'amount',
        'type',
        'description'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'transaction_id', 'transaction_id'); // Chỉnh theo cột liên kết của bạn
    }
    public function ads()
    {
        return $this->belongsTo(ADS::class, 'transaction_id', 'payment_code');  // Chỉnh sửa `shop_id` -> `id`
    }
    
}