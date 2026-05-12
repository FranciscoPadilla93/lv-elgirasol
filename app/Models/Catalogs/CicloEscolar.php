<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CicloEscolar extends Model
{
    use SoftDeletes;

    protected $table = 'ciclos_escolares';

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
}
