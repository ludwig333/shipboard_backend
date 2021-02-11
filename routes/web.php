<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::redirect('/', '/api/v1/docs');

//Route::group(['namespace' => 'Bot', 'as' => ':bot'], function () {
//    /**
//     * Bots Instance
//     */
//    Route::match(['get', 'post'], '/telegram/{id}', 'TelegramBotController');
//    Route::match(['get', 'post'], '/facebook/{id}', 'FacebookBotController');
//    Route::match(['get', 'post'], '/slack/{id}', 'SlackBotController');
//    Route::match(['get', 'post'], '/{id}', 'BotChannelController');
//});

Route::match(['get', 'post'], '/telegram/{id}', [\App\Http\Controllers\Bot\TelegramBotController::class]);
