<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'transactions'; // Nếu bảng có tên khác, hãy thay thế tên chính xác

    // Cột khóa chính
    protected $primaryKey = 'ID'; // Nếu khóa chính không phải là `id`

    // Nếu bảng không có các cột timestamps (created_at, updated_at)
    public $timestamps = false;

    // Các trường có thể điền dữ liệu
    protected $fillable = [
        'ID',
        'Bank',
        'AccountNumber',
        'TransactionDate',
        'TransactionID',
        'Amount',
        'Type',
        'Description',
    ];
}
