<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/4
 * Time: 11:19
 */

namespace app\admin\controller;


use think\Db;
use think\Request;
use think\Session;
use think\Validate;
class Game extends Admin
{
    protected $rule = [
        'game_time'  =>  'require',
        'game_type' =>  'require',
        'team_A'  =>  'require',
        'team_B' =>  'require',
    ];

    protected $message = [
        'game_time'  =>  '请选择比赛日期',
        'game_type' =>  '请选择比赛类型',
        'team_A'  =>  '请选择队伍1',
        'team_B' =>  '请选择队伍2',
    ];
    public function index(){
        $request= Request::instance();
        if($request->isPost()) {

            $input = $request->param();

            if ($input['type'] == 1) {
                $list = Db::name('game')->where('team_A', $input['team_name'])->whereOr('team_B',$input['team_name'])->order('game_time','asc')->paginate(8);
            } else {
                $list = Db::name('game')->where('game_type', $input['game_type'])->order('game_time','asc')->paginate(8);
            }
        }else{
            $list=Db::name('game')->order('game_time','asc')->paginate(8);
        }
        $team=Db::name('team')->field('id,team_name')->select();
        $this->assign('data','game');
        return $this->fetch('list',['team'=>$team,'list'=>$list]);
    }
    public function create(){
        $request=Request::instance();
        if($request->isPost()){
            $input=$request->param();
            if(is_numeric($input['game_type'])){
                $info=Db::name('team')->field('id,team_name,team_logo')->where('best_type','<=',$input['game_type'])->select();
                }else{
                $info=Db::name('team')->field('id,team_name,team_logo')->where('team_group',$input['game_type'])->select();
            };
            if($info){
                return json(['code'=>0,'info'=>$info]) ;
            }else{
                return json(['code'=>1,'msg'=>'获取名单失败！']);
            }

        }else{
            $this->assign('data','game');
            return $this->fetch('create',[]);
        }

    }
    public function add(){
        $input=Request::instance()->param();
        $validate = new Validate($this->rule, $this->message);
        if(!$validate->check($input)){
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
        if(Db::name('game')->where($input)->find()){
            return json(['code'=>1,'msg'=>'此次比赛已存在']);
        }else{
            $input['addtime']=time();
            $input['game_time']=strtotime($input['game_time']);
            if(Db::name('game')->insert($input)){
                return json(['code'=>0,'msg'=>'比赛添加成功！']);
            }else{
                return json(['code'=>0,'msg'=>'比赛添加失败！请稍后再试']);
            }
        }
    }
    public function del(){
        $id=Request::instance()->param();
        if(Db::name('game')->where($id)->delete()){
            return json(['code'=>0,'msg'=>'删除成功！']);
        }else{
            return json(['code'=>1,'msg'=>'删除失败！']);
        }
    }
    public function edit(){
        $this->view->engine->layout(false);
        $request=Request::instance();
        if ($request->isPost()){
            $input=$request->param();
            $data['score_A']=trim($input['score_A']);
            $data['score_B']=trim($input['score_B']);
            $data['edit_user']=Session::get('username');
            $data['edit_time']=time();
            if ( $data['score_A']>$data['score_B']){
                $data['game_result']=1;
            }elseif ($data['score_A']==$data['score_B']){
                $data['game_result']=0;
            }else{
                $data['game_result']=-1;
            }
            if (Db::name('game')->where('id',$input['id'])->update($data)){
                return json(['code'=>0,'msg'=>' 添加成功！']);
            }else{
                return json(['code'=>1,'msg'=>'添加失败！']);
            }

        }else{
            $id=$request->param();
            $this->assign('data','game');
            return $this->fetch('edit',['id'=>$id]);
        }

    }
    public function find(){
        $this->view->engine->layout(false);
        $id=Request::instance()->param('id');
        $game_result=Db::name('game')->field('game_result')->where('id',$id)->find();
            if(Request::instance()->isPost()){
                $data['status']=1;
                $data['deal_user']=Session::get('username');
                $data['deal_time']=time();
                Db::name('myguess')->where('status',0)->where('result_game',$game_result['game_result'])->update($data);
                return json(['code'=>0,'msg'=>'派奖成功！']);
            }else{
                $list=Db::name('myguess')->field('user,id,status')->where('result_game',$game_result['game_result'])->where('id_game',$id)->select();
                return $this->fetch('find',['list'=>$list,'id'=>$id]);

            }
    }


}