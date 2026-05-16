<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Concepto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conceptos';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function ciclosEscolares(): HasMany
    {
        return $this->hasMany(ConceptoCicloEscolar::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
