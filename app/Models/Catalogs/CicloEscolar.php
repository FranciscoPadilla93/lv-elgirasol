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
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
