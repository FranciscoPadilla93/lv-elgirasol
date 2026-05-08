<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasActivityLogOptions;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Throwable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

    /** @use HasFactory<UserFactory> */
    use HasActivityLogOptions, HasApiTokens, HasFactory, LogsActivity, Notifiable;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('users', [
            'name',
            'email',
            'role_id',
            'status',
        ]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function cacheStore(): Repository
    {
        // return Cache::store('redis');
        return Cache::store(config('cache.default'));
    }

    public static function flushRolePermissionsCache(?int $roleId = null): void
    {
        if ($roleId === null) {
            static::incrementCacheVersion('roles:version');

            return;
        }

        static::incrementCacheVersion("roles:{$roleId}:version");
    }

    protected static function roleCacheVersion(int $roleId): string
    {
        return static::cacheVersion('roles:version')
            .':'.static::cacheVersion("roles:{$roleId}:version");
    }

    protected static function cacheVersion(string $key): int
    {
        try {
            return (int) static::cacheStore()->rememberForever($key, fn () => 1);
        } catch (Throwable) {
            return 1;
        }
    }

    protected static function incrementCacheVersion(string $key): void
    {
        try {
            if (static::cacheStore()->increment($key) === false) {
                static::cacheStore()->forever($key, 2);
            }
        } catch (Throwable) {
            return;
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        $role = $this->getCachedRoleWithPermissions();

        return in_array($role?->code, $roles, true);
    }

    public function getCachedRoleWithPermissions(): ?Role
    {
        if ($this->role_id === null) {
            return null;
        }

        try {
            return static::cacheStore()->remember(
                "roles:{$this->role_id}:v".static::roleCacheVersion($this->role_id),
                now()->addHour(), // TTL
                fn () => $this->getRoleWithPermissionsFromDatabase()
            );
        } catch (Throwable) {
            return $this->getRoleWithPermissionsFromDatabase();
        }
    }

    public function getCachedPermissions(): array
    {
        $role = $this->getCachedRoleWithPermissions();

        if ($role === null) {
            return [];
        }

        return $role
            ->rolePermissions()
            ->where('allowed', true)
            ->with(['module', 'permission'])
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->module->code.':'.$item->permission->code => true,
                ];
            })
            ->toArray();
    }

    protected function getRoleWithPermissionsFromDatabase(): ?Role
    {
        return Role::query()
            ->with(['rolePermissions.module', 'rolePermissions.permission'])
            ->find($this->role_id);
    }

    public function hasPermission(string $module, string $permission): bool
    {
        // Dios absoluto
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Solo super_admin puede manejar permisos de roles
        if ($module === 'roles' && in_array($permission, ['create', 'assign_permissions'], true)) {
            return false;
        }

        // Developer: todo menos gestión de permisos de roles
        if ($this->hasRole('developer')) {
            return true;
        }

        // Admin: todo menos assign_permissions
        if ($this->hasRole('admin') && $permission !== 'assign_permissions') {
            return true;
        }

        // Usuario normal → cache
        $permissions = $this->getCachedPermissions();

        return isset($permissions["{$module}:{$permission}"]);
    }
}
