<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image',
        'password',
        'referral_code'
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

    /**
     * Get the roles that belong to the user.
     */
   public function roles()
{
    return $this->belongsToMany(Role::class);
}


    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        // If no roles are passed, return false
        if (empty($roles)) {
            return false;
        }

        // Convert string to array if it's a comma-separated list
        if (is_string($roles) && str_contains($roles, ',')) {
            $roles = explode(',', $roles);
        }
        
        // If it's still a string, convert to array
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        // Use direct DB query for better performance
        return DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $this->id)
            ->whereIn('roles.slug', $roles)
            ->exists();
    }

    /**
     * Assign role to user
     * 
     * @param string|int|Role $role
     * @return void
     */
    public function assignRole($role)
    {
        if (is_numeric($role)) {
            $roleId = $role;
        } elseif (is_string($role)) {
            $roleObj = Role::where('slug', $role)->first();
            if (!$roleObj) return;
            $roleId = $roleObj->id;
        } elseif ($role instanceof Role) {
            $roleId = $role->id;
        } else {
            return;
        }
        
        if (!$this->hasRoleId($roleId)) {
            DB::table('role_user')->insert([
                'user_id' => $this->id,
                'role_id' => $roleId
            ]);
        }
    }

    /**
     * Check if user has role by ID
     * 
     * @param int $roleId
     * @return bool
     */
    protected function hasRoleId($roleId)
    {
        return DB::table('role_user')
            ->where('user_id', $this->id)
            ->where('role_id', $roleId)
            ->exists();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
}
