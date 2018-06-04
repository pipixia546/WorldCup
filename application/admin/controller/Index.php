<?php
namespace app\admin\controller;

use think\Db;
use think\Request;
use think\Session;

class Index extends Admin
{
    public function index(){
        $this->assign( 'data','index');
        $username=Session::get('username');
        $list=Db::name('adminuser')->where('id',1)->find();
        return $this->fetch('index',['user$this->assign( \'data\',\'index\');'=>$username,'list'=>$list]);
    }
    public function  update(){
        $this->assign( 'data','setting');
      $request=Request::instance();
      if ($request->isPost()){
          $password=$request->param('password');
          $input['password']=md5($password);
          if (Db::name('adminuser')->where('id',1)->update($input)){
              Session::delete('username');
              return json(['code'=>0,'msg'=>'修改成功！']);
          }else{
              return json(['code'=>1,'msg'=>'修改失败，请稍后再试~~！']);
          }

      }else{
          return $this->fetch();

      }


    }
}
