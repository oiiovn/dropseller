<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ADS extends Model
{
    use HasFactory;

    protected $table = 'ADS'; // Tên bảng trong database

    protected $fillable = [
        'invoice_id',
        'shop_id',
        'date_range',
        'amount',
        'vat',
        'total_amount',
        'payment_status',
        'payment_code',
    ];
    public function shops()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
