<?php
/**
 * 普通变量示例
 */
function normal_var()
{
    $normal_var = 0;
    var_dump($normal_var);
    $normal_var++;
}

/**
 * 静态变量示例
 */
function static_var()
{
    static $static_var = 0;
    var_dump($static_var);
    $static_var++;
}

normal_var();//输出0
normal_var();//还是输出0

static_var();//输出0
static_var();//会输出1
