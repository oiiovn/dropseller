<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankWalletBalanceSnapshot extends Model
{
    protected $table = 'bank_wallet_balance_snapshots';

    protected $fillable = [
        'account_number',
        'bank',
        'balance_snapshot',
        'as_of_date',
    ];

    protected $casts = [
        'balance_snapshot' => 'decimal:2',
        'as_of_date' => 'date',
    ];
}
