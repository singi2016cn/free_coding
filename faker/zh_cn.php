<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/6/8
 * Time: 17:11
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

$faker = new Faker\Generator();
$faker->addProvider(new Faker\Provider\zh_CN\Person($faker));

echo $faker->name;