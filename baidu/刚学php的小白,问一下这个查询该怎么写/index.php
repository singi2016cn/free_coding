<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/30
 * Time: 18:28
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
//得到的二维数组
$data=[
    [
        'id'=>'1',
        'range'=>'所在部门以及下级部门',
        '功能项'=>'72',
    ],
    [
        'id'=>'1',
        'range'=>'所在部门以及下级部门',
        '功能项'=>'78,72',
    ],
    [
        'id'=>'10,7',
        'range'=>'所在部门以及下级部门',
        '功能项'=>'72,82',
    ],
];

//遍历
foreach($data as &$item){
    $sth = $db->query('select user_name from user where member_id in ('.$item['id'].')');
    $user = $sth->fetchAll(PDO::FETCH_ASSOC);
    $sth->debugDumpParams();
    $item['member_name'] = implode(',',array_column($user,'user_name'));
}
unset($item);

var_dump($data);


function getSign($password){
    return str_replace(base64_encode($password),['\r','\n']);
}