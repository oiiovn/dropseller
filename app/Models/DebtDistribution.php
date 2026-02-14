<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtDistribution extends Model
{
    use HasFactory;

    protected $table = 'debt_distributions';

    protected $fillable = [
        'debt_monthly_income_id',
        'debt_creditor_id',
        'amount',
        'percent_applied',
        'transaction_code',
        'status',
        'paid_at',
        'bank_transaction_ref',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percent_applied' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function debtMonthlyIncome(): BelongsTo
    {
        return $this->belongsTo(DebtMonthlyIncome::class, 'debt_monthly_income_id');
    }

    public function debtCreditor(): BelongsTo
    {
        return $this->belongsTo(DebtCreditor::class, 'debt_creditor_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
