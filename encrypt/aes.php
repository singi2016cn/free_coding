<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/6/11
 * Time: 10:51
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use phpseclib\Crypt\AES;

$aes = new AES();

$aes->setKey('conzhu');
$aes->setKeyLength(224);

$data = $aes->encrypt('singi');

var_dump(base64_encode($data));

var_dump($aes->decrypt($data));