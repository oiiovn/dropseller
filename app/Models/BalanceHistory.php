<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    protected $fillable = [
        'user_id',
        'amount_change',
        'balance_after',
        'type',
        'reference_id',
        'reference_type',
        'note',
        'transaction_code',
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
