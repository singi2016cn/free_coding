<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/3/1
 * Time: 18:21
 */

class Crypto
{
    private $key;

    public function __construct($key)
    {
        $key2 = pack('H*',$key);
        $this->key = $key2 . substr($key2, 0 , 8);
    }

    public function encrypt($text) {
        $data = mb_convert_encoding($text,'utf-8','auto');
        $len = strlen($data);
        $x = 8 - $len % 8;
        for ($i=0;$i<$x;$i++){
            $data .= chr(0);
        }
        $encrypt_text = openssl_encrypt($data, "des-ede3", $this->key, OPENSSL_ZERO_PADDING);
        return $encrypt_text;
    }

    public function decrypt($data,$isTrim='') {
        $clear_text = openssl_decrypt(base64_decode($data), "des-ede3", $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
        return strtoupper(unpack('H32key',$clear_text)['key']);
    }
}