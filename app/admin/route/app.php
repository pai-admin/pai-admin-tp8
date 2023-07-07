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

// 登录相关
Route::get("get-code","index/getCode");
Route::post("login","index/login");
Route::get("info","index/info");
Route::get("auth","index/auth");
Route::post("logout","index/logout");
Route::post("editPwd","index/editPwd");
Route::post("info/edit","index/editInfo");
Route::get("log/my","index/myLog");
Route::post("upload/file","index/upload");

// 日志操作
Route::get("log/list","log/list");
Route::delete("log/del","log/del");

// 字典管理
Route::get("dict_type/list","DictType/list");
Route::delete("dict_type/del","DictType/del");
Route::post("dict_type/add","DictType/add");
Route::put("dict_type/edit","DictType/edit");

Route::get("dict_data/list","DictData/list");
Route::delete("dict_data/del","DictData/del");
Route::post("dict_data/add","DictData/add");
Route::put("dict_data/edit","DictData/edit");

// 部门管理
Route::get("dept/list","dept/list");
Route::delete("dept/del","dept/del");
Route::post("dept/add","dept/add");
Route::put("dept/edit","dept/edit");

// 菜单管理
Route::get("menu/list","menu/list");
Route::delete("menu/del","menu/del");
Route::post("menu/add","menu/add");
Route::put("menu/edit","menu/edit");

// 角色管理
Route::get("role/list","role/list");
Route::delete("role/del","role/del");
Route::post("role/add","role/add");
Route::put("role/edit","role/edit");

// 账号管理
Route::get("account/list","account/list");
Route::delete("account/del","account/del");
Route::post("account/add","account/add");
Route::put("account/edit","account/edit");
