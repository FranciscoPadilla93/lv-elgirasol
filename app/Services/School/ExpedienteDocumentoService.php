<?php

namespace App\Services\School;

use App\Models\School\ExpedienteDocumento;
use App\Repositories\Storage\UploadFileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ExpedienteDocumentoService
{
    public function __construct(
        private UploadFileRepository $uploadFileRepository
    ) {}

    public function create(array $data): ExpedienteDocumento {
        return DB::transaction(function () use ($data) {
            $archivo = $data['archivo'];

            // SUBIR ARCHIVO
            $filePath = $this->uploadFileRepository
                ->uploadFileStoragePrivate('expedientes/documentos',$archivo);

            // GUARDAR BD
            $documento = ExpedienteDocumento::create([
                // RELACIONES
                'expediente_id' => $data['expediente_id'],
                'tipo_documento_id' => $data['tipo_documento_id'],
                // ARCHIVO
                'original_name' => $archivo->getClientOriginalName(),
                'file_name' => basename($filePath),
                'file_path' => $filePath,
                'mime_type' => $archivo->getMimeType(),
                'extension' => $archivo->extension(),
                'size_bytes' => $archivo->getSize(),
                // VALIDACIÓN
                'validation_notes' => $data['validation_notes'] ?? null,
                // STATUS
                'status' => true,
                // AUDITORÍA
                'created_by' => auth()->id(),
            ]);

            return $documento->load([
                'tipoDocumento',
            ]);
        });
    }

    public function delete(ExpedienteDocumento $documento): void {
        DB::transaction(function () use ($documento) {
            // ELIMINAR ARCHIVO
            if ($documento->file_path) {
                if ($this->uploadFileRepository->getTipoAlmacenamiento() == 'ftp') {
                    $this->uploadFileRepository
                        ->deleteExistingFileFtp($documento->file_path,'/private');
                } else {
                    $this->uploadFileRepository
                        ->deleteExistingFile($documento->file_path, 'local');
                }
            }

            $documento->delete();
        });
    }

    public function validateDocument(ExpedienteDocumento $documento,array $data): ExpedienteDocumento {
        return DB::transaction(
            function () use ($documento, $data) {
                $documento->update([
                    // VALIDACIÓN
                    'is_validated' => $data['is_validated'],
                    'validated_by' => auth()->id(),
                    'validated_at' => now(),
                    'validation_notes' =>
                        $data['validation_notes']
                        ?? null,
                    // AUDITORÍA
                    'updated_by' => auth()->id(),
                ]);

                return $documento->load([
                    'tipoDocumento',
                ]);
            }
        );
    }
}
