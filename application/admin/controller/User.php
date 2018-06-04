<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8
 * Time: 19:07
 */

namespace app\admin\controller;


use think\Db;
use think\Request;
use think\Session;

class User extends Admin
{
    public function guess(){
        $search['user']='';
        $search['game_type']='';
        if (Request::instance()->isPost()){
            $input=Request::instance()->param();
            if ($input['type']==1){
                $list=Db::table('tp_myguess')->alias('a')->join('tp_game b','a.id_game=b.id')->
                field('a.*,b.team_A,b.team_B,b.game_result,b.game_time,b.game_type')->where('user',$input['user'])->
                order('a.guess_time','asc')->paginate(16);
                $search['user']=$input['user'];
            }else{
                $list=Db::table('tp_myguess')->alias('a')->join('tp_game b','a.id_game=b.id')->
                field('a.*,b.team_A,b.team_B,b.game_result,b.game_time,b.game_type')->where('b.game_type',$input['game_type'])->
                order('a.guess_time','asc')->paginate(16);
                $search['game_type']=$input['game_type'];
            }
        }else{
            $list=Db::table('tp_myguess')->alias('a')->join('tp_game b','a.id_game=b.id')->field('a.*,b.team_A,b.team_B,b.game_result,b.game_time,b.game_type')->order('a.guess_time','asc')->paginate(8);

        }
        $this->assign('data','user');
        return $this->fetch('list',['list'=>$list,'search'=>$search]);

    }
    public function forecast(){
        $search['user']='';
        $search['select']='';
        if (Request::instance()->isPost()) {
            $input = Request::instance()->param();
            if ($input['type'] == 1) {
                $forecast = Db::name('forecast')->where('user',$input['user'])->paginate(16);
                $search['user']=$input['user'];
            }else{
                $forecast = Db::name('forecast')->where('forecast_type',$input['forecast_type'])->paginate(16);
                $search['select']=$input['forecast_type'];
            }
        }else{
            $forecast=Db::name('forecast')->paginate(16);
        }
        $this->assign('data','user');
        return $this->fetch('forecast',['list'=>$forecast,'search'=>$search]);

    }
    public function deal(){
        $id=Request::instance()->param();
        $data['status']=1;
        $data['deal_user']=Session::get('username');
        $data['deal_time']=time();
        if(Db::name('myguess')->where($id)->update($data)){
            return json(['code'=>0,'msg'=>'派奖成功']);
        }else{
            return json(['code'=>1,'msg'=>'派奖失败']);
        }
    }
    public function prize(){
        $id=Request::instance()->param();
        $data['status']=1;
        $data['prize']=0;
        $data['deal_user']=Session::get('username');
        $data['deal_time']=time();
        if(Db::name('forecast')->where($id)->update($data)){
            return json(['code'=>0,'msg'=>'派奖成功']);
        }else{
            return json(['code'=>1,'msg'=>'派奖失败']);
        }
    }
    public function apply(){
        $search['user']='1';
        $search['game_type']='1';
        $where=[];
        if (Request::instance()->isPost()){
            $where=Request::instance()->param();
        }else{
        $where['apply_status']='未处理';
        }
        $list=Db::name('apply')->where($where)->paginate(16);
        $this->assign('data','user');
        return $this->fetch('apply',['list'=>$list,'search'=>$search]);

    }
    public function del(){
        $id=Request::instance()->param();
        if (Db::name('apply')->where($id)->delete()){
            return json(['code'=>0,'msg'=>'删除成功']);
        }else{
            return json(['code'=>1,'msg'=>'删除失败']);
        }

    }
    public function edit(){
        $input=Request::instance()->param();
        if (Db::name('apply')->where('id',$input['id'])->update($input)){
            return json(['code'=>0,'msg'=>'编辑成功']);
        }else{
            return json(['code'=>1,'msg'=>'编辑失败']);
        }
    }
    public function tan(){
        $this->view->engine->layout(false);
        $id=Request::instance()->param('id');
        if (Request::instance()->isPost()){
            $input=Request::instance()->param();
            $input['apply_status']="未通过";
            if (Db::name('apply')->where('id',$input['id'])->update($input)){
                return json(['code'=>0,'msg'=>'编辑成功']);
            }else{
                return json(['code'=>1,'msg'=>'编辑失败']);
            }
        }
        return $this->fetch('tan',['id'=>$id]);
    }
    public function upload(){

         $this->assign('data','user');
         $request=Request::instance();
         if ($request->isPost()){
             $file=$request->file('user');
             if(empty($file)) return json(['code'=>1,'msg'=>'请选择上传文件']);
             $file_name=pathinfo($file->getInfo()['name'], PATHINFO_EXTENSION);
             if($file_name=='xlsx'){
                 $data = $file->move(ROOT_PATH . 'public' . DS . 'file');
                 if($data){
                     vendor("PHPExcel");
                     $PHPExcel = new \PHPExcel();
                     $PHPReader = \PHPExcel_IOFactory::createReader('Excel2007');  
                     $filename=ROOT_PATH . 'public' . DS . 'file/'.$data->getSaveName();
                     $excel = $PHPReader->load($filename);
                     $currentSheet = $excel->getSheet(0);
                     $allColumn = $currentSheet->getHighestColumn();
                     //获取总行数
                     $allRow = $currentSheet->getHighestRow();

                     //循环获取表中的数据,$currentRow表示当前行,从哪行开始读取数据,索引值从0开始
                     for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                         //从哪列开始，A表示第一列
                         for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                             $address = $currentColumn . $currentRow;
                             $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
                         }
                     }
                     foreach ($arr as $key=>$item){
                         if($key==1) continue;
                         if ($item['A']=="") continue;
                         if (Db::name('user')->where('user',$item['A'])->find()) continue;
                         $content['user']=$item['A'];
                         $content['addtime']=time();
                         $content['add_user']=Session::get('username');
                         Db::name('user')->insert($content);
                     }
                     return json(['code'=>0,'msg'=>'导入成功！']);
                 }else{
                     return json(['code'=>1,'msg'=>$data->getError()]);
                 }

             }else{
                 return json(['code'=>1,'msg'=>'上传文件的格式必须为xlsx']);
             }
         }else{
             $list=Db::name('user')->order('addtime','desc')->paginate(60);
             return $this->fetch('upload',['list'=>$list]);
         }

    }
  public function uploadone(){
     $input=Request::instance()->param();
     $input['addtime']=time();
     $input['add_user']=Session::get('username');
     if(Db::name('user')->where('user',$input['user'])->find())  return json(['code'=>1,'msg'=>'此用户已存在！']);
     if( Db::name('user')->insert($input)){
        return json(['code'=>0,'msg'=>'添加成功！']);
     }else{
        return json(['code'=>1,'msg'=>'添加失败！']);
     }
    
  
  }

}