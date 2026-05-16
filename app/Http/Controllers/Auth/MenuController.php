<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\MenuResource;
use App\Services\Auth\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        private MenuService $menuService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $menus = $this->menuService
            ->getMenusForUser($request->user());

        return ResponseHelper::success(
            MenuResource::collection($menus),
            'Menús obtenidos correctamente.'
        );
    }
}
