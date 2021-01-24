<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\FolderController;
use App\Http\Controllers\API\v1\BotController;
use App\Models\Flow;
use App\Models\Message;
use App\Models\Content;
use App\Models\Text;
use App\Http\Controllers\API\v1\FlowController;
use App\Http\Controllers\API\v1\MessageController;
use App\Http\Controllers\API\v1\ContentController;
use App\Http\Controllers\API\v1\TextController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group(['prefix' => 'v1'], function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('reset-password', [AuthController::class, 'reset'])->name('reset');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user', [AuthController::class, 'userInfo'])->name('auth-user');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        /** Bot API Endpoints Start */
        Route::apiResource('bots', BotController::class);
        /** Bot API Endpoints End */

        /** Folder API Endpoints Start */
        Route::apiResource('folders', FolderController::class);
        /** Folder API Endpoints End */

        /** Flow API Endpoints Start */
        Route::apiResource('flows', FlowController::class);
        /** Flow API Endpoints End */

        /** Message API Endpoints Start */
        Route::apiResource('messages', MessageController::class);
        /** Message API Endpoints End */

        /** Content API Endpoints Start */
        Route::apiResource('contents', ContentController::class);
        /** Content API Endpoints End */

        /** Text API Endpoints Start */
        Route::apiResource('texts', TextController::class);
        /** Text API Endpoints End */

    });
});


