<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
    use HasActivityLogOptions, LogsActivity, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('roles', [
            'code',
            'name',
            'status',
        ]);
    }

    protected static function booted(): void
    {
        static::saved(function (Role $role) {
            User::flushRolePermissionsCache($role->id);
        });

        static::deleted(function (Role $role) {
            User::flushRolePermissionsCache($role->id);
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'role_permissions')
            ->withPivot(['permission_id', 'allowed'])
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot(['module_id', 'allowed'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
