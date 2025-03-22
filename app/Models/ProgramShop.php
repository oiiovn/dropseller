<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramShop extends Model
{
    use HasFactory;

    protected $table = 'program_shop'; // Đặt tên bảng

    protected $fillable = [
        'shop_id',
        'program_id',
        'total_payment',
        'status_program',
        'status_payment',
        'payment_code',
        'confirmer',
    ];

    /**
     * Liên kết với bảng Shops
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Liên kết với bảng Programs
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Liên kết với bảng Users (người xác nhận)
     */
    public function confirmerUser()
    {
        return $this->belongsTo(User::class, 'confirmer');
    }
}
