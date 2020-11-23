<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;

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

Route::get('/refunds/{year}/{limit?}', [Controller::class, 'get_refunds'])
    ->where(['year' => '[0-9]+', 'limit' => '[0-9]+']);

Route::get('/social-ranking/{limit?}', [Controller::class, 'get_social_ranking'])
    ->where(['limit' => '[0-9]+']);
