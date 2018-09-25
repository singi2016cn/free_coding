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
var_dump($plaintext);

$rsa->loadKey('-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCM/77+WI7K0xNCntEenfR116qA
xw6X8POQjUbT3G/xNWfvfQYyWCbu/v1Hx2UlTMvY52OV//K21NtAFB7xEKJ1qsV0
YoC38SzoPEQgt2wO/WRqZ7ePrIj8rpOMFL8EcaWFi3WZOdD4wGIFoitLGxolG5ZQ
fWJbvEKYPvv0xfHNGQIDAQAB
-----END PUBLIC KEY-----'); // public key
$rsa->setHash('sha256');
$ciphertext = $rsa->encrypt($plaintext);

print_r(base64_encode($ciphertext));

$rsa->setHash('sha256');
$rsa->setPassword('9aff280368beb8f8fb643b48a5239a4c');
$rsa->loadKey('-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,5B083687A5B6F5D0

aPnkjc6ioTYM6XbOhKihrtbNVUV/8POCu9qQTYCcpaH28turRzIfV7lgy6yIg6Wb
1kkHXRwq1/GAdVeDqutcQ+li/FnWQHJgpe/rfmAaQw/cLSOpjREh44Q2w2+7YCC3
A+Ns1Q4fmqfjzBB0ZmqvNxVzqHg3/vP176Hobkdu4LUAuicS0RFeiqDnglUH1JKs
QOGon7t8TG+lp4BeNggsoW7esnS6O9YGVWwGRDuHX2/FI/mt4Gqv/L1rV4DV3MyC
0WOpp6UbKcH5U1WFoPnRdFkfenXaddIz68TwDceUQYmifC6knoik9ifALiJFL4y5
INVPPvPEghtwR6lBLtl89cPlJnIvvtKjxQzzsTBoCe07hmwbS45o0LqCV4g1CYNz
+n5gr6v221mYOfpM/ilEuXv90m88U7/3zNtinIGCJ1qQJrKEZU+Mz09ypc/MK7lg
EOgNuvLnzJsAkTyToGLIqYdwSE/WTGlIE8+f3RmLdw4d+wgAt1clCqEWpGR2z6sM
qTHX2GPFzLB2V4XdFlsq0HQBsJm8pimlGUs+jX5jSMIoeWfXSTIVOPmIDU0vsmkG
/bgYkVC+OWnUb/yt8m6Fa9sNeMhGiHv6c8yklPo4kL5gqbNuVYdd9r503eMejZ63
gb30eI4sDDSDnKCsuvLC+nG0Bh9sx98qCCMlLEgXcH7ISS9UqdUN8434xBGE+trM
MEuspG5W0FH1tlY2fBzgF4MISPQG4GQkzfrLwzzXbFvBf2qBs44Ng/Cr1qJToFDY
fOkH5mBuaVDIiu5FYC95/0m3nEeHIYhFGitK7tDECdQ=
-----END RSA PRIVATE KEY-----'); // private key
$r = $rsa->decrypt($ciphertext);

var_dump($r);
