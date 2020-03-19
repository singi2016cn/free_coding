<?php
/**
 * Created by PhpStorm.
 * User: chenliefu
 * Date: 2019/7/26
 * Time: 14:06
 */
function load_class($class){
    static $loaded = [];
    $class_real = './lib/'.str_replace('\\', '/', $class).'.php';
    if( !isset($loaded[$class]) && is_file($class_real) ){
        require $class_real;
        $loaded[$class] = true;
    }
}
spl_autoload_register('load_class');