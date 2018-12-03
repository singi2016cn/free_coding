<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="index.php" method="post">
    <label>URL</label>
    <input type="text" name="url">
    <input type="submit" value="提交">
</form>
</body>
</html>
<?php
    error_reporting(0);
    $url = $_POST['url']?$_POST['url']:'http://www.wobuzhidao.com';
    $ret = file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'data.log',$url);
    if ($ret){
        echo '<hr/>';
        exit('URL: <pre>'.$url.'</pre>已经保存成功<br><a href="js.php">访问该页面跳转到保存的URL</a>');
    }else{
        exit('URL保存失败');
    }
?>