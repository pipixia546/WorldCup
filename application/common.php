<?php
use think\Db;
function getTeamName($id){
    $info=Db::name('team')->field('team_name,team_logo')->where('id',$id)->find();
    return $info['team_name'];
}
function getTeamLogo($id){
    $info=Db::name('team')->field('team_name,team_logo')->where('id',$id)->find();
    return $info['team_logo'];
}
function getTime($field){
    $time=Db::name('setting')->where('id',1)->find();
    return $time[$field];
}
function getTimeRange($a,$b){
    $time=time();
    $info=Db::name('setting')->where('id',1)->find();
    if($a=='guess'){
       $arr=['A','B','C','D','E','F','G','H'];
       if (in_array($b,$arr)){
           if ($time>$info['guess_zu_start_time']&&$time<$info['guess_zu_end_time']){
               return 0;
           } else{
               return 1;
           }
       }elseif ($b==8){
           if ($time>$info['guess_eight_start_time']&&$time<$info['guess_eight_end_time']){
               return 0;
           } else{
               return 1;
           }
       }elseif ($b==4){
           if ($time>$info['guess_four_start_time']&&$time<$info['guess_four_end_time']){
               return 0;
           } else{
               return 1;
           }
       }elseif ($b==3){
           if ($time>$info['guess_three_start_time']&&$time<$info['guess_three_end_time']){
               return 0;
           } else{
               return 1;
           }
       }elseif ($b==2){
           if ($time>$info['guess_two_start_time']&&$time<$info['guess_two_end_time']){
               return 0;
           } else{
               return 1;
           }
       }
    }else{
           if ($b==16){
               if ($time>$info['fore_sixteen_start_time']&&$time<$info['fore_sixteen_end_time']){
                   return 0;
               } else{
                   return 1;
               }
           }elseif($b==8){
               if ($time>$info['fore_eight_start_time']&&$time<$info['fore_eight_end_time']){
                   return 0;
               } else{
                   return 1;
               }
           }elseif($b==4){
               if ($time>$info['fore_four_start_time']&&$time<$info['fore_four_end_time']){
                   return 0;
               } else{
                   return 1;
               }
           }elseif($b==2){
               if ($time>$info['fore_two_start_time']&&$time<$info['fore_two_end_time']){
                   return 0;
               } else{
                   return 1;
               }
           }

    }
}
function getNum($str,$type){
     $arrB=explode(',',$str);
     $arrA=Db::name('team')->field('team_name')->where('best_type','<=',$type)->select();
     if(empty($arrA)){
         return '尚未添加该项目队伍';
     }
     $num=0;
     foreach ($arrA as $item){
         for ($i=0;$i<count($arrB);$i++){
             if($item['team_name']==$arrB[$i]){
                 $num=$num+1;
             }
         }

     }
     return $num;
 }
function getGameStatus($time){
    $now_time=time();
    $cha_time=$now_time-$time;
    if($cha_time>0 && $cha_time<7200){
        return '<font color="red">正在进行</font>';
    }elseif($cha_time>-3600 && $cha_time<=0){
        return '<font color="red">即将开始</font>';
    }elseif($cha_time<=3600){
        return '未开始';
    }else{
        return 1;
    }

}
 function getprize($str,$type){
  $num=getNum($str,$type);
  if(!is_numeric($num)){
   		return '未结算';
   }
   if($type==16){
      if(8<=$num&&$num<10){
          return 88;
      }elseif(10<=$num&&$num<12){
          return 188;
      }elseif(12<=$num&&$num<14){
          return 388;
      }elseif(14<=$num&&$num<16){
          return 588;
      }elseif($num==16){
          return 888;
      }else{
         return '未中奖';
      }
   }elseif($type==8){
        if($num==5){
            return 88;
        }elseif($num==6){
            return 188;
        }elseif($num==7){
            return 388;
        }elseif($num==8){
            return 888;
        }else{
           return '未中奖';
        }
   }elseif($type==4){
        if($num==2){
            return 88;
        }elseif($num==3){
            return 188;
        }elseif($num==4){
            return 388;
        }else{
           return '未中奖';
        }
   }elseif($type==2){
        if($num==1){
            return 88;
        }elseif($num==2){
            return 388;
        }else{
           return '未中奖';
        }
   }else{
       if($num==1){
            return 188;
        }else{
           return '未中奖';   
     }     }

 }
function getOrder($info){
    $i=0;$j=0;
    $info_one=[];
    $info_two=[];
    foreach ($info as $key=>$item){
        $time=time();
        $cha_time=$item['game_time']-$time;
        if($cha_time>-7200){
            $info_one[$i]=$item;
            $i++;
        }else{
            $info_two[$j]=$item;
            $j++;
        }
    }
    $n=count($info_one);
    foreach ($info_two as $vo){
        $info_one[$n]=$vo;
        $n++;
    }
    return $info_one;
}
function getTeam($id){
    $team=Db::name('team')->where('id',$id)->value('team_name');
    return $team;
}