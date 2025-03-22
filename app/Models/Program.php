<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong database.
     */
    protected $table = 'programs';
    /**
     * Các trường có thể được gán giá trị (Mass Assignable).
     */
    protected $fillable = [
        'name_program',
        'products',
        'description',
        'shops',
        'created_by',
        'updated_by',
    ];
    // Ép kiểu để Laravel hiểu đây là mảng
    protected $casts = [
        'shops' => 'array',
    ];

    // Custom accessor để lấy danh sách đối tượng Shop
    public function getShopObjectsAttribute()
    {
        return Shop::whereIn('id', $this->shops ?? [])->get();
    }
}
