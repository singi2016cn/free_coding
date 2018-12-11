<?php


/**
 * 大小写字符,数字;长度在6到30个字符
 * @param $name
 * @return bool
 */
function check_user($name){
    $preg = '/[^a-zA-Z0-9]/';
    $valid = true;
    if(preg_match($preg,$name)){
        $valid = false;
    }
    $str_len = strlen($name);
    if ($str_len<6 or $str_len>30){
        $valid = false;
    }
    return $valid;
}

//不合法
var_dump(check_user('sgwe'));
var_dump(check_user('sgwe451wf5wegwegwegwe5gwe54g6w4eg6we4g6w6eg6w4g22'));
var_dump(check_user('sgzwe#g22'));
var_dump(check_user('sgzwe g22'));
var_dump(check_user('你好sgwe#g22'));

//合法
var_dump(check_user('512948'));
var_dump(check_user('sgwe25'));
var_dump(check_user('sgwe25566489489489415178wqwfwe'));
var_dump(check_user('SG458EGEGEGdfsg'));
var_dump(check_user('dfsdfsfs'));