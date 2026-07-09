<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    use HasFactory;

    protected $table = 'crm_debts';

    protected $fillable = [
        'order_id',
        'customer_id',
        'total_payable_jpy',
        'total_paid_jpy',
        'outstanding_jpy',
        'due_date',
        'debt_status',
        'last_collected_at',
        'notes',
    ];

    protected $casts = [
        'total_payable_jpy' => 'decimal:2',
        'total_paid_jpy' => 'decimal:2',
        'outstanding_jpy' => 'decimal:2',
        'due_date' => 'date',
        'last_collected_at' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
