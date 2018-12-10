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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//上传
Route::any('api/upload','Api\api@upload');
Route::any('api/content_upload','Api\api@content_upload');
//选择会议
Route::any('api/choiseinfo','Api\api@choiseinfo');
Route::any('api/choiseinfos','Api\api@choiseinfos');
//管理员模块
Route::any('user/login','Api\user@login');
Route::any('user/userdesc','Api\user@userdesc');
Route::any('user/list','Api\user@userlist');
Route::any('user/edit','Api\user@edit');
Route::any('user/add','Api\user@add');
Route::any('user/del','Api\user@del');
Route::any('user/password','Api\user@password');
//权限模块
Route::any('auth/menu','Api\Auth@menu');
Route::any('auth/rule','Api\Auth@rule');
Route::any('auth/groupdel','Api\Auth@groupdel');
Route::any('auth/groupmenu','Api\Auth@groupmenu');
Route::any('auth/groupedit','Api\Auth@groupedit');
Route::any('auth/groupadd','Api\Auth@groupadd');
//管理员日志模块
Route::any('adminlog/list','Api\adminlog@list');

//菜单管理
Route::any('rule/list','Api\Rule@list');
Route::any('rule/del','Api\Rule@del');
Route::any('rule/edit','Api\Rule@edit');
Route::any('rule/add','Api\Rule@add');

//参会人员管理
Route::any('people/list','Api\People@list');
Route::any('people/get_ticket','Api\People@get_ticket');
Route::any('people/get_type','Api\People@get_type');
Route::any('people/get_info','Api\People@get_info');
Route::any('people/add','Api\People@add');
Route::any('people/edit','Api\People@edit');
Route::any('people/del','Api\People@del');

//会议管理
Route::any('info/list','Api\Info@list');
Route::any('info/get_type','Api\Info@get_type');
Route::any('info/add','Api\Info@add');
Route::any('info/edit','Api\Info@edit');
Route::any('info/del','Api\Info@del');

//嘉宾管理
Route::any('guest/list','Api\Guest@list');
Route::any('guest/get_type','Api\Guest@get_type');
Route::any('guest/add','Api\Guest@add');
Route::any('guest/edit','Api\Guest@edit');
Route::any('guest/del','Api\Guest@del');
Route::any('guest/get_info_guest','Api\Guest@get_info_guest');
Route::any('guest/add_info','Api\Guest@add_info');
Route::any('guest/del_info','Api\Guest@del_info');

//日程管理
Route::any('agenda/list','Api\Agenda@list');
Route::any('agenda/add','Api\Agenda@add');
Route::any('agenda/edit','Api\Agenda@edit');
Route::any('agenda/del','Api\Agenda@del');