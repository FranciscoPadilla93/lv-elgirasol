<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\School\Expediente;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use SoftDeletes;

    protected $table = 'cat_estados';

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean'
        ];
    }

    // RELACIONES
    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
