<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/1/7
 * Time: 11:29
 */

class Rsa
{
    private $crypted_length = 245;//明文分段加密长度为245个字符(2048/8)

    /**
     * 获取公钥私钥
     * @return array
     */
    public function getPublicKeyAndPrivateKey(){
        $resource = openssl_pkey_new();
        openssl_pkey_export($resource, $privateKey);
        $detail = openssl_pkey_get_details($resource);
        return ['publicKey'=>$detail['key'],'privateKey'=>$privateKey];
    }

    /**
     * 公钥加密
     * @param $plaintext
     * @param $publicKey
     * @return string
     */
    public function encryptWithPublicKey($plaintext,$publicKey){
        $crypto = '';
        if ($plaintext){
            foreach (str_split($plaintext, $this->crypted_length) as $chunk) {
                openssl_public_encrypt($chunk, $encryptData, $publicKey);
                $crypto .= $encryptData;
            }
            $crypto = base64_encode($crypto);
        }
        return $crypto;
    }

    /**
     * 私钥解密
     * @param $encryptData
     * @param $privateKey
     * @return mixed
     */
    public function decryptWithPrivateKey($encryptData, $privateKey){
        $crypto = '';
        foreach (str_split(base64_decode($encryptData), 256) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $privateKey);
            $crypto .= $decryptData;
        }
        return $crypto;
    }
}

$rsa = new Rsa();
var_dump($rsa->getPublicKeyAndPrivateKey());