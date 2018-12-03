<?php
error_reporting(0);
$url = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'data.log');
if(!$url){
    $url = 'http://www.wobuzhidao.com';
}
?>
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
<h1>1秒钟后跳转到新页面</h1>
<script language="javascript" type="text/javascript">
    window.setTimeout("window.location='<?php echo $url;?>'",1000);
</script>
</body>
</html>