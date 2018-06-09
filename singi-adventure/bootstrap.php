<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/18
 * Time: 14:33
 */

define('ROOT_PATH',dirname(__DIR__).DIRECTORY_SEPARATOR);

require_once ROOT_PATH . 'vendor/autoload.php';

use Symfony\Component\Cache\Simple\FilesystemCache;

require_once 'func.php';
require_once ROOT_PATH . 'config/db.php';

$cache = new FilesystemCache();

while (1){
    $singi = $cache->get('player',array(
        'name' => 'singi',
        'hp' => 20,
        'ack' => 10,
        'def' => 10,
        'kill' => 1000,
        'dodge' => 1000,
        'level' => 1,
        'exp' => 0,
    ));

    if (!$singi) break;

    $singi['cur_hp'] = $singi['hp'];

    $enemy = find_enemy($singi['level']);
    echo "\n---";
    echo "\n遇到敌人，开始战斗...";
    echo "\n{$singi['name']}({$singi['level']}) vs {$enemy['name']}({$enemy['level']})!";
    $enemy['cur_hp'] = $enemy['hp'];

    //当前回合dmg
    $singi['dmg'] = ensure_min_eq_zero($singi['ack'] - $enemy['def']);
    $enemy['dmg'] = ensure_min_eq_zero($enemy['ack'] - $singi['def']);

    while (1){
        if($singi['dmg'] == 0 && $enemy['dmg'] == 0){
            echo "\n双方势均力敌,无法对地方造成伤害!";
            echo "\n---";
            break;
        }

        $enemy_dmg = $enemy['dmg'];
        $enemy_echo_skill = "";
        if (mt_rand(0,10000) < $enemy['kill']){
            $enemy_echo_skill = "发动了必杀一击 ";
            $enemy_dmg *= 3;//必杀3倍伤害
        }
        $singi_echo_dodge = "";
        if (mt_rand(0,10000) < $singi['dodge']){
            $singi_echo_dodge = " 但被闪避";
            $enemy_dmg = 0;
        }
        echo "\n{$enemy['name']} {$enemy_echo_skill}攻击了 {$singi['name']} 1 次{$singi_echo_dodge},造成了 {$enemy_dmg} 伤害";
        $singi['cur_hp'] -= $enemy_dmg;//计算剩余生命值
        if ($singi['cur_hp'] < 1){
            echo "\n{$singi['name']} 被击败了!游戏结束";
            echo "\n---";
            break;
        }
        sleep(1);

        $singi_dmg = $singi['dmg'];
        $singi_echo_skill = "";
        if (mt_rand(0,10000) < $singi['kill']){
            $singi_echo_skill = "发动了必杀一击 ";
            $singi_dmg *= 3;//必杀3倍伤害
        }
        $enemy_echo_dodge = "";
        if (mt_rand(0,10000) < $enemy['dodge']) {
            $enemy_echo_dodge = " 但被闪避";
            $singi_dmg = 0;
        }
        echo "\n{$singi['name']} {$singi_echo_skill}攻击了 {$enemy['name']} 1 次{$enemy_echo_dodge},造成了 {$singi_dmg} 伤害";
        $enemy['cur_hp'] -= $singi_dmg;
        if ($enemy['cur_hp'] < 1){
            echo "\n{$singi['name']} 赢得了胜利!获得经验值 {$enemy['exp']}";
            //TODO 是否获得宝物

            //是否升级
            $update_data['exp'] = $singi['exp'] += $enemy['exp'];
            if ($singi['exp'] > $level_singi[$singi['level']]){
                $update_data['level'] = $singi['level'] += 1;
                $update_data['hp'] =$singi['hp'] += 5;
                $update_data['ack'] =$singi['ack'] += 5;
                $update_data['def'] =$singi['def'] += 5;
                echo "\n{$singi['name']} 升级了!现在的等级是：level {$singi['level']},各项属性成长：[+5,+5,+5]=>[{$singi['hp']},{$singi['ack']},{$singi['def']}]";
            }
            //更新数据
            $cache->set('player',array_merge($update_data,$singi));
            echo "\n---";
            break;
        }
        sleep(1);
    }
}




