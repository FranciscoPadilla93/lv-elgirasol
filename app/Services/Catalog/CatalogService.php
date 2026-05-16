<?php

namespace App\Services\Catalog;

use App\Http\Resources\Catalog\CatalogItemResource;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CatalogService
{
    public function getAll(?array $only = null): array
    {
        $catalogs = config('catalogs');

        if ($only) {
            $catalogs = collect($catalogs)
                ->only($only)
                ->toArray();
        }

        $response = [];

        foreach ($catalogs as $key => $config) {
            $response[$key] = [
                'label' => $config['label'],
                'items' => CatalogItemResource::collection(
                    $this->queryCatalog($config)
                ),
            ];
        }

        return $response;
    }

    public function getOne(string $catalog): array
    {
        $catalogs = config('catalogs');

        if (! array_key_exists($catalog, $catalogs)) {
            throw new InvalidArgumentException('Catálogo no permitido o no existente.');
        }

        $config = $catalogs[$catalog];

        return [
            'key' => $catalog,
            'label' => $config['label'],
            'items' => CatalogItemResource::collection(
                $this->queryCatalog($config)
            ),
        ];
    }

    private function queryCatalog(array $config): Collection
    {
        $model = $config['model'];
        $orderBy = $config['order_by'] ?? 'name';

        $query = $model::query();

        if ($this->hasColumn($model, 'status')) {
            $query->where('status', true);
        }

        return $query
            ->orderBy($orderBy)
            ->get();
    }

    private function hasColumn(string $model, string $column): bool
    {
        return in_array(
            $column,
            (new $model)->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing((new $model)->getTable())
        );
    }
}
