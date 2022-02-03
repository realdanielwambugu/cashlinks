<?php

use Xcholars\Support\Proxies\Route;

use Xcholars\Support\Proxies\RouteGroup as Group;

use Xcholars\Http\Request;

use Xcholars\Http\Response;

// Route::view('/', 'home');
//
// Route::get('/home', 'HomeController@show');

Group::middleware('auth')->members(function ()
{
    //signup
    Route::view('/signup', 'auth/signup');

    Route::post('/signup', 'SignupController@create');

    //signin
    Route::view('/signin', 'auth/signin');

    Route::post('/signin', 'LoginController@authenticate');

    //forgotPassword
    Route::view('/forgot_password', 'auth/forgot_password');

    Route::post('/forgot_password', 'forgotPasswordController@verify');

    //resetCode
    Route::view('/confirm_reset_code', 'auth/confirm_reset_code');

    Route::post('/confirm_reset_code', 'ResetPasswordController@verifyResetCode');

    //resetPassword
    Route::view('/reset_password', 'auth/reset_password');

    Route::post('/reset_password', 'ResetPasswordController@reset');
});

Route::get('/', 'ThreadController@show');

Route::get('/next_page/{last_id}/{rand}', 'ThreadController@show');

Route::get('/comment/{thread_id}', 'CommentController@show');

Route::post('/click', 'ClickController@create');

Route::view('/privacy', 'privacy');

Route::view('/terms', 'terms');

Group::middleware('guest')->members(function ()
{
    // thread
    Route::view('create_thread', 'create_thread');

    Route::view('create_thread_custom83736', 'create_thread_cook');

    Route::post('create_thread', 'ThreadController@create');

    Route::get('/delete_thread/{thread_id}', 'ThreadController@delete');

    // comment
    Route::view('create_comment', 'create_comment');

    Route::post('/comment', 'CommentController@create');

    Route::post('/delete_comment/{comment_id}', 'CommentController@delete');

    // reply
    Route::view('create_reply', 'create_reply');

    Route::post('/reply', 'ReplyController@create');

    Route::post('/delete_reply/{reply_id}', 'ReplyController@delete');


    // profile
    Route::get('/profile', 'ProfileController@show');

    Route::post('/profile_update', 'ProfileController@update');

    // sign Out
    Route::get('/logout', 'LogoutController@logout');

    Route::post('/logout', 'LogoutController@logout');
});

Route::fallback(function (Response $response)
{
    return $response->withView('404');
});
