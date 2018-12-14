<?php
$f_name = "num.txt";//计数器的数据保存在num.txt
$n_digit = 10;
//如果文件不存在，则新建文件，初始值置为100/
if (!file_exists($f_name)) {
    $fp = fopen($f_name, "w");
    fputs($fp, "100");
    fclose($fp);
}
$fp = fopen($f_name, "r"); //打开num.txt文件
$hits = fgets($fp, $n_digit); //开始计取数据
fclose($fp); //关闭文件
$hits = (int)$hits + 1;//计数器增加1
$hits = (string)$hits;
$fp = fopen($f_name, "w");
fputs($fp, $hits);//写入新的计数
fclose($fp); //关闭文件
echo $hits;
