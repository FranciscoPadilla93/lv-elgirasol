<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class TipoEvaluacion extends Model
{
    protected $table = 'cat_tipos_evaluacion';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function evaluacionesIniciales(): HasMany
    {
        return $this->hasMany(EvaluacionInicial::class);
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
