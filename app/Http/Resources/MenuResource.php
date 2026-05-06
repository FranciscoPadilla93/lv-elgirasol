<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'parent_id' => $this->parent_id,
            'label' => $this->label,
            'path' => $this->path,
            'icon' => $this->icon,
            'order' => $this->order,
            'status' => $this->status,
            'module' => $this->whenLoaded('module'),
            'parent' => $this->whenLoaded('parent', fn () => new self($this->parent)),
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
