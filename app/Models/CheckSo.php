<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckSo extends Model
{
    protected $table = 'checkso';

    protected $fillable = [
        'phone',
        'username',
        'referral_code', // Thêm trường referral_code
        'type', // Thêm trường type để phân biệt check và submit
        'exists',
        'status',
        'created_at',
        'updated_at',
    ];
}
