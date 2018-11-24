<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/11/17
 * Time: 11:36
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
//获取RSA公钥
libxml_disable_entity_loader(false);

$url = 'https://www.elecredit.com/element_credit/ws/EncryptedService?wsdl';
$client = new SoapClient($url, ['login' => "gzwl", 'password' => "4PSl47kS2zJP9tNfavIRczcnqDbCM8SH", 'stream_context' => stream_context_create([
    'ssl' => [
        'verify_peer' => false
    ],
    'https'=>[
        'curl_verify_ssl_peer'=>false,
        'curl_verify_ssl_host'=>false,
    ]
])]);
//$rsaPublicKey = $client->getPublicKey()->return;
//var_dump($rsaPublicKey);
//生成DES秘钥
$desKey = 'qinyi';
//使用 RSA 公钥对 DES 密钥加密获得 key
$rsaPublicKey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvcxapZAjkSI57Ogf39Ac
VX17krKJhPj6qWTgM/6IrENoRgx8D3W1Qv5e8W8DImnqA8bU8xBU4MHeodXepYW4
DLTu08RxqtgnpRIo0kXAUn0Uct6Kl/GlqUDfITfmaXg0l83caP/F9CsqVKgtSwCm
err5PwVO/mR6b8/4xM31jjDxPiwxaRfw2KQnB0a+4gXP54/x2VkO0o3Nv4E2xlwJ
4EqqQXwg25qzWneSIwTi2q2/fajcf60tY3Ou1QqU4RcAHoyW2EPQw3kaaecNp2Ba
iP4QmPtoQcooqe6/2uDAiYGlVvksBASoRls7jYCpW1X235KH6dMIYWG2MEqtbDkg
6wIDAQAB
-----END PUBLIC KEY-----';
$key = rsa_encrypt($desKey,$rsaPublicKey);
var_dump($key);
//使用 DES 密钥对请求报文加密获得 message （加密报文处理空格，防止空格在网络传输中转码造成无法解密）
$data = '<request><meta><username>gzwl</username><password>4PSl47kS2zJP9tNfavIRczcnqDbCM8SH</password><service>ys_saic_single</service></meta><data><ent_name>峨眉山市永忠汽车服务有限公司</ent_name></data></request>';
$message = SecretUtilTools::encryptForDES($data,$desKey);
var_dump($message);
/*$message = 'RcjIiM3IrLskT/cKXr+JKuJ0q4p+dqS0sr16hhV8sNwgykcsY/Ou0uCuMiL6g7tIAKkcBC1QzjzUYM7BzxU1jnNiZ+XkpSWuAvEmiOlQAiym5VgszSg8OFUmoR9IJ799hmham/d3Tqry70JncBDEgFak1z1dClzEdPJVxaDDR0vLux7ykV+X4cBsl+bIftlNOHuYq/bupMcwM/YbLaahtNfL9sUdeKBxBKaV7sus6GzeFdjycfg6L1+k8RXSWl0TAanEaDRfGp+E3cpF/nxbRS9yci+jlGjJBmqH+Il+f/Q=';
var_dump($message);*/
//调用 invokeService 获得加密的响应报文
$encryptData = $client->invokeService($key,urlencode($message));
var_dump($encryptData);
//使用 DES 密钥解密响应报文
$decryptData = SecretUtilTools::decryptForDES($encryptData,$desKey);
var_dump($decryptData);

/**
* rsa加密,每245个未加密字符加密后的长度为366个字节
* POST最大为8M的话,最多可发送5.3M字节数据
* @param $plaintext
* @param $publicKey
* @return string
    */
function rsa_encrypt($plaintext,$publicKey){
//公钥加密
    $crypted_length = 245;//明文分段加密长度为245个字符(2048/8)
    $crypto = '';
    if ($plaintext){
        $plaintext = json_encode($plaintext);
        foreach (str_split($plaintext, $crypted_length) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $publicKey);
            $crypto .= $encryptData;
        }
        $crypto = base64_encode($crypto);
    }
    return $crypto;
}

class SecretUtilTools
{
    /**
     * 解密函数
     * 算法：des
     * 加密模式：ecb
     * 补齐方法：PKCS5
     * @param string $input
     * @return string
     */
    public static  function encryptForDES($input,$key)
    {
        $size = mcrypt_get_block_size('des','cbc');
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open('des', '', 'cbc', '');

        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }


    public static  function decryptForDES($input,$key)
    {
        $input = base64_decode($input);
        $size = mcrypt_get_block_size('des','cbc');
        $td = mcrypt_module_open('des', '', 'cbc', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mdecrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = self::pkcs5_unpad($data, $size);
        return $data;
    }


    public static  function pkcs5_pad ($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static  function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
        {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
        {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}
