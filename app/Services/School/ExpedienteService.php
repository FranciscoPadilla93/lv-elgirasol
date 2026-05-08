<?php

namespace App\Services\School;

use App\Models\School\Expediente;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Repositories\Storage\UploadFileRepository;
use App\Services\Media\ImageOptimizationService;

class ExpedienteService
{
    public function __construct(
        private UploadFileRepository $uploadFileRepository,
        private ImageOptimizationService $imageOptimizationService
    ) {}

    public function create(array $data): Expediente
    {
        return DB::transaction(function () use ($data) {
            // FOTO
            if (isset($data['foto'])) {
                $optimized = $this->imageOptimizationService
                    ->optimize($data['foto']);

                $fotoPath = $this->uploadFileRepository->putOptimizedContent('expedientes/fotos', $optimized['file_name'], $optimized['content']);

                $data['foto_path'] = $fotoPath;
            }

            unset($data['foto']);
            // FOLIO
            $data['folio'] = $this->generateFolio();
            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $expediente = Expediente::create($data);

            return $expediente->load([
                'genero',
                'estadoExpediente',
            ]);
        });
    }

    public function update(Expediente $expediente,array $data): Expediente {
        return DB::transaction(function () use ($expediente, $data) {
            // FOTO
            if (isset($data['foto'])) {
                // ELIMINAR ANTERIOR
                if ($expediente->foto_path) {
                    if ($this->uploadFileRepository->getTipoAlmacenamiento() == 'ftp') {
                        $this->uploadFileRepository->deleteExistingFileFtp($expediente->foto_path,'/private');
                    } else {
                        $this->uploadFileRepository->deleteExistingFile($expediente->foto_path,'local');
                    }
                }

                // OPTIMIZAR
                $optimized =$this->imageOptimizationService->optimize($data['foto']);

                // GUARDAR
                $fotoPath = $this->uploadFileRepository
                                ->putOptimizedContent('expedientes/fotos', $optimized['file_name'], $optimized['content']);

                $data['foto_path'] = $fotoPath;
            }

            unset($data['foto']);
            // AUDITORÍA
            $data['updated_by'] = auth()->id();

            $expediente->update($data);
            $expediente->refresh();

            return $expediente->load([
                'genero',
                'estadoExpediente',
            ]);
        });
    }

    public function delete(Expediente $expediente): void
    {
        DB::transaction(function () use ($expediente) {
            // ELIMINAR FOTO
            if ($expediente->foto_path) {
                if ($this->uploadFileRepository->getTipoAlmacenamiento() == 'ftp') {
                    $this->uploadFileRepository->deleteExistingFileFtp($expediente->foto_path, '/private');
                } else {
                    $this->uploadFileRepository->deleteExistingFile($expediente->foto_path, 'local');
                }
            }

            $expediente->delete();
        });
    }

    public function restore(int $id): ?Expediente
    {
        return DB::transaction(function () use ($id) {
            $expediente = Expediente::withTrashed()->find($id);

            if (!$expediente || !$expediente->trashed()) {
                return null;
            }

            $expediente->restore();

            return $expediente->load([
                'genero',
                'estadoExpediente',
            ]);
        });
    }

    private function generateFolio(): string
    {
        $year = now()->format('Y');
        $lastId = Expediente::withTrashed()->max('id') + 1;

        return "EXP-{$year}-" .
            str_pad($lastId, 6, '0', STR_PAD_LEFT);
    }
}
