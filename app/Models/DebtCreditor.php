<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebtCreditor extends Model
{
    use HasFactory;

    protected $table = 'debt_creditors';

    protected $fillable = [
        'user_id',
        'debtor_user_id',
        'total_debt',
        'phone',
        'notes',
        'restructuring_date',
    ];

    protected $casts = [
        'total_debt' => 'decimal:2',
        'restructuring_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'debtor_user_id');
    }

    public function repaymentPlans(): HasMany
    {
        return $this->hasMany(DebtRepaymentPlan::class, 'debt_creditor_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(DebtDistribution::class, 'debt_creditor_id');
    }

    public function activeRepaymentPlan(): ?DebtRepaymentPlan
    {
        return $this->repaymentPlans()->where('is_active', true)->first();
    }

    public function oldDebtItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DebtOldDebtItem::class, 'debt_creditor_id')->orderBy('sort_order')->orderBy('id');
    }
}
