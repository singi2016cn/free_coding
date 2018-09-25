<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我要保函</title>
</head>
<body>
<div style="text-align: center;margin-top: 100px">
    <h1>我要保函</h1>
    <a href="javascript:history.go(-1)">返回</a>
    <hr>
    <form action="curl.php?act=need_guarantee" method="post" target="_blank">
        <div>
            <label>交易中心</label>
            <select name="config" id="">
                <option value="1" selected>深圳建设局机构测试2</option>
                <option value="2"></option>
            </select>
        </div>
        <div>
            <label>招投标中心用户ID</label>
            <input type="text" name="yuid" required placeholder="招投标中心用户ID">
        </div>
        <div>
            <label>招投标中心用户公司名</label>
            <input type="text" name="company_name" required placeholder="招投标中心用户公司名">
        </div>
        <div>
            <input type="submit" value="我要保函">
        </div>
    </form>
</div>
</body>
</html>