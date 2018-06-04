<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::rule([
   //admin
    'login' => 'admin/Login/index',
    'admin' =>'admin/Index/index',
    'admin/update' =>'admin/Index/update',
    'ad/team' =>'admin/Team/index',
    'ad/team/create' =>'admin/Team/create',
    'tan' =>'admin/Team/tan',
    'ad/setting' =>'admin/Setting/index',
    'ad/game' =>'admin/Game/index',
    'game/create' =>'admin/Game/create',
    'game/add' =>'admin/Game/add',
    'game/del' =>'admin/Game/del',
    'game/edit' =>'admin/Game/edit',
    'game/find' =>'admin/Game/find',
    'user/guess'=>'admin/User/guess',
    'user/forecast'=>'admin/User/forecast',
    'user/deal'=>'admin/User/deal',
    'user/prize'=>'admin/User/prize',
    'user/apply'=>'admin/User/apply',
    'user/del'=>'admin/User/del',
    'user/edit'=>'admin/User/edit',
    'user/tan'=>'admin/User/tan',
    'user/upload'=>'admin/User/upload',
    'user/uploadone'=>'admin/User/uploadone',
   //index
    '/'   =>'index/Index/index',
    'info'  =>'index/Index/info',
    'reg'   =>'index/Index/reg',
    'user'   =>'index/Index/user',
    'forecast' =>'index/Index/forecast',
    'guess' =>'index/Index/guess',
    'rule' =>'index/Index/rule',
    'image' =>'index/Index/image_captcha'

]);

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['admin/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['admin/hello', ['method' => 'post']],
    ],

];
