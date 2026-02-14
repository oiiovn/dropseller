<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebtMonthlyIncome extends Model
{
    use HasFactory;

    protected $table = 'debt_monthly_incomes';

    protected $fillable = [
        'debtor_user_id',
        'month',
        'year',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'debtor_user_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(DebtDistribution::class, 'debt_monthly_income_id');
    }
}
