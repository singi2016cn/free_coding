<?php
$input = '你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物你是个文物';
if (mb_strlen($input) > 50){
    $ret = mb_substr($input,0,50,'UTF-8');
    $ret .= '...';
}else{
    $ret = $input;
}

var_dump($ret);

