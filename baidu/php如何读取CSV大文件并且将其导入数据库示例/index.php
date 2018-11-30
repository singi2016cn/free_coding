<?php
/**
 * Created by PhpStorm.
 * User: singi
 * Date: 2018/11/29
 * Time: 23:27
 * 读取csv文件，每读取一行数据，就插入数据库
 */

//获取数据库实例
$dsn = 'mysql:dbname=test;host=127.0.0.1';
$user = 'root';
$password = '';
try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

//读取file.csv文件
if (($handle = fopen("file.csv", "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //写入数据库
        $sth = $db->prepare('insert into test set name=:name,age=:age');
        $sth->bindParam(':name',$row[0],PDO::PARAM_STR,255);
        $sth->bindParam(':age',$row[1],PDO::PARAM_INT);
        $sth->execute();
    }
    fclose($handle);
}