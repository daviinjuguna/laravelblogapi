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

//user
Route::post('login','AuthController@login');
Route::post('register','AuthController@register');
Route::post('refresh','AuthController@refresh');

//user reset password
Route::post('recover_password','ResetPasswordController@recoverPassword');
Route::post('change_password','ResetPasswordController@changePassword');

Route::middleware('auth:api')->group( function () {
    //user,Authenticated user
    Route::post('logout', 'AuthController@logout');
    Route::post('save_user_info','AuthController@saveUserInfo');

    //post
    Route::post('posts/create','PostController@create');
    Route::post('posts/delete','PostController@delete');
    Route::post('posts/update','PostController@update');
    Route::get('posts','PostController@posts');
    Route::get('posts/my_posts','PostController@myPosts');

    //comment
    Route::post('comments/create','CommentController@create');
    Route::post('comments/delete','CommentController@delete');
    Route::post('comments/update','CommentController@update');
    Route::post('posts/comments','CommentController@comments');

    //like
    Route::post('posts/like','LikeController@like');
});
