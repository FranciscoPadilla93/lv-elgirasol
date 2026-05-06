<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RolePermission extends Model
{
    use HasActivityLogOptions, LogsActivity;

    protected $fillable = [
        'role_id',
        'module_id',
        'permission_id',
        'allowed',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'allowed' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('role_permissions', [
            'role_id',
            'module_id',
            'permission_id',
            'allowed',
        ]);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
