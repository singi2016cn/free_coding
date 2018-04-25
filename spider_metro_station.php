<?php
/**
 * Created by PhpStorm.
 * User: lishuting
 * Date: 2018/4/25
 * Time: 下午10:41
 */

$js = file_get_contents('http://www.szmc.net/public/scripts/sites.js');

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

    <button onclick="metro_station()">存储地铁站点</button>
    <?php echo '<script>'.$js.'</script>';?>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script>
        console.log(sites);
        function metro_station(){
            console.log(1);
            $.post('save_metro_stations.php',{site1:site1},function(res){
                console.log(res);
            },'json');
        }
    </script>
</body>
</html>
