<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'affiliate_id',
        'full_name',
        'phone',
        'email',
        'address',
        'facebook_url',
        'google_maps_url',
        'prefecture',
        'city',
        'postal_code',
        'preferred_bicycle_line',
        'consultation_status',
        'last_consulted_at',
        'last_purchased_at',
        'purchase_interest',
        'consultation_history',
    ];

    protected $casts = [
        'last_consulted_at' => 'date',
        'last_purchased_at' => 'date',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
}
