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
//normal password changing
Route::GET('auth/changePass',[
	'middleware' => 'jwt-auth',
	'as' => 'changePass',
	'uses' => 'Auth\AuthenticateController@changePass'
]);



Route::GET('/updateToilet/{token}/{id}/{active}',[
	'as' => 'updateToilet',
	'uses' => 'AdminController@updateToilet'
]);

// Route::GET('/deactivateToilet/{token}',[
// 	'as' => 'deactivateToilet',
// 	'uses' => 'AdminController@deactivateToilet'
// ]);
/*Route::POST('auth/getAuthenticatedUser',[
	'middleware' => 'jwt-auth',
	'as' => 'getAuthenticatedUser',
	'uses' => 'Auth\AuthenticateController@getAuthenticatedUser'
]);
*/


Route::GET('/giveGenFeedback',[
	'as' => 'giveGenFeedback',
	'uses' => 'ToiletController@giveGenFeedback'
]);
Route::get('/showUser',[
	'middleware' => 'jwt-auth',
	'as' => 'showUser',
	'uses' => 'UserController@show'
]);
/**************************Toilets******************************/
// Route::POST('requestReg/regToilet',[
// 	// 'middleware' => 'jwt-auth',
// 	'as' => 'Regrequest',
// 	'uses' => 'ToiletController@create'
// ]);

Route::POST('requestReg/regToilet',[
	// 'middleware' => 'jwt-auth',
	'as' => 'Regrequest',
	'uses' => 'ToiletController@requestReg'
]);

Route::POST('register/addToilet',[
	// 'middleware' => 'jwt-auth',
	'as' => 'addToilet',
	'uses' => 'ToiletController@requestReg'
]);


Route::POST('/editFacilities',[
	// 'middleware' => 'jwt-auth',
	'as' => 'editFacilities',
	'uses' => 'ToiletController@edit'
]);


Route::POST('/editTimeOpen',[
	// 'middleware' => 'jwt-auth',
	'as' => 'editTime',
	'uses' => 'ToiletController@editTimeOpen'
]);

Route::POST('/editTimeClose',[
	// 'middleware' => 'jwt-auth',
	'as' => 'editTime',
	'uses' => 'ToiletController@editTimeClose'
]);



Route::GET('/showToilets',[
	//'middleware' => 'jwt-auth',
	'as' => 'showToilets',
	'uses' => 'ToiletController@show'
]);

Route::GET('/toiletSOS',[
	//'middleware' => 'jwt-auth',
	'as' => 'toiletSOS',
	'uses' => 'ToiletController@showSOS'
]);

Route::GET('/showToiletsIIT',[
	//'middleware' => 'jwt-auth',
	'as' => 'showToiletsIIT',
	'uses' => 'ToiletController@showOld'
]);

Route::POST('/addFeedback',[
	// 'middleware' => 'jwt-auth',
	'as' => 'addFeedback',
	'uses' => 'ToiletController@addFeedback'
]);
Route::GET('/showVisits',[
	'middleware' => 'jwt-auth',
	'as' => 'showVisits',
	'uses' => 'ToiletController@showVisitHistory'
]);
Route::POST('/addHistory',[
	'middleware' => 'jwt-auth',
	'as' => 'addHistory',
	'uses' => 'ToiletController@addHistory'
]);
Route::GET('/showSpcfcToiletsIIT',[
	//'middleware' => 'jwt-auth',
	'as' => 'showSpcfcToiletsIIT',
	'uses' => 'ToiletController@showSpecificToiletOld'
]);
Route::GET('/showSpcfcToilets',[
	//'middleware' => 'jwt-auth',
	'as' => 'showSpcfcToilets',
	'uses' => 'ToiletController@showSpecificToilet'
]);
//for reporting toilet issue
Route::POST('/reportIssue',[
	// 'middleware' => 'jwt-auth',
	'as' => 'reportIssue',
	'uses' => 'ToiletController@reportIssue'
]);
//image upload for toilets
Route::POST('/uploadToiletImage',[
	// 'middleware' => 'jwt-auth',
	'as' => 'upldToiletImage',
	'uses' => 'ToiletController@upload'
]);
Route::POST('/showToltImages',[
	'middleware' => 'jwt-auth',
	'as' => 'showToltImages',
	'uses' => 'ToiletController@showImages'
]);

Route::GET('/toiletstats',[
	'as' => 'toiletstats',
	'uses' => 'ToiletController@toiletstats'
]);




Route::POST('/requestClean',[
	'as' => 'requestClean',
	'uses' => 'ToiletController@requestClean'
]);

Route::POST('/addComment',[
	// 'middleware' => 'jwt-auth',
	'as' => 'addComment',
	'uses' => 'ToiletController@addComment'
]);

Route::GET('/getComment',[
	// 'middleware' => 'jwt-auth',
	'as' => 'getComment',
	'uses' => 'ToiletController@getComment'
]);

Route::GET('/toiletActive',[
	// 'middleware' => 'jwt-auth',
	'as' => 'toiletActive',
	'uses' => 'ToiletController@toiletActive'
]);



