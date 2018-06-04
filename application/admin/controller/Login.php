<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29
 * Time: 16:52
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class Login extends Controller
{
   public function index(){
       $request=Request::instance();
       if($request->isPost()){
           $input = $request->param();
           $input['password']=md5($input['password']);
           if(Db::name('adminuser')->where($input)->find()){
               Session::set('username',$input['username']);
               return json(['code'=>0,'msg'=>'登陆成功！']);
           }else{
               return json(['code'=>1,'msg'=>'账号或密码错误！']);
           }
       }
       $this->view->engine->layout(false);
       return $this->fetch('login');
   }
}