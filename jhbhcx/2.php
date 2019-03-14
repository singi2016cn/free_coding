<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/3/1
 * Time: 10:37
 */

date_default_timezone_set('PRC');

$url = "http://usps.adh123.cn/v2b/fpay_adapter/fpayServlet";
$privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDfChLKAuyVT9dIp6YBMtbVZ9zqF0KKTYMz1jHSL72laAjcUscm
wrPi2dZhRvaUIDqLVnkn41a42a309F73nhT6FnNEwzRltUKyQAFjr82+h70dIKLg
YZPJ90u4DLXntIt/A+DmmlSAEOJ2Ii3odZL20+qwXTEnciqzoo8yyzWvqQIDAQAB
AoGBAIBVXnxH9HLxTrEqbZUw+HYwXed/3LSRZxXTgDUtbRSYaMjFBHwj+bdkmjq8
xzJx3N3BstKlt4eDENnqJh6aIfy0ryoQusw8pusHg5n/4yvctjvb0vbeKIqQ6HWz
Q1z8ZEIyOVI9osqWMaj1NtO2cLD/FBM8Fv+QpL1NHIehMnVFAkEA/SiNQvsmDk/w
pWdQRY7uphI2JeUx90J1+GM2YJCbZanfeYyDJUAgpZqh3xCHDV9gVHWn1mrajBls
3iwrgt9mNwJBAOGK+YsX7eXi7wSsTOSJxqgCaAqgxQ/fxfF1mfi3a06+cILG5jsa
Tf4DP1Kn5nkcGHb9snadmM6abLzyig7CqR8CQA/Uc1DW54BJm2CcXzaaQ237AYvJ
EyDE9R99BK9xG2Z2AtVU5fZ0VhZE3w5VuDwr1JgzazVLJTNRe69Az8+1LG0CQQCI
vGyVeyhrWq11s8/aLf0WFn9lEhrmiM/El3uFYg3Ya3RilJs38bae7PES91+uxO3A
FgaACwN9HfoWgyRX1oQZAkA8p5AosSTqquYgbBVVPRhTu09wrqjNqks62uaHmJvV
ObGSNIMVAtEWSAhuRRnNUxYWn3fnY4EPNqO/V021bp5X
-----END RSA PRIVATE KEY-----';
$baseKey = "333705661205A5E3D950933325240713";//基本解密密钥
$OrgNo = '105584049990002';//商户编号

//--- 发起Z001(保函业务密钥同步)请求 开始 ---

$TxCode = 'Z001';
$TxDate = date('Ymd');
$TxTime = date('His');
$traceNo = getTraceNo(); //系统跟踪号

$req = "<TxCode>{$TxCode}</TxCode><TxDate>{$TxDate}</TxDate><TxTime>{$TxTime}</TxTime><TraceNo>{$traceNo}</TraceNo>";
$signData = sign($req, $privateKey);//签名
$head = "<Head><TxCode>{$TxCode}</TxCode><OrgNo>{$OrgNo}</OrgNo><TraceNo>{$traceNo}</TraceNo><SecNo></SecNo><SignData>{$signData}</SignData></Head>";
$xml = "<?xml version=\"1.0\" encoding=\"GB18030\" ?><Root>{$head}<Req>{$req}</Req></Root>";
$resp = curl_post($url, $xml);

//--- 发起Z001(保函业务密钥同步)请求 结束 ---

if ($resp['Resp']['RetCode'] == '000000') {

    //--- 发起B002(保函业务信息查询)请求 开始 ---

    $respDck = $resp['Resp']['Dck'];
    $workKeyList = array_filter(explode('==', $respDck));

    $sec = mt_rand(0,9);//随机获取一组密钥
    $reqKey = decrypt_3des($workKeyList[$sec], $baseKey,true);//解密得到加密密钥

    $TxCode = 'B002';
    $TxDate = date('Ymd');
    $TxTime = date('His');
    $traceNo = getTraceNo();
    $GuaraNum = '888888';//保函编号
    $sec += 1;//下标+1得到密钥组数
    // 拼接报文
    $req2 = "<TxCode>{$TxCode}</TxCode><TxDate>{$TxDate}</TxDate><TxTime>{$TxTime}</TxTime><TraceNo>{$traceNo}</TraceNo><GuaraNum>{$GuaraNum}</GuaraNum>";
    $req2 = encrypt_3des($req2, $reqKey);// 对Req进行加密
    $signData2 = sign($req2, $privateKey);// 对加密后的Req进行加签
    $head2 = "<Head><TxCode>{$TxCode}</TxCode><OrgNo>{$OrgNo}</OrgNo><TraceNo>{$traceNo}</TraceNo><SecNo>{$sec}</SecNo><SignData>{$signData2}</SignData></Head>";
    $xml2 = "<?xml version=\"1.0\" encoding=\"GB18030\" ?><Root>{$head2}<Req>{$req2}</Req></Root>";
    $ret2 = curl_post($url, $xml2);
    var_dump($ret2);
    if ($ret2['Resp']) {
        $req3Key = decrypt_3des($workKeyList[intval($ret2['Head']['SecNo'])-1], $baseKey, true);
        $decryptedXml = decrypt_3des($ret2['Resp'],$req3Key);
        $decryptedData = xml2array('<xml>'.trim(mb_convert_encoding($decryptedXml,'utf-8','gbk')).'</xml>');
        var_dump($decryptedData);
        if ($decryptedData['RetCode'] == '00000'){
            //获取返回数据成功
        }else{
            var_dump($decryptedData['RetMsg'] . '[' . $decryptedData['RetCode'] . ']');
        }
    }

    //--- 发起B002(保函业务信息查询)请求 结束 ---

} else {
    var_dump($resp['Resp']['RetMsg'] . '[' . $resp['Resp']['RetCode'] . ']');
}

/**
 * 获取系统跟踪单号
 * @return string
 */
function getTraceNo(){
    return 'CONZHU'.date('YmdHis').mt_rand(10000, 99999);
}

/**
 * 发起post请求
 * @param string $url 请求地址
 * @param string $data 请求数据
 * @return mixed
 */
function curl_post($url, $data)
{
    //创建一个新cURL资源
    $curl = curl_init();
    //设置URL和相应的选项
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
    //执行curl，抓取URL并把它传递给浏览器
    $output = curl_exec($curl);

    //关闭cURL资源，并且释放系统资源
    curl_close($curl);
    if ($output){
        return xml2array($output);
    }else{
        return false;
    }
}

/**
 * xml转array
 * @param $xml
 * @return mixed
 */
function xml2array($xml){
    return json_decode(json_encode(simplexml_load_string($xml)), TRUE);
}

/**
 * 使用私钥签名
 * @param string $input 待签名的数据
 * @param string $private_key RSA私钥
 * @return string
 */
function sign($input, $private_key)
{
    $private_key_res = openssl_pkey_get_private($private_key);
    openssl_sign($input, $sign, $private_key_res, OPENSSL_ALGO_MD5);
    openssl_free_key($private_key_res);
    $sign = base64_encode($sign);//最终的签名
    return $sign;
}

/**
 * 3DES加密
 * @param string $input 待加密的数据
 * @param string $key 加密密钥
 * @return string
 */
function encrypt_3des($input, $key)
{
    $key2 = pack('H*', $key);
    $key3 = $key2 . substr($key2, 0, 8);
    $data = mb_convert_encoding($input, 'utf-8', 'auto');
    $len = strlen($data);
    $x = 8 - $len % 8;
    for ($i = 0; $i < $x; $i++) {
        $data .= chr(0);
    }
    $encrypt_text = openssl_encrypt($data, "des-ede3", $key3, OPENSSL_ZERO_PADDING);
    return $encrypt_text;
}

/**
 * 3DES解密
 * @param string $input 待解密的数据
 * @param string $key 解密密钥
 * @param bool $isUnpack 是否unpack,默认否(Z001请求时需要为true)
 * @return string
 */
function decrypt_3des($input, $key, $isUnpack=false)
{
    $key2 = pack('H*', $key);
    $key3 = $key2 . substr($key2, 0, 8);
    $decrypt_text = openssl_decrypt(base64_decode($input), "des-ede3", $key3, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
    if ($isUnpack){
        $decrypt_text = strtoupper(unpack('H32key', $decrypt_text)['key']);
    }
    return $decrypt_text;
}