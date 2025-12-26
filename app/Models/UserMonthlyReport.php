<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMonthlyReport extends Model
{
    protected $fillable = [
        'user_id',
        'id_QT',
        'month',
        'total_topup',
        'total_paid',
        'total_paid_ads',
        'total_canceled',
        'total_return',
        'total_chi',
        'ending_balance',
        'shop_details',
        'tien_thuc_te',
        'khau_trang',
        'tien_phai_thanh_toan',
        'Drop_ships',
        'status_payment',
    ];

    protected $casts = [
        'shop_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shops()
    {
        return $this->belongsTo(Shop::class);
    }
}
