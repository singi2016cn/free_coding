<?php
/**
 * Created by PhpStorm.
 * User: lishuting
 * Date: 2018/5/19
 * Time: 下午8:38
 */

require_once 'level.php';

if (!function_exists('make_name')) {
    /**
     * 随机产生名字
     * @return string
     */
    function make_name()
    {
        return Faker\Factory::create()->name;
    }
}

if (!function_exists('find_enemy')) {
    /**
     * 产生敌人
     * @param int $level_user 用户等级（根据用户等级匹配对等的敌人)
     * @return array
     */
    function find_enemy($level_user = 1)
    {
        global $level_enemy;
        $level = mt_rand(1, $level_user);
        $hp = mt_rand($level_enemy[$level]['hp'][0], $level_enemy[$level]['hp'][1]);
        $ack = mt_rand($level_enemy[$level]['pa'][0], $level_enemy[$level]['pa'][1]);
        $def = mt_rand($level_enemy[$level]['pd'][0], $level_enemy[$level]['pd'][1]);
        $exp = mt_rand($level_enemy[$level]['exp'][0], $level_enemy[$level]['exp'][1]);
        $enemy = [
            'name' => make_name(),
            'hp' => $hp,
            'pa' => $ack,
            'pd' => $def,
            'exp' => $exp,
            'level' => $level
        ];
        return $enemy;
    }
}

if (!function_exists('ensure_min_eq_zero')) {
    /**
     * 返回的整数值最小为0
     * @param $num
     * @return int
     */
    function ensure_min_eq_zero($num)
    {
        if ($num < 0) $num = 0;
        return $num;
    }
}

