<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtRepaymentPlan extends Model
{
    use HasFactory;

    protected $table = 'debt_repayment_plans';

    protected $fillable = [
        'debt_creditor_id',
        'monthly_percent',
        'pay_day_of_month',
        'start_at',
        'end_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'monthly_percent' => 'decimal:2',
        'start_at' => 'date',
        'end_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function debtCreditor(): BelongsTo
    {
        return $this->belongsTo(DebtCreditor::class, 'debt_creditor_id');
    }
}
