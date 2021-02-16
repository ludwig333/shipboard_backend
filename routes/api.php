<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\FolderController;
use App\Http\Controllers\API\v1\BotController;
use App\Http\Controllers\API\v1\FlowController;
use App\Http\Controllers\API\v1\MessageController;
use App\Http\Controllers\API\v1\ContentController;
use App\Http\Controllers\API\v1\TextController;
use App\Http\Controllers\API\v1\ImageController;
use App\Http\Controllers\API\v1\CardController;
use App\Http\Controllers\API\v1\ButtonController;
use App\Http\Controllers\Bot\TelegramBotController;
use App\Http\Controllers\Bot\SlackBotController;
use App\Http\Controllers\Bot\MessengerBotController;
use Illuminate\Support\Facades\File;

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

Route::get('img', function (\Illuminate\Http\Request $request) {
  $path = $request->input('path');
  if($path) {
      $path = storage_path('app/public/'. $path);
      if (!File::exists($path)) {
          abort(404);
      }
      $file = File::get($path);
      $type = File::mimeType($path);
      $response = response()->make($file, 200);
      $response->header("Content-Type", $type);
      return $response;
  }
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgot'])->name('forgot');
    Route::post('reset-password', [AuthController::class, 'reset'])->name('reset');
//    Route::get('storage/{filename}', function ($filename)
//    {
//        $path = storage_path('public/' . $filename);
//
//        if (!File::exists($path)) {
//            abort(404);
//        }
//
//        $file = File::get($path);
//        $type = File::mimeType($path);
//
//        $response = response()->make($file, 200);
//        $response->header("Content-Type", $type);
//
//        return $response;
//    });
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('user', [AuthController::class, 'userInfo'])->name('auth-user');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        /** Bot API Endpoints Start */
        Route::get('configure-bot/{bot}', [BotController::class, 'getConfigurations']);
        Route::post('configure-bot/{bot}', [BotController::class, 'updateConfiguration'])->name('bots.configure');
        Route::apiResource('bots', BotController::class);
        /** Bot API Endpoints End */

        /** Folder API Endpoints Start */
        Route::apiResource('folders', FolderController::class);
        /** Folder API Endpoints End */

        /** Flow API Endpoints Start */
        Route::post('flow/publish/{flow}', [FlowController::class, 'publish']);
        Route::apiResource('flows', FlowController::class);
        /** Flow API Endpoints End */

        /** Message API Endpoints Start */
        Route::post('message/connect-flow/{message}', [MessageController::class, 'connectFlow']);
        Route::post('message/create-and-connect/{message}', [MessageController::class, 'createAndConnect']);
        Route::apiResource('messages', MessageController::class);
        /** Message API Endpoints End */

        /** Content API Endpoints Start */
        Route::apiResource('contents', ContentController::class);
        /** Content API Endpoints End */

        /** Text API Endpoints Start */
        Route::apiResource('texts', TextController::class);
        /** Text API Endpoints End */

        /** Image API Endpoints Start */
        Route::post('/image/upload-image/{image}', [ImageController::class, 'uploadImage']);
        Route::apiResource('images', ImageController::class);
        /** Image API Endpoints End */

        /** Cards API Endpoints Start */
        Route::post('/card/{group}', [CardController::class, 'addCard']);
        Route::put('/card/{card}', [CardController::class, 'updateCard']);
        Route::delete('/card/{card}', [CardController::class, 'deleteCard']);
        Route::post('/card/upload-image/{card}', [CardController::class, 'uploadImage']);
        Route::post('/card-groups', [CardController::class,'createCardGroup']);
        Route::delete('/card-groups/{groups}', [CardController::class, 'destroyCardGroup']);
        /** Cards API Endpoints End */

        /** Button API Endpoints Start */
        Route::post('button/connect-flow/{button}', [ButtonController::class, 'connectFlow']);
        Route::post('button/create-and-connect/{button}', [ButtonController::class, 'createAndConnect']);
        Route::post('button', [ButtonController::class, 'store']);
        Route::put('button/{button}', [ButtonController::class, 'update']);
        Route::delete('button/{button}', [ButtonController::class, 'destroy']);
        /** Button API Endpoints End */
    });

    /**
     * Bot endpoints
     */
    Route::match(['get', 'post'], '/telegram/{id}',[TelegramBotController::class,'reply']);
    Route::match(['get', 'post'], '/messenger/{id}',[MessengerBotController::class,'reply']);
    Route::match(['get', 'post'], '/slack/{id}',[SlackBotController::class,'reply']);

});


