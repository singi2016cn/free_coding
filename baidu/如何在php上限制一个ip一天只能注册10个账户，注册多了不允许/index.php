<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/30
 * Time: 19:35
 * 限制一个ip一天只能注册10个账户
 * 获取访问用户ip，查询数据库判断该ip是否可以继续注册新用户
 */
//获取数据库实例
$dsn = 'mysql:dbname=test;host=127.0.0.1';
$user = 'root';
$password = '';
try {
    $db = new PDO($dsn, $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

//获取访问用户ip
$access_user_ip = $_SERVER['REMOTE_ADDR'];

//查询数据库判断该ip是否可以继续注册新用户
$start_time = strtotime(date('Y-m-d'));//今天0点
$end_time = strtotime(date('Y-m-d').' +1 day ');//明天0点
$sth = $db->prepare('select count(*) from user where ip=:ip and created_at>:start_time and created_at<:end_time');
$sth->bindParam(':ip',$access_user_ip);
$sth->bindParam(':start_time',$start_time);
$sth->bindParam(':end_time',$end_time);
$sth->execute();
$count = $sth->fetchColumn();//当前该ip今天注册的用户总数量
if ($count>10){
    exit('今天，您已注册10个新账号了，请明天再来吧');
}


