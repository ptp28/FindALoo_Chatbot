<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/*****************User**************************/
Route::get('/', function () {
    return view('welcome');
});

Route::POST('register/addUser',[
	'as' => 'addUser',
	'uses' => 'UserController@create'
]);
Route::any('/login', [
	'as' => 'login',
	'uses' => 'Auth\AuthenticateController@login'
	/*function(){
		return view('register/login');
	}*/
]);
Route::POST('auth/user_login',[
	'as' => 'user_login',
	'uses' => 'Auth\AuthenticateController@authenticate'
]);
Route::GET('/logout',[
	'middleware' => 'jwt-refresh',
	'as' => 'logout',
	'uses' => 'Auth\AuthenticateController@logout'
]);

Route::GET('auth/forgotPass',[
	'as' => 'forgotPass',
	'uses' => 'Auth\AuthenticateController@forgotpassProcess'
]);

Route::GET('auth/verify_pass/{username}/{token}',[
	'as' => 'verifyPassToken',
	'uses' => 'Auth\AuthenticateController@verifyPassToken'
]);

Route::GET('/verifyAccount/{token}',[
	'as' => 'verifyEmail',
	'uses' => 'UserController@verify_account'
]);

/*Route::POST('auth/getAuthenticatedUser',[
	'middleware' => 'jwt-auth',
	'as' => 'getAuthenticatedUser',
	'uses' => 'Auth\AuthenticateController@getAuthenticatedUser'
]);
*/

Route::get('/showUser',[
	'middleware' => 'jwt-auth',
	'as' => 'showUser',
	'uses' => 'UserController@show'
]);
/**************************Toilets******************************/
Route::POST('register/addToilet',[
	'middleware' => 'jwt-auth',
	'as' => 'addToilet',
	'uses' => 'ToiletController@create'
]);

Route::GET('/showToilets',[
	//'middleware' => 'jwt-auth',
	'as' => 'showToilets',
	'uses' => 'ToiletController@show'
]);

Route::GET('/addFeedback',[
	'middleware' => 'jwt-auth',
	'as' => 'addFeedback',
	'uses' => 'ToiletController@addFeedback'
]);
Route::GET('/showSpcfcToilets',[
	//'middleware' => 'jwt-auth',
	'as' => 'showSpcfcToilets',
	'uses' => 'ToiletController@showSpecificToilet'
]);
//image upload for toilets
Route::POST('/upldToiletImage',[
	'middleware' => 'jwt-auth',
	'as' => 'upldToiletImage',
	'uses' => 'ToiletController@upload'
]);
Route::POST('/showToltImages',[
	'middleware' => 'jwt-auth',
	'as' => 'showToltImages',
	'uses' => 'ToiletController@showImages'
]);