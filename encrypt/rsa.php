<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/17
 * Time: 10:16
 *
 * 参考:
 * http://www.jb51.net/article/114960.htm
 * http://web.chacuo.net/netrsakeypair
 */

/**
 * RSA私钥加密
 * @param string $private_key 私钥
 * @param string $data 要加密的字符串
 * @return string $encrypted 返回加密后的字符串
 * @author mosishu
 */
function privateEncrypt($private_key,$data){
    $encrypted = '';
    $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
    //最大允许加密长度为117，得分段加密
    $plainData = str_split($data, 100);//生成密钥位数 1024 bit key
    foreach($plainData as $chunk){
        $partialEncrypted = '';
        $encryptionOk = openssl_private_encrypt($chunk,$partialEncrypted,$pi_key);//私钥加密
        if($encryptionOk === false){
            return false;
        }
        $encrypted .= $partialEncrypted;
    }

    $encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
    return $encrypted;
}

/**
 * RSA公钥解密(私钥加密的内容通过公钥可以解密出来)
 * @param string $public_key 公钥
 * @param string $data 私钥加密后的字符串
 * @return string $decrypted 返回解密后的字符串
 * @author mosishu
 */
function publicDecrypt($public_key,$data){
    $decrypted = '';
    $pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
    $plainData = str_split(base64_decode($data), 128);//生成密钥位数 1024 bit key
    foreach($plainData as $chunk){
        $str = '';
        $decryptionOk = openssl_public_decrypt($chunk,$str,$pu_key);//公钥解密
        if($decryptionOk === false){
            return false;
        }
        $decrypted .= $str;
    }
    return $decrypted;
}

//RSA公钥加密
function publicEncrypt($public_key,$data){
    $encrypted = '';
    $pu_key = openssl_pkey_get_public($public_key);
    $plainData = str_split($data, 100);
    foreach($plainData as $chunk){
        $partialEncrypted = '';
        $encryptionOk = openssl_public_encrypt($chunk,$partialEncrypted,$pu_key);//公钥加密
        if($encryptionOk === false){
            return false;
        }
        $encrypted .= $partialEncrypted;
    }
    $encrypted = base64_encode($encrypted);
    return $encrypted;
}

//RSA私钥解密
function privateDecrypt($private_key,$data){
    $decrypted = '';
    $pi_key = openssl_pkey_get_private($private_key);
    $plainData = str_split(base64_decode($data), 128);
    foreach($plainData as $chunk){
        $str = '';
        $decryptionOk = openssl_private_decrypt($chunk,$str,$pi_key);//私钥解密
        if($decryptionOk === false){
            return false;
        }
        $decrypted .= $str;
    }
    return $decrypted;
}

$pk = <<<PK
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIoCnpLJIGOkfj8s1PPM+ipgjM
8nWighYBfI1Htd2YZsg0iMeekte++SalKqB7udJ8CLaz3LSw+LfAHK9VV0gKgTgr
JKPKMVo/Cq4FUq4FpAc3MY6hcpbaeF5uZeSix4e7LS2cIP3JEXtFqGoRihk6TBiC
aByoT1tqihA4ZzsJTQIDAQAB
-----END PUBLIC KEY-----
";
PK;

$sk = <<<SK
-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAMigKekskgY6R+Py
zU88z6KmCMzydaKCFgF8jUe13ZhmyDSIx56S1775JqUqoHu50nwItrPctLD4t8Ac
r1VXSAqBOCsko8oxWj8KrgVSrgWkBzcxjqFyltp4Xm5l5KLHh7stLZwg/ckRe0Wo
ahGKGTpMGIJoHKhPW2qKEDhnOwlNAgMBAAECgYEAiflUOW8UkcHdFRAzUE4jAdmr
7b7FVdie57DPvMR/PENbZn22wwB77XONt0NEkTJvZivb8oBTp+20+SMBFfDrsk2R
1hoxKwtePDaQ/ZOZcu2CemXXCtjMtN0QKbp3Eum2zof//JZaefF3YA5DPjmWzN9c
kmo5pn1iRnZmzdezBMUCQQDvbd11llY1McleIMVu0iePqM/2iqwDYNvDyHwd9a2d
xDanypJ2lyAm3QBRProo5MPdEBavCssK2gqr/mm1nf27AkEA1oLKEvSLlQDYNHFf
Jieifd8AxL1mFdb9HFRWuKQvWXoKbSoqv9Bg7e2GjcZAvY92u9zc2eeyuv1vswIu
N0kglwJAeO/Gj9F7A+186BWnhB2UrAS53q8WybIP72mB/+QLaQgmD0TMDCizrc2t
jlwfze4XXL72pj3OZ1HRWCTuojUGZwJARmvbgQuNQ1ZqpBTQAsuiOaZbLztqZyMU
Jxe8/JLSBJLfF2VvcVcsaw++S47ZNCID/bqNOnApKeAHqoG9wGKecwJAUNl5UVLR
G4ZILOPuRhuP2Eh+pGUYwXPQmJgpVMBkeMu2dLSB0f3DKCC/Kd8wGwng/onbyfdH
PvmhA9mu9tqlAg==
-----END PRIVATE KEY-----
SK;

$data = 'singi';

$e_data = publicEncrypt($pk,$data);
var_dump($e_data);

$d_data = privateDecrypt($sk,$e_data);

var_dump($d_data);
