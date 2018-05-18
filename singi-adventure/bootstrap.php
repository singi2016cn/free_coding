<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/18
 * Time: 14:33
 */

$level = [
    1 => 10,
    2 => 20,
    3 => 40,
    4 => 80,
    5 => 160,
    6 => 320,
    7 => 640,
    8 => 1280,
    9 => 2560,
    10 => 5120,
];

$singi = [
    'name'=>'singi',
    'hp'=>55,
    'ack'=>10,
    'def'=>10,
    'exp'=>0,
    'level'=>1
];

$enemy = [
    'name'=>'silly',
    'hp'=>35,
    'ack'=>20,
    'def'=>3,
    'exp'=>12,
    'level'=>1
];

echo "game start, {$singi['name']}({$singi['level']}) vs {$enemy['name']}({$enemy['level']}) !!!";

//等到本回合hp
$singi['cur_hp'] = $singi['hp'];
$enemy['cur_hp'] = $enemy['hp'];

//等到本回合dmg
$singi['dmg'] = $singi['ack'] - $enemy['def'];
$enemy['dmg'] = $enemy['ack'] - $singi['def'];

while ($singi['hp'] > 0 || $enemy['hp'] > 0){

    $singi['cur_hp'] -= $enemy['dmg'];//计算剩余生命值
    if ($singi['cur_hp'] < 1){
        echo "\ngame over, {$singi['name']} failed !!!";
        break;
    }
    echo "\n{$enemy['name']} attack {$singi['name']},hurt {$enemy['dmg']},rest cur_hp {$singi['cur_hp']}";
    sleep(1);

    $enemy['cur_hp'] -= $singi['dmg'];
    if ($enemy['cur_hp'] < 1){
        echo "\ngame over, {$singi['name']} win !!! get exp {$enemy['exp']}";
        $singi['exp'] += $enemy['exp'];
        if ($singi['exp'] > $level[$singi['level']]){
            $singi['level'] += 1;
            $singi['hp'] += 5;
            $singi['ack'] += 5;
            $singi['def'] += 5;
            echo "\n{$singi['name']} level up !!!\nnow is level 2,[+5,+5,+5]=>[{$singi['hp']},{$singi['ack']},{$singi['def']}]";
        }
        break;
    }
    echo "\n{$singi['name']} attack {$enemy['name']},hurt {$singi['dmg']},rest cur_hp {$enemy['cur_hp']}";
    sleep(1);
}


