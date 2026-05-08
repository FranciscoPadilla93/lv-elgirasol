<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\School\ExpedienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\School\ExpedienteDocumentoController;

Route::post('/v1/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware(['access.token.cookie', 'auth:sanctum', 'active.user'])
    ->prefix('v1')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [SessionController::class, 'me']);

        // USUARIOS
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users,read');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->middleware('permission:users,read');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users,create');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users,update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users,delete');

        Route::post('/users/{user}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users,update');

        // ROLES
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

        // EXPEDIENTES (ALUMNOS)
        Route::get('/expedientes', [ExpedienteController::class, 'index'])
            ->middleware('permission:expedientes,read');

        Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'show'])
            ->middleware('permission:expedientes,read');

        Route::post('/expedientes', [ExpedienteController::class, 'store'])
            ->middleware('permission:expedientes,create');

        Route::put('/expedientes/{expediente}', [ExpedienteController::class, 'update'])
            ->middleware('permission:expedientes,update');

        Route::delete('/expedientes/{expediente}', [ExpedienteController::class, 'destroy'])
            ->middleware('permission:expedientes,delete');

        Route::post('/expedientes/{id}/restore', [ExpedienteController::class, 'restore'])
            ->middleware('permission:expedientes,update');

        // EXPEDIENTE DOCUMENTOS
        Route::get('/expediente-documentos', [ExpedienteDocumentoController::class, 'index'])
            ->middleware('permission:expedientes,read');

        Route::post('/expediente-documentos', [ExpedienteDocumentoController::class, 'store'])
            ->middleware('permission:expedientes,create');

        Route::delete('/expediente-documentos/{expedienteDocumento}', [ExpedienteDocumentoController::class, 'destroy'])
            ->middleware('permission:expedientes,delete');

        Route::get('/expediente-documentos/{expedienteDocumento}/download', [ExpedienteDocumentoController::class, 'download'])
            ->middleware('permission:expedientes,read');

            Route::post('/expediente-documentos/{expedienteDocumento}/validate', [ExpedienteDocumentoController::class, 'validateDocument'])
            ->middleware('permission:expedientes,update');
    });
