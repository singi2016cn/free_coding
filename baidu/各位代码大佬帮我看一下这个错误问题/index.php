<?php
$id = $_GET['id'];
$pw = $_GET['pw'];
$link = mysqli_connect('localhost', 'root', 'root', 'student', '3306');
mysqli_set_charset($link, 'utf8');
$result = mysqli_query($link, "select * from information");
if (!$result) {
    exit(+mysqli_error($link));
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($id == $row['emailId'] && $pw == $row['password']) {
            echo('<script>alert("登录成功")</script>');
            echo('你的邮箱:');
            echo $id;
            echo '</br>';
            echo('你的密码:');
            echo $pw;
            echo '</br>';
            echo('修改密码:');
            echo '<form method="sub.php">';//sub.php是你想要跳转的php文件
            echo "<input type='text'>";
            echo "<input type='submit' value='确认修改'>";
            echo "</form>";
            exit();
        } else {
            echo('<script>alert("账号不存在或密码错误")</script>');
            exit();
        }
    }
} ?>