<?php
define('INDEX',1);
require './lib/autoload.php';

$op = isset($_GET['op'])?$_GET['op']:'';

if($op == 'get_diff'){
    header( 'Content-Type:application/json;charset=utf-8 ');

    $db_source = $_POST['db_source'];
    $db_target = $_POST['db_target'];
    if(!$db_source){
        exit(json_encode(['code'=>0,'msg'=>'请选择源库']));
    }
    if(!$db_target){
        exit(json_encode(['code'=>0,'msg'=>'请选择目标库']));
    }

    $data_dir = './data/';
    $params = array(
        'server1' => $data_dir.$db_source,
        'server2' => $data_dir.$db_target,
        'type'=> 'schema',//schema or data or all
//            'include'=>'down'
    );

    try{
        $res = DBDiff\DBDiff::run($params);
    }catch (Exception $e){
        exit(json_encode(['code'=>0,'msg'=>$e->getMessage()]));
    }

    exit(json_encode(['code'=>1,'data'=>$res]));
}else{

    $path = './data';
    $dir = [];
    if (is_dir($path)) {
        $handle = @opendir($path);

        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $dir[] = $file;
            }
        }
        closedir($handle);
    }

    require './view/index.php';
}

