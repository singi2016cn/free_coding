<?php
require_once dirname(__DIR__).'/config/db.php';

$insert['created_at'] = time();
$insert['referer'] = $_SERVER['HTTP_REFERER'];
$insert['request_url'] = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$insert['ip'] = $_SERVER['REMOTE_ADDR'];

$db->insert('user_access',$insert);
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <a href="detail.php">detail</a>
</body>
</html>