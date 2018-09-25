<?php

define('SDK_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
require_once SDK_PATH.'phpseclib1.0.11/Crypt/RSA.php';
require_once SDK_PATH.'phpseclib1.0.11/Math/BigInteger.php';

if ($_POST['config'] == 2){
    require_once SDK_PATH.'config2.php';
}else{
    require_once SDK_PATH.'config.php';
}


if ($_GET['act'] == 'need_guarantee'){
    //投保人点击我要保函
    need_guarantee($_POST);
}elseif($_GET['act'] == 'search_guarantee'){
    var_dump(search_guarantee($_POST));
}

/**
 * 查询保函
 * @param $data
 * @return bool|int|mixed
 */
function search_guarantee($data){
    $ret = request_api($data,'search_guarantee');
    return $ret;
}

/**
 * 投保人点击我要保函
 * @param $data
 */
function need_guarantee($data){
    //获取用户token
    $ret_data_token = request_api($data,'get_token');
    $ret_data_token = json_decode($ret_data_token,true);
    if ($ret_data_token['status']==200 && $ret_data_token['data']['token']){
        $token = urlencode($ret_data_token['data']['token']);
        //再次获取access_token
        $ret_data_api_token = get_api_token();
        $ret_data_api_token = json_decode($ret_data_api_token,true);
        if ($ret_data_api_token['status']==200 && $ret_data_api_token['data']['access_token']){
            $redirect_url =REQUEST_API_URL.'?enews=get_guarantee_from_ebg&access_token='.urlencode($ret_data_api_token['data']['access_token']).'&token=';//重定向地址
            header('Location:'.$redirect_url.$token);
        }else{
            exit($ret_data_api_token['msg']);
        }
    }else{
        exit($ret_data_token['msg']);
    }
}

/**
 * 发起请求
 * @param $request_param
 * @param $auth_api
 * @return bool|int|mixed
 */
function request_api($request_param, $auth_api){
    $ret_data = get_api_token();
    $ret_data = json_decode($ret_data,true);
    if ($ret_data['status']==200 && $ret_data['data']['access_token']) {
        $request_param['enews'] = $auth_api;
        $request_param['timestamp'] = time();
        $request_param['sign'] = sign($request_param);
        $curl_data['param'] = rsa_encrypt($request_param,false);
        if (!$curl_data['param']) {
            exit('加密失败');
        }
        $curl_data['access_token'] = urlencode($ret_data['data']['access_token']);
        return curl_post(REQUEST_API_URL, $curl_data);
    }else{
        exit($ret_data['msg']);
    }
}

/**
 * 1 对除签名外的所有请求参数按key做的升序排列,value无需编码。
 * 例如：有c=3,b=2,a=1 三个参，另加上时间戳后， 按key排序后为：a=1，b=2，c=3，timestamp=12345678。
 * 2 把参数名和参数值连接成字符串，得到拼装字符：a1b2c3_timestamp12345678
 * 3 用申请到的api_key 连接到接拼装字符串头部和尾部，然后进行32位MD5加密，最后将到得MD5加密摘要转化成大写。
 * @param $data
 * @return string
 */
function sign($data){
    if (!$data) return false;
    $data = array_filter($data);
    ksort($data);
    $data_str = '';
    foreach($data as $i=>$k){
        $data_str .=$i.$k;
    }
    $data_str = API_SECRET.$data_str.API_SECRET;
    return strtoupper(md5($data_str));
}

/**
 * 获取access_token
 * @return bool|int|mixed
 */
function get_api_token(){
    if (!API_KEY){
        return false;
    }
    if (!API_SECRET){
        return false;
    }
    //签名
    $request_data['api_key'] = API_KEY;
    $request_data['api_secret'] = API_SECRET;
    $request_data['enews'] = 'get_api_token';
    $request_data['timestamp'] = time();
    $request_data['sign'] = sign($request_data);
    //加密
    $param = rsa_encrypt($request_data);
    if (!$param) {
        return false;
    }
    //发送请求
    return curl_post(REQUEST_API_URL,array('param'=>$param));
}

/**
 * rsa加密
 * @param $plaintext
 * @param boolean $type
 * @return string
 */
function rsa_encrypt($plaintext,$type=true){
    $rsa = new \Crypt_RSA();
    if ($type){
        $rsa->setHash(API_RSA_HASH);
        $rsa->loadKey(API_RSA_PK);
    }else{
        $rsa->setHash(RSA_HASH);
        $rsa->loadKey(RSA_PK);
    }
    return urlencode(base64_encode($rsa->encrypt(json_encode($plaintext))));
}

/**
 * curl post 请求
 * @param $url
 * @param $data
 * @return bool|int|mixed
 */
function curl_post($url, $data){
    if (!$data){
        return false;
    }
    $curl_data_arr = [];
    foreach($data as $k=>$v){
        $curl_data_arr[] = $k.'='.$v;
    }
    $curl_data_str = '';
    if ($curl_data_arr) {
        $curl_data_str = implode('&',$curl_data_arr);
    }
    if (!$curl_data_str) {
        exit('请求参数错误');
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_data_str);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    $output = curl_exec($curl);
    if($output === false) {
        return curl_errno($curl);
    }
    curl_close($curl);
    return $output;
//    return json_decode($output,true);
}