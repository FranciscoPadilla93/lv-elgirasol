<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Catalogs\Parentesco;
use App\Models\User;
use App\Models\School\Expediente;
use App\Models\School\Tutor;

class ExpedienteTutor extends Model
{
    use SoftDeletes;

    protected $table = 'expediente_tutores';

    protected $fillable = [
        'expediente_id',
        'tutor_id',
        'parentesco_id',
        'is_primary_contact',
        'is_financial_responsible',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'expediente_id' => 'integer',
            'tutor_id' => 'integer',
            'parentesco_id' => 'integer',
            'is_primary_contact' => 'boolean',
            'is_financial_responsible' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // RELACIONES
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    public function parentesco(): BelongsTo
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePrimaryContact($query)
    {
        return $query->where('is_primary_contact', true);
    }

    public function scopeFinancialResponsible($query)
    {
        return $query->where('is_financial_responsible', true);
    }
}
