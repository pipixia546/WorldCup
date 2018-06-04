<?php
use think\Db;
 function getName($id){
     $info=Db::name('team')->field('team_name,team_logo')->where('id',$id)->find();
     return $info['team_name'];
 }

 function getForecastType($str)
 {
     switch ($str) {
         case 'A':
             return '小组赛A组';
             break;
         case 'B':
             return '小组赛B组';
             break;
         case 'C':
             return '小组赛C组';
             break;
         case 'D':
             return '小组赛D组';
             break;
         case 'E':
             return '小组赛E组';
             break;
         case 'F':
             return '小组赛F组';
             break;
         case 'G':
             return '小组赛G组';
             break;
         case 'H':
             return '小组赛H组';
             break;
         case 16:
             return '16强';
             break;
         case 8:
             return '8强';
             break;
         case 4:
             return '4强';
             break;
         case 3:
             return '三、四名决赛';
             break;
         case 2:
             return '决赛';
             break;
          case 1:
             return '冠军';
             break;
     }
 }
function getGuessType($str)
{
    switch ($str) {
        case 'A':
            return '小组赛A组';
            break;
        case 'B':
            return '小组赛B组';
            break;
        case 'C':
            return '小组赛C组';
            break;
        case 'D':
            return '小组赛D组';
            break;
        case 'E':
            return '小组赛E组';
            break;
        case 'F':
            return '小组赛F组';
            break;
        case 'G':
            return '小组赛G组';
            break;
        case 'H':
            return '小组赛H组';
            break;
        case 16:
            return '1/8决赛';
            break;
        case 8:
            return '1/4决赛';
            break;
        case 4:
            return '半决赛';
            break;
        case 3:
            return '三、四名决赛';
            break;
        case 2:
            return '决赛';
            break;
    }
}
 function getProject($item){
     switch ($item) {
         case 1:
             return '第一步曲';
             break;
         case 2:
             return '第二步曲';
             break;
         case 3:
             return '第三步曲';
             break;
         case 4:
             return '第四步曲';
             break;
         case 5:
             return '第五步曲';
             break;
         case 6:
             return '第六步曲';
             break;
         case 7:
             return '第七步曲';
             break;
     }
 }