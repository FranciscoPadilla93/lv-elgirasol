<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grado extends Model
{
    use SoftDeletes;

    protected $table = 'cat_grados';

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }
}
