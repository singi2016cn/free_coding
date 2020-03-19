<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/8/29
 * Time: 16:35
 */


/**
 * curl get 请求
 * @param string $url 请求地址
 * @param array $data 请求数据
 * @param array $headers 请求header头数据
 * @return int|mixed
 */
function curl_get_http($url, $data=[],$headers=[]){
    $curl = curl_init();
    if ($data){
        $url .= '?'.http_build_query($data);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    $output = curl_exec($curl);
    if($output === false) {
        throw new Exception(sprintf("%s[%s]",curl_errno($curl), curl_error($curl)));
    }
    curl_close($curl);
    return $output;
}

/**
　* 驼峰命名转下划线命名
　* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
　*/
function uncamelize($camelCaps,$separator='_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

var_dump(uncamelize('SingiName'));