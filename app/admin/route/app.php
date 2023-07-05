<?php
/**
 * Author: cfn <cfn@leapy.cn>
 */

use think\exception\HttpResponseException;
use think\facade\Route;
use utils\Tools;

Route::miss(function (){
    throw new HttpResponseException(\think\Response::create(['code' => Tools::CODE_FAIL, 'msg' => "路由不存在"], 'json'));
});

Route::get("get-code","index/getCode");
Route::post("login","index/login");
Route::get("info","index/info");
Route::get("auth","index/auth");
Route::post("logout","index/logout");

Route::post("editPwd","index/editPwd");
Route::post("info/edit","index/editInfo");
Route::get("log/my","index/myLog");

Route::get("log/list","log/list");
Route::delete("log/del","log/del");













