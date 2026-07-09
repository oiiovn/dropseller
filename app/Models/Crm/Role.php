<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'crm_roles';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'description',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'crm_role_user');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'crm_permission_role');
    }
}
