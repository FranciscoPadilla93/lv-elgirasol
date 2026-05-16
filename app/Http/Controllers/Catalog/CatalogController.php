<?php

namespace App\Http\Controllers\Catalog;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CatalogController extends Controller
{
    public function __construct(
        private CatalogService $catalogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $only = null;

        if ($request->filled('only')) {
            $only = collect(explode(',', $request->string('only')->toString()))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->toArray();
        }

        $catalogs = $this->catalogService->getAll($only);

        return ResponseHelper::success(
            $catalogs,
            'Catálogos obtenidos correctamente.'
        );
    }

    public function show(string $catalog): JsonResponse
    {
        try {
            $data = $this->catalogService->getOne($catalog);

            return ResponseHelper::success(
                $data,
                'Catálogo obtenido correctamente.'
            );
        } catch (InvalidArgumentException $exception) {
            return ResponseHelper::error(
                $exception->getMessage(),
                404
            );
        }
    }
}
