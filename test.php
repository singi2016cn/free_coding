<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/12/25
 * Time: 15:19
 */

$str = 'http://cdn-image.conzhu.com/d/file/p/2018-12-25/Microsoft Word_695eeb7d5612e071afdf20e5c7cd810a.docx';
var_dump(basename($str));

function get_original_name_from_url($url){
    $file_name = basename($url);
    $pos = stripos($file_name,'_');
    if ($pos !== false){
        return substr($file_name,0,$pos);
    }else{
        return $file_name;
    }
}

var_dump(get_original_name_from_url($str));