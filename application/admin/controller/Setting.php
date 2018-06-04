<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/3
 * Time: 17:42
 */

namespace app\admin\controller;


use think\Db;
use think\Request;

class Setting extends Admin
{
    public function index(){
        if(Request::instance()->isPost()){
            $input=Request::instance()->param();
            $data=[];
            foreach ($input as $key=>$item){
                $data[$key]=strtotime($item);
            }
            if(Db::name('setting')->where('id',1)->update($data)){
                return json(['code'=>0,'msg'=>'添加成功！']);
            }else{
                return json(['code'=>0,'msg'=>'添加失败！请稍后再试']);
            }
        }else{
            $info=Db::name('setting')->where('id',1)->find();
            $this->assign('data','setting');
            return $this->fetch('index',['info'=>$info]);
        }

    }
}