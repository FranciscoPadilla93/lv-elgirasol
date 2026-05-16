<?php

return [
    'type' => env(
        'TIPO_ALMACENAMIENTO',
        'local'
    ),
    'ftp' => [
        'host' => env('FTP_HOST'),
        'port' => (int) env('FTP_PORT', 21),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),
        'root' => env('FTP_ROOT', '/private'),
        'passive' => env('FTP_PASSIVE', true),
    ],
];
