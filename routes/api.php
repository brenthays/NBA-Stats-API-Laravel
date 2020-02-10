<?php

use Illuminate\Http\Request;

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

Route::resource('conference', 'ConferenceController')->only(['index']);
Route::resource('division', 'DivisionController')->only(['index']);
Route::resource('game', 'GameController')->only(['index']);
Route::resource('player', 'PlayerController')->only(['index']);
Route::resource('season', 'SeasonController')->only(['index']);
Route::resource('team', 'TeamController')->only(['index']);
