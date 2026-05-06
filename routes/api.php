<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

// Route::post('/v1/login', [AuthController::class, 'login']);

Route::post('/v1/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware(['access.token.cookie', 'auth:sanctum', 'active.user'])
    ->prefix('v1')
    ->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [SessionController::class, 'me']);

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users,read');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users,create');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users,update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users,delete');

        Route::post('/users/{user}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users,update');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles,read');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles,create');

        Route::post('/users/{user}/role', [RoleController::class, 'assignToUser'])
            ->middleware('permission:roles,assign_permissions');

        Route::post('/modules', [ModuleController::class, 'store'])
            ->middleware('permission:modules,create');

        Route::apiResource('menu', MenuController::class)
            ->middleware('developer.only');

        Route::post('/roles/{role}/permissions/sync', [RolePermissionController::class, 'sync'])
            ->middleware('permission:roles,assign_permissions');

        Route::get('/ventas', [VentaController::class, 'index'])
            ->middleware('permission:ventas,read');

        Route::post('/ventas', [VentaController::class, 'store'])
            ->middleware('permission:ventas,create');

        Route::get('/ventas/{venta}', [VentaController::class, 'show'])
            ->middleware('permission:ventas,read');

        Route::put('/ventas/{venta}', [VentaController::class, 'update'])
            ->middleware('permission:ventas,update');

        Route::delete('/ventas/{venta}', [VentaController::class, 'destroy'])
            ->middleware('permission:ventas,delete');

        Route::get('/inventario', [InventarioController::class, 'index'])
            ->middleware('permission:inventario,read');

        Route::post('/inventario', [InventarioController::class, 'store'])
            ->middleware('permission:inventario,create');

        Route::get('/inventario/{inventario}', [InventarioController::class, 'show'])
            ->middleware('permission:inventario,read');

        Route::put('/inventario/{inventario}', [InventarioController::class, 'update'])
            ->middleware('permission:inventario,update');

        Route::delete('/inventario/{inventario}', [InventarioController::class, 'destroy'])
            ->middleware('permission:inventario,delete');

    });
