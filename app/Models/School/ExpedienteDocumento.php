<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Catalogs\TipoDocumento;
use App\Models\User;

class ExpedienteDocumento extends Model
{
    use SoftDeletes;

    protected $table = 'expediente_documentos';

    protected $fillable = [
        'expediente_id',
        'tipo_documento_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'extension',
        'size_bytes',
        'is_validated',
        'validated_by',
        'validated_at',
        'validation_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_validated' => 'boolean',
            'status' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ACCESSORS
    public function getHumanSizeAttribute(): string
    {
        if (!$this->size_bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];

        $bytes = $this->size_bytes;

        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf(
            "%.2f %s",
            $bytes / pow(1024, $factor),
            $units[$factor]
        );
    }
}
