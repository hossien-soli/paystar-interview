<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

Route::controller(MainController::class)->group(function () {
    Route::get('/','home')->name('main.home');
    Route::post('/new-order','newOrder');
    Route::post('/callback','callback')->name('main.callback');
});
