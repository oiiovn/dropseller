<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionRule extends Model
{
    use HasFactory;

    protected $table = 'crm_commission_rules';

    protected $fillable = [
        'name',
        'product_category',
        'min_order_amount_jpy',
        'max_order_amount_jpy',
        'min_affiliate_sales_jpy',
        'max_affiliate_sales_jpy',
        'rate_percent',
        'priority',
        'is_active',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'min_order_amount_jpy' => 'decimal:2',
        'max_order_amount_jpy' => 'decimal:2',
        'min_affiliate_sales_jpy' => 'decimal:2',
        'max_affiliate_sales_jpy' => 'decimal:2',
        'rate_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function affiliates(): BelongsToMany
    {
        return $this->belongsToMany(Affiliate::class, 'crm_commission_rule_affiliate');
    }
}
