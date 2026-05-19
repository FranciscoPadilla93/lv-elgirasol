<?php

use App\Models\Catalogs\Estado;
use App\Models\Catalogs\Genero;
use App\Models\Catalogs\CatGrupoSanguineo;
use App\Models\Catalogs\CatTipoSeguroMedico;
use App\Models\Catalogs\TipoDocumento;
use App\Models\Catalogs\Parentesco;
use App\Models\Catalogs\TipoContacto;
use App\Models\Catalogs\EstadoExpediente;
use App\Models\Catalogs\EstadoInscripcion;
use App\Models\Catalogs\Nivel;
use App\Models\Catalogs\Grado;
use App\Models\Catalogs\CicloEscolar;
use App\Models\Catalogs\TipoEvaluacion;
use App\Models\School\Concepto;

return [
    'conceptos' => [
        'model' => Concepto::class,
        'label' => 'Conceptos Ciclos Escolares',
        'order_by' => 'name',
    ],
    'estados' => [
        'model' => Estado::class,
        'label' => 'Estados',
        'order_by' => 'name',
    ],
    'generos' => [
        'model' => Genero::class,
        'label' => 'Géneros',
        'order_by' => 'name',
    ],

    'grupos_sanguineos' => [
        'model' => CatGrupoSanguineo::class,
        'label' => 'Grupos sanguíneos',
        'order_by' => 'name',
    ],

    'tipos_seguro_medico' => [
        'model' => CatTipoSeguroMedico::class,
        'label' => 'Tipos de seguro médico',
        'order_by' => 'name',
    ],

    'tipos_documento' => [
        'model' => TipoDocumento::class,
        'label' => 'Tipos de documento',
        'order_by' => 'name',
    ],

    'parentescos' => [
        'model' => Parentesco::class,
        'label' => 'Parentescos',
        'order_by' => 'name',
    ],

    'tipos_contacto' => [
        'model' => TipoContacto::class,
        'label' => 'Tipos de contacto',
        'order_by' => 'name',
    ],

    'estados_expediente' => [
        'model' => EstadoExpediente::class,
        'label' => 'Estados de expediente',
        'order_by' => 'id',
    ],

    'estados_inscripcion' => [
        'model' => EstadoInscripcion::class,
        'label' => 'Estados de inscripción',
        'order_by' => 'id',
    ],

    'niveles' => [
        'model' => Nivel::class,
        'label' => 'Niveles',
        'order_by' => 'id',
    ],

    'grados' => [
        'model' => Grado::class,
        'label' => 'Grados',
        'order_by' => 'id',
    ],

    'ciclos_escolares' => [
        'model' => CicloEscolar::class,
        'label' => 'Ciclos escolares',
        'order_by' => 'name',
    ],

    'tipos_evaluacion' => [
        'model' => TipoEvaluacion::class,
        'label' => 'Evaluaciones',
        'order_by' => 'name',
    ],
];
