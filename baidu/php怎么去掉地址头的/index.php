<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19
 * Time: 19:34
 */

$str = 'http://www.example.com';//要去除http://的地址
$result = str_replace('http://','',$str);
var_dump($result);//结果 www.example.com