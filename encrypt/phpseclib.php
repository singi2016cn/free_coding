<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/17
 * Time: 14:26
 *
 * 参考:
 * https://packagist.org/packages/phpseclib/phpseclib
 * http://phpseclib.sourceforge.net/rsa/intro.html
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use phpseclib\Crypt\RSA;

$rsa = new RSA();

//$rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
//$rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_PKCS1);

//define('CRYPT_RSA_EXPONENT', 65537);
//define('CRYPT_RSA_SMALLEST_PRIME', 64); // makes it so multi-prime RSA is used
extract($rsa->createKey()); // == $rsa->createKey(1024) where 1024 is the key size

$createKeyRet = $rsa->createKey();
$pk = $createKeyRet['publickey'];
$sk = $createKeyRet['privatekey'];

$plaintext = 'singi';

$rsa->loadKey($pk); // public key
$ciphertext = $rsa->encrypt($plaintext);

print_r(base64_encode($ciphertext));

$rsa->loadKey($sk); // private key
$rsa->decrypt($ciphertext);
