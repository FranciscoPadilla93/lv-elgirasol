<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Menu extends Model
{
    use HasActivityLogOptions, LogsActivity;

    protected $table = 'menu';

    protected $fillable = [
        'module_id',
        'parent_id',
        'label',
        'path',
        'icon',
        'order',
        'status',
    ];

    protected $casts = [
        'module_id' => 'integer',
        'parent_id' => 'integer',
        'order' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('menu', [
            'module_id',
            'parent_id',
            'label',
            'path',
            'icon',
            'order',
            'status',
        ]);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
