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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');
Route::get('product', 'ProductController@product');

Route::get('product', 'ProductController@product')->middleware('jwt.verify');
Route::get('product-owner', 'ProductController@productOwner')->middleware('jwt.verify');


Route::get('user', 'UserController@getAuthenticatedUser')->middleware('jwt.verify');
Route::get('userprofile', 'UserController@getProfileUser')->middleware('jwt.verify');
Route::get('user/{id}', 'UserController@show');
Route::put('user/update/{id}', 'UserController@update')->middleware('jwt.verify');
Route::delete('user/delete/{id}', 'UserController@destroy')->middleware('jwt.verify');

Route::get('test', 'UserController@fetchCategory');


Route::get('logout', 'UserController@logout')->middleware('jwt.verify');