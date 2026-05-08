<?php

return [
    'type' => env(
        'TIPO_ALMACENAMIENTO',
        'local'
    ),

    'ftp' => [
        'host' => env('FTP_HOST'),
        'port' => env('FTP_PORT', 21),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),
    ],
];
