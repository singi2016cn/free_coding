<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/12/6
 * Time: 11:00
 * 爬去京东单个图书信息
 * https://item.jd.com/11993134.html,根据该网站末尾的id获取对于的图书信息
 */
require_once dirname(__DIR__).'/vendor/autoload.php';
require_once dirname(__DIR__).'/config/db.php';

use Goutte\Client;

//配置
define('INIT_ITEM_ID',11000000);//初始item_id
define('RESOURCE_URL','https://item.jd.com/ITEM_ID.html');//资源url
define('RESOURCE_MORE_INFO_URL','https://dx.3.cn/desc/ITEM_ID?cdn=2&callback=showdesc');//更多信息资源url
//详细信息下标转换
define('ZH2EN',[
    '出版社'=>'publishing_company',
    'ISBN'=>'isbn',
    '版次'=>'edition',
    '商品编码'=>'commodity_code',
    '包装'=>'packing',
    '丛书名'=>'series_name',
    '开本'=>'format',
    '出版时间'=>'publish_date',
    '用纸'=>'form',
    '页数'=>'page',
    '正文语种'=>'language',
]);

var_dump('start work...');
while (1){
    $book = start(0);
    //写入数据库
    if ($book){
        $db->insert('books',$book);
        var_dump('work '.$book['resource_url'].' is done.');
    }else{
        var_dump('work '.$book['resource_url'].' pass.');
    }
}

function start($is_test=true){
    $book['from_platform'] = 'jd';
    //开始发起请求
    $client = new Client();
    $item_id = get_item_id($is_test);
    $resource_url = str_replace('ITEM_ID',$item_id,RESOURCE_URL);
    $book['resource_url'] = $resource_url;
    $crawler = $client->request('GET', $resource_url);
    //获取主要信息
    if ($crawler->filter('#name > div.sku-name')->count()<=0){
        return null;
    }
    $book['title'] = trim($crawler->filter('#name > div.sku-name')->text());
    $book['author'] = trim($crawler->filter('#p-author')->text());
    $book['cover'] = trim($crawler->filter('#spec-n1 > img')->image()->getUri());
    $book_categories = $crawler->filter('#crumb-wrap > div > div.crumb.fl.clearfix > div')->each(function($node){
        return trim($node->text());
    });
    if ($book_categories) $book['category'] = implode('',$book_categories);
    //获取详细信息
    $book_param = $crawler->filter('#parameter2 > li')->each(function($node){
        return $node->text();
    });
    if ($book_param){
        foreach($book_param as $v){
            $v_arr = explode('：',$v);
            if (isset($v_arr[0]) && isset(ZH2EN[$v_arr[0]])) $book[ZH2EN[$v_arr[0]]] = trim($v_arr[1]);
        }
    }
    //更多信息
    $resource_more_file_url = str_replace('ITEM_ID',$item_id,RESOURCE_MORE_INFO_URL);
    $book['resource_more_file_url'] = $resource_more_file_url;
    $crawler_more_info = $client->request('GET', $resource_more_file_url);
    $book_more_info = $client->getInternalResponse()->getContent();
    if ($book_more_info){
        $book_more_info_html = json_decode(utf8_encode(substr($book_more_info,9,-1)),true)['content'];
    }
    if ($book_more_info_html){
        $crawler_more_info->addHtmlContent($book_more_info_html);
        $book['content_profile'] = get_text($crawler_more_info,'#detail-tag-id-3 div.book-detail-content');
        $book['author_profile'] = get_text($crawler_more_info,'#detail-tag-id-4 div.book-detail-content');
        $book['table_of_contents'] = get_text($crawler_more_info,'#detail-tag-id-6 div.book-detail-content');
    }
    return $book;
}

/**
 * 防止致命错误,命令中断
 * @param $crawler
 * @param $selecter
 * @return null|string
 */
function get_text($crawler, $selecter){
    $crawler_filter = $crawler->filter($selecter);
    if ($crawler_filter->count()){
        return trim($crawler_filter->text());
    }else{
        return null;
    }
}

/**
 * 获取item_id
 * @param bool $is_test
 * @return bool|int|string
 */
function get_item_id($is_test=true){
    if ($is_test) return INIT_ITEM_ID;
    if (!file_exists(get_item_log_file())){
        return init_item_id();
    }
    $item_id = file_get_contents(get_item_log_file());
    if ($item_id){
        $item_id += 1;
        file_put_contents(get_item_log_file(),$item_id);
        return $item_id;
    }else{
        return init_item_id();
    }
}

/**
 * 初始化item_id
 * @return int
 */
function init_item_id(){
    $item_id = INIT_ITEM_ID;
    file_put_contents(get_item_log_file(),$item_id);
    return $item_id;
}

/**
 * 获取item_id.log文件
 * @return string
 */
function get_item_log_file(){
    return __DIR__.DIRECTORY_SEPARATOR.'jd_item_id.log';
}

/**
 * Json对象转成php可以识别的Json格式(json_decode可以使用的字符串)
 * @param $js_obj_str
 * @return null|string|string[]
 */
function JsObjToJsonDecodeEnableStr($js_obj_str){
return preg_replace(["/([a-zA-Z_]+[a-zA-Z0-9_]*)\s*:/", "/:\s*'(.*?)'/"], ['"\1":', ': "\1"'], $js_obj_str);
}