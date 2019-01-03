<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 22:58
 */

$name = [
    'a' => [
        ['张三', '10', 'abc'],
        ['李四', '20', 'Abc'],
        ['王五', '30', 'ABC'],
    ],
    'b' => [
        ['张三', '10', 'abc'],
        ['李四', '20', 'Abc'],
        ['王五', '30', 'ABC'],
    ],
    'c' => [
        ['张三', '10', 'abc'],
        ['李四', '20', 'Abc'],
        ['王五', '30', 'ABC'],
    ],
];
foreach ($name as $value) {
    /*
     * $value 代表
     * 'a' => [
        ['张三', '10', 'abc'],
        ['李四', '20', 'Abc'],
        ['王五', '30', 'ABC'],
    ],
     * */
    foreach ($value as $a) {
        /*
         * $a 代表
         * ['张三', '10', 'abc']
         * */
        foreach ($a as $b) {
            /*
             * $b代表 '张三'
             * */
            echo $b;
        }
        echo "<br />";
    }
}