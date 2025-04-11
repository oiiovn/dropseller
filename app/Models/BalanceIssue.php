<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceIssue extends Model
{
    protected $fillable = [
        'user_id',
        'balance_history_id',
        'expected_balance',
        'actual_balance',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balanceHistory()
    {
        return $this->belongsTo(BalanceHistory::class);
    }
}
