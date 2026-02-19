<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/encrypt', 'pages::encrypt')->name('encrypt');
