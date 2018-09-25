<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>查询保函</title>
</head>
<body>
<div style="text-align: center;margin-top: 100px">
    <h1>查询保函</h1>
    <a href="javascript:history.go(-1)">返回</a>
    <hr>
    <form action="curl.php?act=search_guarantee" method="post" target="_blank">
        <div>
            <label>序列号</label>
            <input type="text" name="eno" placeholder="序列号">
        </div>
        <div>
            <label>校验码</label>
            <input type="text" name="pass" placeholder="校验码">
        </div>
        <div>
            <input type="submit" value="查询保函">
        </div>
    </form>
</div>
</body>
</html>