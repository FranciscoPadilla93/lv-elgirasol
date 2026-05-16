<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\StoreIntranetUserRequest;
use App\Http\Requests\School\UpdateIntranetUserRequest;
use App\Http\Resources\School\IntranetUserResource;
use App\Models\School\IntranetUser;
use App\Services\School\IntranetUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntranetUserController extends Controller
{
    public function __construct(
        private IntranetUserService $intranetUserService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $sortBy = $request->string('sort_by')->toString() ?: 'id';
        $sortDirection = $request->string('sort_direction')->toString() ?: 'desc';

        $intranetUsers = IntranetUser::query()
            ->with([
                'createdBy',
                'updatedBy',
            ])
            // SEARCH
            ->when($search !== '', function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->orWhere('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('curp', 'LIKE', "%{$search}%");
            })
            // FILTRO STATUS
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'status',
                        filter_var(
                            $request->status,
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
            // ORDENAMIENTO
            ->orderBy($sortBy, $sortDirection)
            // PAGINACIÓN
            ->paginate($perPage);

        return ResponseHelper::success(
            IntranetUserResource::collection($intranetUsers),
            'Usuarios de intranet obtenidos correctamente.'
        );
    }

    public function show(IntranetUser $intranetUser): JsonResponse {
        $intranetUser->load([
            'createdBy',
            'updatedBy',
        ]);

        return ResponseHelper::success(
            new IntranetUserResource($intranetUser),
            'Usuario de intranet obtenido correctamente.'
        );
    }

    public function store(StoreIntranetUserRequest $request): JsonResponse
    {
        $intranetUser = $this->intranetUserService->create($request->validated());

        return ResponseHelper::success(
            new IntranetUserResource($intranetUser),
            'Usuario de intranet creado correctamente.',
            201
        );
    }

    public function update(UpdateIntranetUserRequest $request, IntranetUser $intranetUser): JsonResponse
    {
        $intranetUser = $this->intranetUserService
            ->update(
                $intranetUser,
                $request->validated()
            );

        return ResponseHelper::success(
            new IntranetUserResource($intranetUser),
            'Usuario de intranet actualizado correctamente.'
        );
    }

    public function destroy(IntranetUser $intranetUser): JsonResponse
    {
        $this->intranetUserService->delete($intranetUser);

        return ResponseHelper::success(
            null,
            'Usuario de intranet eliminado correctamente.'
        );
    }

    public function restore(int $id): JsonResponse
    {
        $intranetUser = $this->intranetUserService->restore($id);

        if (!$intranetUser) {
            return ResponseHelper::error(
                'Usuario de intranet no encontrado o no eliminado.',
                404
            );
        }

        return ResponseHelper::success(
            new IntranetUserResource($intranetUser),
            'Usuario de intranet restaurado correctamente.'
        );
    }
}
