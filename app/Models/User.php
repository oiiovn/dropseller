<?php

namespace App\Models;

use App\Models\Crm\Affiliate;
use App\Models\Crm\Role;
use App\Models\Crm\UserNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'locale',
        'currency',
        'affiliate_id',
        'image',
        'password',
        'referral_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function shops()
    {
        return $this->hasMany(Shop::class, 'user_id', 'id'); // Một user có nhiều shop
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function crmRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'crm_role_user');
    }

    public function hasCrmRole(string $role): bool
    {
        return $this->crmRoles()->where('name', $role)->exists();
    }

    public function hasCrmAnyRole(array $roles): bool
    {
        return $this->crmRoles()->whereIn('name', $roles)->exists();
    }

    public function crmUserNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}
