<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/18
 * Time: 14:33
 */

define('ROOT_PATH',dirname(__DIR__).DIRECTORY_SEPARATOR);

require_once ROOT_PATH . 'vendor/autoload.php';
require_once 'func.php';
require_once ROOT_PATH . 'config/db.php';

while (1){
    $singi = $db->get('roles',['name','level','exp','hp','pa','pd'],['user_id'=>1]);

    if (!$singi) break;

    $singi['cur_hp'] = $singi['hp'];

    $enemy = find_enemy($singi['level']);
    echo "\n---";
    echo "\n遇到敌人，开始战斗...";
    echo "\n{$singi['name']}({$singi['level']}) vs {$enemy['name']}({$enemy['level']})!";
    $enemy['cur_hp'] = $enemy['hp'];

    //当前回合dmg
    $singi['dmg'] = ensure_min_eq_zero($singi['pa'] - $enemy['pd']);
    $enemy['dmg'] = ensure_min_eq_zero($enemy['pa'] - $singi['pd']);

    while (1){
        if($singi['dmg'] == 0 && $enemy['dmg'] == 0){
            echo "\n双方势均力敌,无法对地方造成伤害!";
            echo "\n---";
            break;
        }

        $singi['cur_hp'] -= $enemy['dmg'];//计算剩余生命值
        if ($singi['cur_hp'] < 1){
            echo "\n{$singi['name']} 被击败了!游戏结束";
            echo "\n---";
            break;
        }
        echo "\n{$enemy['name']} 攻击了 {$singi['name']} 1 次,造成了 {$enemy['dmg']} 伤害";
        sleep(1);

        $enemy['cur_hp'] -= $singi['dmg'];
        if ($enemy['cur_hp'] < 1){
            echo "\n{$singi['name']} 赢得了胜利!获得经验值 {$enemy['exp']}";
            //TODO 是否获得宝物

            //是否升级
            $update_data['exp'] = $singi['exp'] += $enemy['exp'];
            if ($singi['exp'] > $level_singi[$singi['level']]){
                $update_data['level'] = $singi['level'] += 1;
                $update_data['hp'] =$singi['hp'] += 5;
                $update_data['pa'] =$singi['pa'] += 5;
                $update_data['pd'] =$singi['pd'] += 5;
                echo "\n{$singi['name']} 升级了!现在的等级是：level {$singi['level']},各项属性成长：[+5,+5,+5]=>[{$singi['hp']},{$singi['pa']},{$singi['pd']}]";
            }
            //更新数据到数据库
            $db->update('roles',$update_data,['user_id'=>1]);
            echo "\n---";
            break;
        }
        echo "\n{$singi['name']} 攻击了 {$enemy['name']} 1 次,造成了 {$singi['dmg']} 伤害";
        sleep(1);
    }
}




