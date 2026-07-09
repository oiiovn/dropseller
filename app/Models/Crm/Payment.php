<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'crm_payments';

    protected $fillable = [
        'order_id',
        'customer_id',
        'amount_jpy',
        'discount_jpy',
        'payment_type',
        'payment_method',
        'payment_date',
        'reference_code',
        'payment_status',
        'approval_status',
        'notes',
        'recorded_by',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    protected $casts = [
        'amount_jpy' => 'decimal:2',
        'discount_jpy' => 'decimal:2',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PaymentHistory::class);
    }
}
