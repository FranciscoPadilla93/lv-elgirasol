<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\School\Concepto;
use App\Models\Catalogs\CicloEscolar;

class ConceptoCicloEscolar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conceptos_ciclos_escolares';

    protected $fillable = [
        'concepto_id',
        'ciclo_escolar_id',
        'price',
        'start_date',
        'due_date',
        'has_late_fee',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'start_date' => 'date:Y-m-d',
            'due_date' => 'date:Y-m-d',
            'has_late_fee' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(Concepto::class);
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
