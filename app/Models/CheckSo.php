<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckSo extends Model
{
    protected $table = 'checkso';

    // app/Models/CheckSo.php
    protected $fillable = [
        'username',
        'referral_code',
        'phone',
        'exists',
        'status',
        'type'
    ];
}
