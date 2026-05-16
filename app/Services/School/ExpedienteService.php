<?php

namespace App\Services\School;

use App\Models\School\Expediente;
use Illuminate\Support\Facades\DB;
use App\Repositories\Storage\UploadFileRepository;
use App\Services\Media\ImageOptimizationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Catalogs\EstadoExpediente;

class ExpedienteService
{
    public function __construct(
        private UploadFileRepository $uploadFileRepository,
        private ImageOptimizationService $imageOptimizationService
    ) {}

    private function relations(): array
    {
        return [
            'estado',
            'genero',
            'estadoExpediente',
            'grupoSanguineo',
            'tipoSeguroMedico',
        ];
    }


    public function paginate(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $perPage = $filters['per_page'] ?? 15;
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        return Expediente::query()->with($this->relations())
            // SEARCH
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('folio', 'LIKE', "%{$search}%")
                        ->orWhere('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellido_materno', 'LIKE', "%{$search}%")
                        ->orWhere('curp', 'LIKE', "%{$search}%")
                        ->orWhereHas('estado', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('code', 'LIKE', "%{$search}%");
                        });
                });
            })

            // FILTROS
            ->when(!empty($filters['estado_id']), function ($query) use ($filters) {
                $query->where('estado_id', $filters['estado_id']);
            })

            ->when(!empty($filters['estado_expediente_id']), function ($query) use ($filters) {
                $query->where('estado_expediente_id', $filters['estado_expediente_id']);
            })

            ->when(!empty($filters['genero_id']), function ($query) use ($filters) {
                $query->where('genero_id', $filters['genero_id']);
            })

            ->when(!empty($filters['grupo_sanguineo_id']), function ($query) use ($filters) {
                $query->where('grupo_sanguineo_id', $filters['grupo_sanguineo_id']);
            })

            ->when(!empty($filters['tipo_seguro_medico_id']), function ($query) use ($filters) {
                $query->where('tipo_seguro_medico_id', $filters['tipo_seguro_medico_id']);
            })

            // ORDENAMIENTO
            ->orderBy($sortBy, $sortDirection)

            // PAGINACIÓN
            ->paginate($perPage);
    }

    public function find(Expediente $expediente): Expediente
    {
        return $expediente->load([
            ...$this->relations(),
            'documentos.tipoDocumento',
            'contactos.parentesco',
            'inscripciones',
            'tutores.tutor',
        ]);
    }

    public function create(array $data): Expediente
    {
        return DB::transaction(function () use ($data) {
            // FOTO
            if (!empty($data['foto'])) {
                $optimized = $this->imageOptimizationService
                    ->optimize($data['foto']);

                $fotoPath = $this->uploadFileRepository->putOptimizedContent('expedientes/fotos', $optimized['file_name'], $optimized['content']);

                $data['foto_path'] = $fotoPath;
            }

            unset($data['foto']);
            // FOLIO
            $data['folio'] = $this->generateFolio();

            // ESTATUS EN PROSPECTO POR DEFECTO
            $estadoProspectoId = EstadoExpediente::query()
                ->where('code', 'prospect')
                ->where('status', true)
                ->value('id');

            if (! $estadoProspectoId) {
                throw new \Exception('No se encontró el estado inicial Prospecto.');
            }

            $data['estado_expediente_id'] = $estadoProspectoId;

            // AUDITORÍA
            $data['created_by'] = auth()->id();
            $expediente = Expediente::create($data);

            return $expediente->load($this->relations());
        });
    }

    public function update(Expediente $expediente, array $data): Expediente
    {
        return DB::transaction(function () use ($expediente, $data) {
            // FOTO
            if (!empty($data['foto'])) {
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

            return $expediente->load($this->relations());
        });
    }

    public function delete(Expediente $expediente): void
    {
         DB::transaction(function () use ($expediente) {
            $expediente->update([
                'updated_by' => auth()->id(),
            ]);

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

            $expediente->update([
                'updated_by' => auth()->id(),
            ]);

            return $expediente->load($this->relations());
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
