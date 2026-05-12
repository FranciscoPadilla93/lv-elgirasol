<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\School\ExpedienteController;
use App\Http\Controllers\School\ExpedienteDocumentoController;
use App\Http\Controllers\School\ExpedienteContactoController;
use App\Http\Controllers\School\TutorController;
use App\Http\Controllers\School\ExpedienteTutorController;
use App\Http\Controllers\School\InscripcionController;
use App\Http\Controllers\School\EvaluacionInicialController;
use App\Http\Controllers\School\EstudioSocioeconomicoController;

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

        // EXPEDIENTE CONTACTOS
        Route::get('/expedientes/{expediente}/contactos', [ExpedienteContactoController::class, 'index'])
            ->middleware('permission:expedientes,read');

        Route::post('/expedientes/{expediente}/contactos', [ExpedienteContactoController::class, 'store'])
            ->middleware('permission:expedientes,create');

        Route::get('/expedientes/{expediente}/contactos/{contacto}', [ExpedienteContactoController::class, 'show'])
            ->middleware('permission:expedientes,read');

        Route::put('/expedientes/contactos/{contacto}', [ExpedienteContactoController::class, 'update'])
            ->middleware('permission:expedientes,update');

        Route::delete('/expedientes/contactos/{contacto}', [ExpedienteContactoController::class, 'destroy'])
            ->middleware('permission:expedientes,delete');

        Route::post('/expedientes/contactos/{id}/restore', [ExpedienteContactoController::class, 'restore'])
            ->middleware('permission:expedientes,update');

        // TUTORES
        Route::get('/tutores', [TutorController::class, 'index'])
            ->middleware('permission:tutores,read');

        Route::get('/tutores/{tutor}', [TutorController::class, 'show'])
            ->middleware('permission:tutores,read');

        Route::post('/tutores', [TutorController::class, 'store'])
            ->middleware('permission:tutores,create');

        Route::put('/tutores/{tutor}', [TutorController::class, 'update'])
            ->middleware('permission:tutores,update');

        Route::delete('/tutores/{tutor}', [TutorController::class, 'destroy'])
            ->middleware('permission:tutores,delete');

        Route::post('/tutores/{id}/restore', [TutorController::class, 'restore'])
            ->middleware('permission:tutores,update');

        // EXPEDIENTE TUTORES
        Route::get('/expediente-tutores', [ExpedienteTutorController::class, 'index'])
            ->middleware('permission:expedientes,read');

        Route::get('/expediente-tutores/{expedienteTutor}', [ExpedienteTutorController::class, 'show'])
            ->middleware('permission:expedientes,read');

        Route::post('/expediente-tutores', [ExpedienteTutorController::class, 'store'])
            ->middleware('permission:expedientes,create');

        Route::put('/expediente-tutores/{expedienteTutor}', [ExpedienteTutorController::class, 'update'])
            ->middleware('permission:expedientes,update');

        Route::delete('/expediente-tutores/{expedienteTutor}', [ExpedienteTutorController::class, 'destroy'])
            ->middleware('permission:expedientes,delete');

        Route::post('/expediente-tutores/{id}/restore', [ExpedienteTutorController::class, 'restore'])
            ->middleware('permission:expedientes,update');

        // INSCRIPCIONES
        Route::get('/inscripciones', [InscripcionController::class, 'index'])
            ->middleware('permission:inscripciones,read');

        Route::get('/inscripciones/{inscripcion}', [InscripcionController::class, 'show'])
            ->middleware('permission:inscripciones,read');

        Route::post('/inscripciones', [InscripcionController::class, 'store'])
            ->middleware('permission:inscripciones,create');

        Route::put('/inscripciones/{inscripcion}', [InscripcionController::class, 'update'])
            ->middleware('permission:inscripciones,update');

        Route::delete('/inscripciones/{inscripcion}', [InscripcionController::class, 'destroy'])
            ->middleware('permission:inscripciones,delete');

        Route::post('/inscripciones/{id}/restore', [InscripcionController::class, 'restore'])
            ->middleware('permission:inscripciones,update');

        // EVALUACIONES INICIALES
        Route::get('/evaluaciones-iniciales', [EvaluacionInicialController::class, 'index'])
            ->middleware('permission:evaluaciones,read');

        Route::get('/evaluaciones-iniciales/{evaluacionInicial}', [EvaluacionInicialController::class, 'show'])
            ->middleware('permission:evaluaciones,read');

        Route::post('/evaluaciones-iniciales', [EvaluacionInicialController::class, 'store'])
            ->middleware('permission:evaluaciones,create');

        Route::put('/evaluaciones-iniciales/{evaluacionInicial}', [EvaluacionInicialController::class, 'update'])
            ->middleware('permission:evaluaciones,update');

        Route::delete('/evaluaciones-iniciales/{evaluacionInicial}', [EvaluacionInicialController::class, 'destroy'])
            ->middleware('permission:evaluaciones,delete');

        Route::post('/evaluaciones-iniciales/{id}/restore', [EvaluacionInicialController::class, 'restore'])
            ->middleware('permission:evaluaciones,update');

        // ESTUDIOS SOCIOECONOMICOS
        Route::get('/estudios-socioeconomicos', [EstudioSocioeconomicoController::class, 'index'])
            ->middleware('permission:estudios_socioeconomicos,read');

        Route::get('/estudios-socioeconomicos/{estudioSocioeconomico}', [EstudioSocioeconomicoController::class, 'show'])
            ->middleware('permission:estudios_socioeconomicos,read');

        Route::post('/estudios-socioeconomicos', [EstudioSocioeconomicoController::class, 'store'])
            ->middleware('permission:estudios_socioeconomicos,create');

        Route::put('/estudios-socioeconomicos/{estudioSocioeconomico}', [EstudioSocioeconomicoController::class, 'update'])
            ->middleware('permission:estudios_socioeconomicos,update');

        Route::delete('/estudios-socioeconomicos/{estudioSocioeconomico}', [EstudioSocioeconomicoController::class, 'destroy'])
            ->middleware('permission:estudios_socioeconomicos,delete');

        Route::post('/estudios-socioeconomicos/{id}/restore', [EstudioSocioeconomicoController::class, 'restore'])
            ->middleware('permission:estudios_socioeconomicos,update');
    });
