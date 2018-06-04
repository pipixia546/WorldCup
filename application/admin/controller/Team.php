<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/30
 * Time: 15:24
 */

namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Session;

class Team extends Admin
{
    public function index(){
        $request= Request::instance();
        if($request->isPost()){
            $input=$request->param();
            if($input['type']==1){
                $list=Db::name('team')->where('team_group',$input['team_group'])->paginate(8);
            }else{
                $list=Db::name('team')->where('best_type','<=',$input['best_type'])->paginate(8);
            }
        }else{
            $list=Db::name('team')->paginate(8);
        }
        $this->assign('data','team');
        return $this->fetch('list',['list'=>$list]);
    }
    public function create(){
        $request= Request::instance();
        if($request->isPost()){
            $info=$request->post();
            $file=$request->file('team_logo');
            if(empty($info['team_name'])||empty($info['team_group'])) return json(['code'=>1,'msg'=>'请输入球队名称']);
            if(empty($file)) return json(['code'=>1,'msg'=>'请选择上传图片']);
            $data = $file->move(ROOT_PATH . 'public' . DS . 'logo');
            if($data){
                $info['team_logo']='logo/'. $data->getSaveName();
                if(Db::name('team')->insert($info)){
                    return json(['code'=>0,'msg'=>'球队添加成功！']);
                }else{
                    return json(['code'=>1,'msg'=>'球队添加失败！请稍后再试~~']);
                }
            }else{
                $msg=$file->getError();
                return json(['code'=>1,'msg'=>$msg]);
            }

        }else{
            $this->assign('data','team');
            return $this->fetch('create');
        }

    }
    public function tan(){
        $this->view->engine->layout(false);
        $request=Request::instance();
        if($request->isPost()){
            $input=$request->param();
            $data['edit_user']=Session::get('username');
            $data['edit_time']=time();
            $num=Db::name('team')->where('best_type','<=',$input['best_type'])->count();
            if($num==$input['best_type']){
                return json(['code'=>1,'msg'=>'你所分配的排名已满！']);
            }else{
                $data['best_type']=$input['best_type'];
                if(Db::name('team')->where('id',$input['id'])->update($data)){
                    return json(['code'=>0,'msg'=>'分配成功！']);
                }else{
                    return json(['code'=>1,'msg'=>'分配失败，请稍后再试！']) ;
                }
            }
        }else{
            $id=$request->param();
            $list=Db::name('setting')->where('id',1)->find();

            $time=time();
           // var_dump($list);var_dump($time);die();
            return $this->fetch('tan' ,['id'=>$id,'list'=>$list,'time'=>$time]);
        }


    }

}