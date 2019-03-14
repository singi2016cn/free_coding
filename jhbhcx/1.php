<?php
header("Content-type:text/html;charset=utf-8");


class Md5RSA{

    /**
     * 利用约定数据和私钥生成数字签名
     * @param $data 待签数据
     * @return String 返回签名
     */
    public function sign($data='',$private_key='')
    {
        if (empty($data))
        {
            return False;
        }

        $private_key = file_get_contents(dirname(__FILE__).'/pkcs8_zhpetro.key');

        $pkeyid = openssl_pkey_get_private($private_key);
        if (empty($pkeyid))
        {
            echo "private key resource identifier False!";
            return False;
        }
        $verify = openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyid);
        return $signature;
    }

    /**
     * 利用公钥和数字签名以及约定数据验证合法性
     * @param $data 待验证数据
     * @param $signature 数字签名
     * @return -1:error验证错误 1:correct验证成功 0:incorrect验证失败
     */
    public function isValid($data='', $signature='')
    {
        if (empty($data) || empty($signature))
        {
            return False;
        }

        $public_key = file_get_contents(dirname(__FILE__).'/ccbszcert.pem');
        if (empty($public_key))
        {
            echo "Public Key error!";
            return False;
        }

        $pkeyid = openssl_pkey_get_public($public_key);
        if (empty($pkeyid))
        {
            echo "public key resource identifier False!";
            return False;
        }

        $ret = openssl_verify($data, $signature, $pkeyid, OPENSSL_ALGO_MD5);
        if($ret == 1){
            return true;
        }else{
            return false;
        }
    }

}


//首先检测是否支持curl
if (!extension_loaded("curl")) {
    trigger_error("对不起，请开启curl功能模块！", E_USER_ERROR);
}

$xx='<Req>
<TxCode>交易代码</TxCode>
<TxDate>交易日期</TxDate>
<TxTime>交易时间</TxTime>
<TraceNo>系统跟踪号</TraceNo>
 </Req>';
$gzw=new Md5RSA();
$v=$gzw->sign($xx);
echo base64_encode($v) ;

$xmldata='<?xml version="1.0" encoding = "GB18030"?>
<Root>
<Head>
<TxCode>Z001</TxCode>
<OrgNo>105584049990001</OrgNo>
<TraceNo>333705661205A5E3D950933325240713</TraceNo>
<SecNo>07</SecNo>
<SignData>'.base64_encode($v).'</SignData>
</Head>
 <Req>
<TxCode>交易代码</TxCode>
<TxDate>交易日期</TxDate>
<TxTime>交易时间</TxTime>
<TraceNo>系统跟踪号</TraceNo>
 </Req>
</Root>';

//初始一个curl会话
$curl = curl_init();

//设置url
curl_setopt($curl, CURLOPT_URL,"http://usps.adh123.cn/v2b/fpay_adapter/fpayServlet");

//设置发送方式：post
curl_setopt($curl, CURLOPT_POST, true);

//设置发送数据
curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);

//TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

//执行cURL会话 ( 返回的数据为xml )
$return_xml = curl_exec($curl);

//关闭cURL资源，并且释放系统资源
curl_close($curl);
//$return_xml=mb_convert_encoding($return_xml, 'utf-8','GB18030');
//echo $return_xml;
//exit;

$xml = simplexml_load_string($return_xml);
$value_array = json_decode(json_encode($xml),TRUE);


//echo "<pre>";
print_r($value_array);
//print_r($return_xml);
//echo $return_xml;
?>