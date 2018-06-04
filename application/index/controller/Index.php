<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Index extends Controller
{
    public function index()
    {
       $time=time()-7200;
        $arr=array('A','B','C','D','E','F','G','H');
        foreach ($arr as $key=>$item){
            $zu[$item][1]=Db::name('game')->where('game_type',$item)->where('game_time','>',$time)->order('game_time','asc')->select();
            $zu[$item][2]=Db::name('game')->where('game_type',$item)->where('game_time','<=',$time)->order('game_time','asc')->select();
        }
        $user=Session::get('user');
        $setting=Db::name('setting')->where('id',1)->find();
        $guess=Db::table('tp_myguess')->alias('a')->join('tp_game b','a.id_game=b.id')->where('a.user',$user)->order('a.guess_time','desc')->select();
        $forecast=Db::name('forecast')->where('user',$user)->select();
        $game4=Db::name('game')->where('game_type',16)->order('game_time','asc')->select();
        $game5=Db::name('game')->where('game_type',8)->order('game_time','asc')->select();
        $game6=Db::name('game')->where('game_type',4)->order('game_time','asc')->select();
        $game7=Db::name('game')->where('game_type',3)->order('game_time','asc')->select();
        $game8=Db::name('game')->where('game_type',2)->order('game_time','asc')->select();
        $this->assign([
            'guess' => $guess,
            'zu'=>$zu,
            'forecast'=>$forecast,
            'game4'=>getOrder($game4),
            'game5' =>getOrder($game5),
            'game6'=>getOrder($game6),
            'game7' =>getOrder($game7),
            'game8'=>getOrder($game8),
            'time' =>time(),
            'setting'=>$setting,
            'user' =>$user,
        ]);
        return $this->fetch();
    }
    public function rule()
    {
    return $this->fetch();
    }
    public function info()
    {
        if (Request::instance()->isPost()){
             $input=Request::instance()->param();
             $code=Session::get('captcha');
             $data['apply_project']=$input['id'];
             $data['user']=$input['user'];
             if (!Db::name('user')->where('user',$data['user'])->find()){
              return json(['code'=>1,'msg'=>'账号不存在']);
             }
             if (strtoupper($input['code'])!=$code)  return json(['code'=>2,'msg'=>'验证码错误！']);
             if ($input['type']==2){
                  $msg=Db::name('apply')->where($data)->find();
                  if($msg){
                      if ($msg['apply_status']=='未通过'){
                          return json(['code'=>0,'msg'=>'您的申请<font color="red">'.$msg['apply_status'].'</font>原因：'.$msg['remark']]);
                      }else{
                          return json(['code'=>0,'msg'=>'您的申请<font color="red">'.$msg['apply_status'].'</font>']);
                      }
                  }else{
                       return json(['code'=>1,'msg'=>'此账号未申请此项活动']);
                  }
             }else{
                  if(Db::name('apply')->where($data)->find()){
                       return json(['code'=>1,'msg'=>'您已申请过此项目！']);
                  }else{
                       $data['apply_remark']=$input['apply_remark'];
                       $data['apply_time']=time();
                       Db::name('apply')->insert($data);
                       return json(['code'=>0,'msg'=>'活动申请成功！']);
                  }
             }
        }else{
             $id=Request::instance()->param('id');
             return $this->fetch('info'.$id,['id'=>$id]);
      }

    }
    public function reg(){
        $user=Request::instance()->param();
       
       if (Db::name('user')->where('user',$user['user'])->find()){
           Session::set('user',$user['user']);
            return json(['code'=>0,'msg'=>'登陆成功']);
        }else{
           return json(['code'=>1,'msg'=>'账号未注册']);
        }

    }
    public function forecast(){
        $input=Request::instance()->param();
        $user=Session::get('user');
        if(!$user){
            return json(['code'=>2]);
        }else{
            $data['forecast_team']=trim($input['html'],',');
            $data['forecast_time']=time();
            $b=$input['type'];
            if(getTimeRange('forecast',$b)==1){
                return json(['code'=>1,'msg'=>'现在不可以预测哦~~请留意活动时间']);
            }else{
                $info=Db::name('forecast')->where('forecast_type',$input['type'])->where('user',$user)->find();
                if($info){
                    Db::name('forecast')->where('forecast_type',$input['type'])->where('user',$user)->update( $data);
                    return json(['code'=>1,'msg'=>'预测修改成功']);
                }else{
                    $data['user']=$user;
                    $data['forecast_type']=$input['type'];
                    if(Db::name('forecast')->insert($data)){
                        return json(['code'=>0,'msg'=>'预测添加成功']);
                    }else{
                        return json(['code'=>0,'msg'=>'预测添加失败！请稍后再试~~']);
                    }
                }
            }
        }
    }
    public function guess(){
        $input=Request::instance()->param();
        $user=Session::get('user');
        $i=0;
        if(!$user){
            return json(['code'=>2]);
        }else {

            $arr = $input['project'];
                foreach ($arr as $key => $item) {
                    if($item[0]==""){
                        $i++;
                        if($i==count($arr)){
                            return json(['code'=>1,'msg'=>'您未选择竞猜哦！']);
                        }
                       continue;
                    }
                    $data['user'] = $user;
                    $data['id_game'] = $item[1];
                    $data['result_game'] = $item[0];
                    $data['guess_time'] = time();
                    if (Db::name('myguess')->where('user',$user)->where('id_game',$data['id_game'])->find()) {
                        Db::name('myguess')->where('user',$user)->where('id_game',$data['id_game'])->update($data);
                    }else{
                        Db::name('myguess')->insert($data);
                    }
                }

        }
        return json(['code'=>0,'msg'=>'竞猜成功！']);
    }
    public function image_captcha(){
        $image = imagecreatetruecolor(100, 30);

        //2.为画布定义(背景)颜色
        $bgcolor = imagecolorallocate($image, 255, 255, 255);

        //3.填充颜色
        imagefill($image, 0, 0, $bgcolor);

        // 4.设置验证码内容

        //4.1 定义验证码的内容
        $content = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        //4.1 创建一个变量存储产生的验证码数据，便于用户提交核对
        $captcha = "";
        for ($i = 0; $i < 4; $i++) {
            // 字体大小
            $fontsize = 10;
            // 字体颜色
            $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            // 设置字体内容
            $fontcontent = substr($content, mt_rand(0, strlen($content)), 1);
            $captcha .= $fontcontent;
            // 显示的坐标
            $x = ($i * 100 / 4) + mt_rand(5, 10);
            $y = mt_rand(5, 10);
            // 填充内容到画布中
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
        }
        Session::set('captcha',strtoupper($captcha)) ;

        //4.3 设置背景干扰元素
        for ($$i = 0; $i < 200; $i++) {
            $pointcolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetpixel($image, mt_rand(1, 99), mt_rand(1, 29), $pointcolor);
        }

        //4.4 设置干扰线
        for ($i = 0; $i < 3; $i++) {
            $linecolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($image, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $linecolor);
        }

        //5.向浏览器输出图片头信息
        header('content-type:image/png');

        //6.输出图片到浏览器
        imagepng($image);

            //7.销毁图片
        imagedestroy($image);
    }
}
