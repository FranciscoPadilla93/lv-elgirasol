<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Venta extends Model
{
    use HasActivityLogOptions, LogsActivity, SoftDeletes;

    protected $fillable = [
        'folio',
        'cliente',
        'fecha_venta',
        'total',
        'estatus',
        'notas',
    ];

    protected $casts = [
        'fecha_venta' => 'date:Y-m-d',
        'total' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return $this->activityLogOptions('ventas', [
            'folio',
            'cliente',
            'fecha_venta',
            'total',
            'estatus',
            'notas',
        ]);
    }
}
