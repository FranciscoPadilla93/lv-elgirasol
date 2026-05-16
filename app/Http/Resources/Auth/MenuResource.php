<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'path' => $this->path,
            'icon' => $this->icon,
            'order' => $this->order,
            'status' => $this->status,
            'module' => $this->whenLoaded('module', function () {
                return [
                    'id' => $this->module?->id,
                    'code' => $this->module?->code,
                    'name' => $this->module?->name,
                ];
            }),
            'children' => MenuResource::collection(
                $this->relationLoaded('children')
                    ? $this->children
                    : collect()
            ),
        ];
    }
}
