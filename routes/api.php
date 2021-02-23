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

//login 

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');

Route::group(['middleware' => 'jwt.verify'], static function( $router){

//Client controller
Route::post('client/create','ClientController@create');
Route::get('client/edit/{id}','ClientController@edit');
Route::put('client/update/{id}','ClientController@update');
Route::delete('client/delete/{id}','ClientController@delete');
Route::get('client/list','ClientController@list');
Route::get('client/all','ClientController@full_list');
Route::get('client/search','ClientController@search');
Route::get('client/total/list','ClientController@totalrecord');


// Portfolio Routes
Route::post('portfolio/create','PortfolioController@create');
Route::get('portfolio/edit/{id}','PortfolioController@edit');
Route::put('portfolio/update/{id}','PortfolioController@update');
Route::get('exposure','PortfolioController@exposure');
Route::get('region','PortfolioController@regionlist');
Route::get('costofborrow','PortfolioController@rcostofborrowlist');
Route::put('assignedto/{id}','PortfolioController@assignedto');


// Portfolio Details
Route::get('risksummary','ClientController@risksummary');
Route::get('performancesummary','ClientController@performancesummary');
Route::get('assetsummary','ClientController@assetsummary');
Route::get('assetmanagement','ClientController@assetmanagement');
Route::get('excelstatus/{id}/{status}','ClientController@excelstatus');


// Asset Management
Route::post('asset/create','AssetController@create');
Route::get('asset/list','AssetController@list');
Route::delete('asset/delete/{id}','AssetController@delete');
Route::post('asset/bulkupload', 'AssetController@bulkupload');


Route::post('logout', 'UserController@logout');
Route::post('refresh', 'UserController@refresh');
Route::get('detail', 'UserController@detail');
Route::post('verifyToken','UserController@verifyToken');

// Rm Management

Route::post('rm/create','RmController@create');
Route::get('rm/list','RmController@list');
Route::get('rm/edit/{id}','RmController@edit');
Route::post('rm/update/{id}','RmController@update');
Route::delete('rm/delete/{id}','RmController@delete');
Route::get('user/list','RmController@userlist');
Route::post('rm/change/password/{id}','RmController@change_password');

//Export
Route::get('export/excel/{id}','ClientController@exportexcel');

//Client Shift
Route::put('client/moveto/{id}','ClientController@moveto');

// Truncate
Route::get('table/truncate','ExcelController@delete');

// Admin Variables
Route::get('admin/variable/list','AdminvariableController@list');
Route::post('admin/variable/store','AdminvariableController@store');

//Benchmarks

Route::get('benchmark/data','BenchmarkController@benchmark_data');
Route::get('benchmark/list','BenchmarkController@list');
Route::post('benchmark/store','BenchmarkController@store');
Route::get('benchmark/status','BenchmarkController@status');


 });

