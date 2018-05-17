<?php
/**
 * Created by PhpStorm.
 * User: lishuting
 * Date: 2018/5/17
 * Time: 下午9:52
 */

require_once 'Human.php';
require_once 'Monster.php';

use singiAdventure\Human;
use singiAdventure\Monster;

$singi = new Human('singi',50,25,6);

$monster = new Monster('bimeng',30,15,12);

echo "game start, singi vs monster !!! \n";

$singi->setDmg($singi->getAtt() - $monster->getDef());
$monster->setDmg($monster->getAtt() - $singi->getDef());

while ($singi->getHp() > 0 && $monster->getHp() > 0){
    $singi->attack($monster);
    echo "{$singi->getName()} attack , {$monster->getName()} hurt {$singi->getDmg()},rest hp: {$monster->getHp()}\n";
    if ($monster->getHp() <= 0) break;
    sleep(1);
    $monster->attack($singi);
    echo "{$monster->getName()} attack , {$singi->getName()} hurt {$monster->getDmg()},rest hp: {$singi->getHp()}\n";
    if ($singi->getHp() <= 0) break;
    sleep(1);
}

if ($singi->getHp() > 0){
    $winner = $singi->getName();
}else{
    $winner = $monster->getName();
}

echo "game over, {$winner} win !!! \n";