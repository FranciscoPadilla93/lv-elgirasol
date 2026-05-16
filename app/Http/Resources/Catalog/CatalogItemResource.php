<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code ?? null,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'status' => $this->status ?? null,
        ];
    }
}
