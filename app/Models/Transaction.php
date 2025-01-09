<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'bank', 'account_number',
        'transaction_date', 'transaction_id', 'amount',
        'type', 'description'
    ];


}

