<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);

        $menu = Menu::query()
            ->with(['module', 'parent', 'children'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('label', 'like', "%{$search}%")
                        ->orWhere('path', 'like', "%{$search}%")
                        ->orWhere('icon', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('module_id'), function ($query) use ($request) {
                $query->where('module_id', $request->integer('module_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->orderBy('order')
            ->orderBy('label')
            ->paginate($perPage);

        return ResponseHelper::success(
            MenuResource::collection($menu),
            'Menu obtenido correctamente.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $menu = Menu::create([
            'module_id' => $data['module_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'path' => $data['path'] ?? null,
            'icon' => $data['icon'] ?? null,
            'order' => $data['order'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        return ResponseHelper::success(
            new MenuResource($menu->load(['module', 'parent', 'children'])),
            'Menu creado correctamente.',
            201
        );
    }

    public function show(Menu $menu): JsonResponse
    {
        return ResponseHelper::success(
            new MenuResource($menu->load(['module', 'parent', 'children'])),
            'Menu obtenido correctamente.'
        );
    }

    public function update(Request $request, Menu $menu): JsonResponse
    {
        $data = $request->validate($this->rules($menu));

        $menu->update($data);

        return ResponseHelper::success(
            new MenuResource($menu->load(['module', 'parent', 'children'])),
            'Menu actualizado correctamente.'
        );
    }

    public function destroy(Menu $menu): JsonResponse
    {
        $menu->delete();

        return ResponseHelper::success(
            null,
            'Menu eliminado correctamente.'
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(?Menu $menu = null): array
    {
        return [
            'module_id' => ['required', 'integer', 'exists:modules,id'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:menu,id',
                Rule::notIn([$menu?->id]),
            ],
            'label' => ['required', 'string', 'max:255'],
            'path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255'],
        ];
    }
}
