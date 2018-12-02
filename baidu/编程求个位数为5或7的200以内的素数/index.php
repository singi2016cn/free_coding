<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/2
 * Time: 20:18
 * 编程求个位数为5或7的200以内的素数
 */

/**
 * 判断是否为素数（质数）
 * 基本判断思路:在一般领域，对正整数n，如果用2到 之间的所有整数去除，均无法整除，则n为质数。
 * 引用于 https://blog.csdn.net/qq_27926653/article/details/80311506
 */
function isPrime($n) {
    if ($n <= 3) {
        return $n > 1;
    } else if ($n % 2 === 0 || $n % 3 === 0) { // 排除能被2整除的数(2x)和被3整除的数(3x)
        return false;
    } else { // 排除能被6x+1和6x+5整除的数
        for ($i = 5; $i * $i <= $n; $i += 6) {
            if ($n % $i === 0 || $n % ($i + 2) === 0) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 判断一个数的个位是5或7
 * @param $n
 * @return bool
 */
function singleDigitIs5Or7($n){
    $singleDigit = strval($n)[strlen($n)-1];
    $ret = false;
    if ($singleDigit==5 || $singleDigit==7){
        $ret = true;
    }
    return $ret;
}

$limitNumber = 200;
$ret = [];
for ($i=2;$i<=$limitNumber;$i++){
    if (isPrime($i) && singleDigitIs5Or7($i)){
        $ret[] = $i;
    }
}

var_dump(implode(',',$ret));