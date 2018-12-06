<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/18
 * Time: 14:31
 *
 * https://medoo.in/api/get
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use Medoo\Medoo;

$db = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'test',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '123456'
]);