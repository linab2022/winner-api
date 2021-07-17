<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'API\AuthController@register');
Route::post('login', 'API\AuthController@login');
Route::post('changeAdminPassword', 'API\AuthController@changeAdminPassword')->middleware('auth:api');
Route::post('showUsers', 'API\AuthController@showUsers')->middleware('auth:api');
Route::post('deleteUser/{id}', 'API\AuthController@deleteUser')->middleware('auth:api');

Route::post('addParticipant', 'API\ParticipantController@addParticipant')->middleware('auth:api');
Route::post('addImage', 'API\ParticipantController@addImage')->middleware('auth:api');
Route::post('deleteParticipant/{id}', 'API\ParticipantController@deleteParticipant')->middleware('auth:api');
Route::post('isWinner/{id}', 'API\ParticipantController@isWinner')->middleware('auth:api');
Route::post('isNotWinner/{id}', 'API\ParticipantController@isNotWinner')->middleware('auth:api');
Route::post('showAllWinners', 'API\ParticipantController@showAllWinners')->middleware('auth:api');
Route::post('showAllParticipants', 'API\ParticipantController@showAllParticipants')->middleware('auth:api');
Route::post('deleteAllParticipants', 'API\ParticipantController@deleteAllParticipants')->middleware('auth:api');

