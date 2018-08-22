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

//登录即可欧

//登录接口
Route::post('wechat/auth', 'WechatMpUsersController@auth');
//商品列表接口
Route::get('products', 'ProductsController@index');
//商品详情接口
Route::get('products/{product}', 'ProductsController@show')->name('products.show');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//类目选项
Route::get('attrs/vals', 'AttrsController@options');
Route::get('attrs/categoryvals', 'AttrsController@optinosfromcate');
