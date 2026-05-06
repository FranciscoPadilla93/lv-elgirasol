<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Inventario extends Model
{
    use HasActivityLogOptions, LogsActivity, SoftDeletes;

    protected $table = 'inventarios';

    protected $fillable = [
        'sku',
        'producto',
        'cantidad',
        'precio_unitario',
        'ubicacion',
        'estatus',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('inventario', [
            'sku',
            'producto',
            'cantidad',
            'precio_unitario',
            'ubicacion',
            'estatus',
        ]);
    }
}
