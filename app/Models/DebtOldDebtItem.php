<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtOldDebtItem extends Model
{
    protected $table = 'debt_old_debt_items';

    protected $fillable = [
        'debt_creditor_id',
        'code',
        'principal_amount',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
    ];

    public function debtCreditor(): BelongsTo
    {
        return $this->belongsTo(DebtCreditor::class, 'debt_creditor_id');
    }
}
