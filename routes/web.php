<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Temporal para probar el envio de correos
use Illuminate\Support\Facades\Mail;

Route::get('/mail-test', function () {

    Mail::raw('Correo de prueba desde Laravel', function ($message) {

        $message->to('themascotaxd23@gmail.com')
                ->subject('Prueba SMTP');

    });

    return 'Correo enviado';
});