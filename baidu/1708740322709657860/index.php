<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2020/3/19
 * Time: 9:36
 */

$id = $_GET['id'];//接受get传递的参数名x的值并赋值给变量id
$conn = mysql_connect('127.0.0.1', 'root', '123456');//连接mysql数据库
if (!$conn) {
    echo "连接错误";
}
mysql_select_db('test', $conn);//选择$conn连接请求下的test数据库名
$sql = "select * from user where id=$id";//定义sql语句并组合变量id
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    echo "ID" . $row['id'] . "</br>";
    echo "用户名" . $row['username'] . "</br>";
    echo "密码" . $row['password'] . "</br>";
}
mysql_close($conn);
echo "<hr>";
echo "当前语句：";
echo $sql;