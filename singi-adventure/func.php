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
        $faker = new Faker\Generator();
        $faker->addProvider(new Faker\Provider\zh_CN\Person($faker));
        return $faker->name;
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
        $ack = mt_rand($level_enemy[$level]['ack'][0], $level_enemy[$level]['ack'][1]);
        $def = mt_rand($level_enemy[$level]['def'][0], $level_enemy[$level]['def'][1]);
        $exp = mt_rand($level_enemy[$level]['exp'][0], $level_enemy[$level]['exp'][1]);
        $kill = mt_rand($level_enemy[$level]['kill'][0], $level_enemy[$level]['kill'][1]);
        $dodge = mt_rand($level_enemy[$level]['dodge'][0], $level_enemy[$level]['dodge'][1]);
        $enemy = [
            'name' => make_name(),
            'hp' => $hp,
            'ack' => $ack,
            'def' => $def,
            'exp' => $exp,
            'level' => $level,
            'kill' => $kill,
            'dodge' => $dodge,
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

