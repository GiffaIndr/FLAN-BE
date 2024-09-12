<?php

use App\Http\Controllers\API\ExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HouseController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SpotifyController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//auth
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

//google & spotify
Route::middleware('web')->group( function () {
    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('auth/spotify/callback', [SpotifyController::class, 'handleSpotifyCallback']);
    Route::get('auth/spotify', [SpotifyController::class, 'redirectToSpotify']);
});

//log
Route::get('/test', function () {
    return response()->json(['message' => 'API is working'], 200);
});

Route::middleware('auth:api')->group(function ()  {
    Route::get('/user', [AuthController::class, 'userInfo']);
    Route::get('/logout', [AuthController::class, 'logout']);
    //house
    Route::get('/house/get', [HouseController::class, 'getHouse']);
    Route::post('/house/store', [HouseController::class, 'store']);
    Route::patch('/house/update/{id}', [HouseController::class, 'update']);
    Route::delete('/house/delete/{id}', [HouseController::class, 'delete']);

    //profile
    Route::patch('/profile/photo/{id}', [UserController::class,'photo_profile']);
    Route::patch('/profile/detail/{id}', [UserController::class,'detail_profile_update']);
    
    //user/admin
    Route::get('/user/get', [AuthController::class, 'user']);
    Route::post('/user/store', [UserController::class, 'store']);
    Route::delete('/user/delete', [UserController::class, 'delete']);


    //activity
    Route::get('/activity/get', [ActivityController::class, 'activity']);
    Route::post('/activity/store', [ActivityController::class, 'store']);
    Route::patch('/activity/update/{id}', [ActivityController::class, 'update']);
    Route::delete('/activity/delete/{id}', [ActivityController::class,'delete']);

    //spotify
    Route::get('/spotify/playlists', [SpotifyController::class, 'getSpotifyPlaylists']);

    //export 
    Route::get('/export/user', [ExportController::class, 'export_user']);
    Route::get('/export/activity', [ExportController::class,'export_activity']);
    Route::get('/export/house', [ExportController::class, 'export_house']);
});