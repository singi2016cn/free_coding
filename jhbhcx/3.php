<?php

/**
 * 3DES加密
 * @param string $data 待加密的数据
 * @param string $key 加密密钥
 * @return string
 */
function encrypt_3des($data, $key)
{
    return base64_encode(openssl_encrypt($data, "des-ede3", $key));
}

/**
 * 3DES解密
 * @param string $data 待解密的数据
 * @param string $key 解密密钥
 * @return string
 */
function decrypt_3des($data, $key)
{
    return openssl_decrypt(base64_decode($data), "des-ede3", $key);
}


$content = 'singi';
$key = 'wegwgwg';
$ret = encrypt_3des($content,$key);
var_dump($ret);
var_dump(decrypt_3des($ret,$key));