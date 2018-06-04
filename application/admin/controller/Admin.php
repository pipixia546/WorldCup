<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29
 * Time: 13:04
 */

namespace app\admin\controller;
use think\Controller;
use think\Config;
use think\Request;
use think\Session;
class Admin extends Controller{
    protected function _initialize(){
        $ip=Config::get('Ip');
        $request = Request::instance()->ip();
        if (!in_array($request,$ip)){
              echo '<html><head><title>Yout site</title></head><body><div class="container container-sm"><div id="icon-header"><span class="fa fa-question-circle"></span></div><div id="text-column"><header class="secondary-header"><h1>Error 404: <strong>Page not found.</strong></h1><p>This is not the page you\'re looking for.</p></header><nav class="secondary-nav"><ul><li><a href="#" class="btn btn-primary">Take me home<span class="fa fa-exclamation"></span></a></li></ul></nav></div></div></body></html>';
              exit;
        }else{
              $user=Session::get('username');
              if (!$user){
                  $this->error('尚未登录','admin/login/index');
              }
        }
    }
}