<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: client.php 1079 2011-04-02 07:29:36Z zhengqingpeng $
*/

if(!defined('UC_API')) {
	exit('Access denied');
}

error_reporting(0);

//define('IN_UC', TRUE);
define('UC_CLIENT_VERSION', '1.6.0');
define('UC_CLIENT_RELEASE', '20110501');
define('UC_ROOT', substr(__FILE__, 0, -10));
define('UC_DATADIR', UC_ROOT.'./data/');
define('UC_DATAURL', UC_API.'/data');
define('UC_API_FUNC', UC_CONNECT == 'mysql' ? 'uc_api_mysql' : 'uc_api_post');
$GLOBALS['uc_controls'] = array();

function uc_addslashes($string, $force = 0, $strip = FALSE) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = uc_addslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}

if(!function_exists('daddslashes')) {
	function daddslashes($string, $force = 0) {
		return uc_addslashes($string, $force);
	}
}

function uc_stripslashes($string) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(MAGIC_QUOTES_GPC) {
		return stripslashes($string);
	} else {
		return $string;
	}
}

function uc_api_post($module, $action, $arg = array()) {
	
	$s = $sep = '';
	foreach($arg as $k => $v) {
		$k = urlencode($k);
		if(is_array($v)) {
			$s2 = $sep2 = '';
			foreach($v as $k2 => $v2) {
				$k2 = urlencode($k2);
				$s2 .= "$sep2{$k}[$k2]=".urlencode(uc_stripslashes($v2));
				$sep2 = '&';
			}
			$s .= $sep.$s2;
		} else {
			$s .= "$sep$k=".urlencode(uc_stripslashes($v));
		}
		$sep = '&';
	}
	$postdata = uc_api_requestdata($module, $action, $s);
	
	return uc_fopen2(UC_API.'/index.php', 500000, $postdata, '', TRUE, UC_IP, 20);
}

function uc_api_requestdata($module, $action, $arg='', $extra='') {
	$input = uc_api_input($arg);
	$post = "m=$module&a=$action&inajax=2&release=".UC_CLIENT_RELEASE."&input=$input&appid=".UC_APPID.$extra;
	return $post;
}

function uc_api_url($module, $action, $arg='', $extra='') {
	$url = UC_API.'/index.php?'.uc_api_requestdata($module, $action, $arg, $extra);
	return $url;
}

function uc_api_input($data) {
	$s = urlencode(uc_authcode($data.'&agent='.md5($_SERVER['HTTP_USER_AGENT'])."&time=".time(), 'ENCODE', UC_KEY));
	return $s;
}

function uc_api_mysql($model, $action, $args=array()) {
	global $uc_controls;
	if(empty($uc_controls[$model])) {
		include_once UC_ROOT.'./lib/db.class.php';
		include_once UC_ROOT.'./model/base.php';
		include_once UC_ROOT."./control/$model.php";
		eval("\$uc_controls['$model'] = new {$model}control();");
	}
	if($action{0} != '_') {
		$args = uc_addslashes($args, 1, TRUE);
		$action = 'on'.$action;
		$uc_controls[$model]->input = $args;
		return $uc_controls[$model]->$action($args);
	} else {
		return '';
	}
}

function uc_serialize($arr, $htmlon = 0) {
	include_once UC_ROOT.'./lib/xml.class.php';
	return xml_serialize($arr, $htmlon);
}

function uc_unserialize($s) {
	include_once UC_ROOT.'./lib/xml.class.php';
	return xml_unserialize($s);
}

/*
function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
*/
//随机数
function createRandomStr($length){ 
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
	$strlen = 62; 
	while($length > $strlen){ 
	$str .= $str; 
	$strlen += 62; 
	} 
	$str = str_shuffle($str); 
	return substr($str,0,$length); 
} 

function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥
	$a_length =8;
	$b_length =10;
	$c_length =15;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keycy=createRandomStr($ckey_length);
	if($operation == 'DECODE'){
		$string=substr($string, $a_length);
		$string=substr($string, 0,-$b_length);
		$stringkeya=substr($string, 0,$ckey_length);
		$stringkeyb=substr($string,$ckey_length+$c_length);
		$string=$stringkeya.$stringkeyb;
	}

	//$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): $keycy) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		//return $keyc.str_replace('=', '', base64_encode($result));
		$vkeya=createRandomStr($a_length);
		$vkeyb=createRandomStr($b_length);
		$vkeyc=createRandomStr($c_length);
		//$keyc=Do_strtoupper($keyc);
		return $vkeya.$keyc.$vkeyc.str_replace('=', '', base64_encode($result)).$vkeyb;
	}

}

function uc_fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
	$__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
	if($__times__ > 2) {
		return '';
	}
	$url .= (strpos($url, '?') === FALSE ? '?' : '&')."__times__=$__times__";
	return uc_fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
}

function uc_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
	$return = '';
	$matches = parse_url($url);
	!isset($matches['host']) && $matches['host'] = '';
	!isset($matches['path']) && $matches['path'] = '';
	!isset($matches['query']) && $matches['query'] = '';
	!isset($matches['port']) && $matches['port'] = '';
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;

    if(substr($url,0,5)=='https'){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // https请求 不验证hosts
        return curl_exec($ch);
    }

	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: '.strlen($post)."\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		//$out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	}

	if(function_exists('pfsockopen')) {
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	} elseif (function_exists('fsockopen')) {
		$fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	} else {
		$fp = false;
	}


	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);

		if(!$status['timed_out']) {
			while (!feof($fp)) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}
			$stop = false;
			while(!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
//		@fclose($fp);
		return $return;
	}
}

function uc_app_ls() {
	$return = call_user_func(UC_API_FUNC, 'app', 'ls', array());
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_feed_add($icon, $uid, $username, $title_template='', $title_data='', $body_template='', $body_data='', $body_general='', $target_ids='', $images = array()) {
	return call_user_func(UC_API_FUNC, 'feed', 'add',
		array(  'icon'=>$icon,
			'appid'=>UC_APPID,
			'uid'=>$uid,
			'username'=>$username,
			'title_template'=>$title_template,
			'title_data'=>$title_data,
			'body_template'=>$body_template,
			'body_data'=>$body_data,
			'body_general'=>$body_general,
			'target_ids'=>$target_ids,
			'image_1'=>$images[0]['url'],
			'image_1_link'=>$images[0]['link'],
			'image_2'=>$images[1]['url'],
			'image_2_link'=>$images[1]['link'],
			'image_3'=>$images[2]['url'],
			'image_3_link'=>$images[2]['link'],
			'image_4'=>$images[3]['url'],
			'image_4_link'=>$images[3]['link']
		)
	);
}

function uc_feed_get($limit = 100, $delete = TRUE) {
	$return = call_user_func(UC_API_FUNC, 'feed', 'get', array('limit'=>$limit, 'delete'=>$delete));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_friend_add($uid, $friendid, $comment='') {
	return call_user_func(UC_API_FUNC, 'friend', 'add', array('uid'=>$uid, 'friendid'=>$friendid, 'comment'=>$comment));
}

function uc_friend_delete($uid, $friendids) {
	return call_user_func(UC_API_FUNC, 'friend', 'delete', array('uid'=>$uid, 'friendids'=>$friendids));
}

function uc_friend_totalnum($uid, $direction = 0) {
	return call_user_func(UC_API_FUNC, 'friend', 'totalnum', array('uid'=>$uid, 'direction'=>$direction));
}

function uc_friend_ls($uid, $page = 1, $pagesize = 10, $totalnum = 10, $direction = 0) {
	$return = call_user_func(UC_API_FUNC, 'friend', 'ls', array('uid'=>$uid, 'page'=>$page, 'pagesize'=>$pagesize, 'totalnum'=>$totalnum, 'direction'=>$direction));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_user_register($username, $password, $email, $phone, $questionid = '', $answer = '', $regip = '') {
	return call_user_func(UC_API_FUNC, 'user', 'register', array('username'=>$username, 'password'=>$password, 'email'=>$email, 'phone'=>$phone, 'questionid'=>$questionid, 'answer'=>$answer, 'regip' => $regip,'appid'=>UC_APPID));
}

function uc_user_login($username, $password, $isuid = 0, $checkques = 0, $questionid = '', $answer = '') {
	$isuid = intval($isuid);
	$return = call_user_func(UC_API_FUNC, 'user', 'login', array('username'=>$username, 'password'=>$password, 'isuid'=>$isuid, 'checkques'=>$checkques, 'questionid'=>$questionid, 'answer'=>$answer));
		
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_user_synlogin($uid) {
	$uid = intval($uid);
	if(@include UC_ROOT.'./data/cache/apps.php') {
		if(count($_CACHE['apps']) > 1) {
			$return = uc_api_post('user', 'synlogin', array('uid'=>$uid,'ia'=>1,'appid'=>UC_APPID));
		} else {
			$return = '';
		}
	}
	return uc_unserialize($return);
}

function uc_user_synlogout() {
	if(@include UC_ROOT.'./data/cache/apps.php') {
		if(count($_CACHE['apps']) > 1) {
			$return = uc_api_post('user', 'synlogout', array());
		} else {
			$return = '';
		}
	}
	return $return;
}

function uc_user_edit($username, $oldpw, $newpw, $email='', $ignoreoldpw = 0, $questionid = '', $answer = '') {
	return call_user_func(UC_API_FUNC, 'user', 'edit', array('username'=>$username, 'oldpw'=>$oldpw, 'newpw'=>$newpw, 'email'=>$email, 'ignoreoldpw'=>$ignoreoldpw, 'questionid'=>$questionid, 'answer'=>$answer, 'isuid'=>1));
}

function uc_user_edit_n($username, $oldpw, $newpw, $email='', $ignoreoldpw = 0, $questionid = '', $answer = '') {
	return call_user_func(UC_API_FUNC, 'user', 'edit', array('username'=>$username, 'oldpw'=>$oldpw, 'newpw'=>$newpw, 'email'=>$email, 'ignoreoldpw'=>$ignoreoldpw, 'questionid'=>$questionid, 'answer'=>$answer, 'isuid'=>0));
}

function uc_user_editphone($username,$phone,$isuid=0) {
	return call_user_func(UC_API_FUNC, 'user', 'editphone', array('username'=>$username, 'phone'=>$phone, 'isuid'=>$isuid));
}

function uc_user_delete($uid) {
	return call_user_func(UC_API_FUNC, 'user', 'delete', array('uid'=>$uid));
}

function uc_user_deleteavatar($uid) {
	uc_api_post('user', 'deleteavatar', array('uid'=>$uid));
}

function uc_user_checkname($username) {
	return call_user_func(UC_API_FUNC, 'user', 'check_username', array('username'=>$username));
}

function uc_user_checkemail($email) {
	return call_user_func(UC_API_FUNC, 'user', 'check_email', array('email'=>$email));
}

function uc_user_checkphone($phone) {
	return call_user_func(UC_API_FUNC, 'user', 'check_phone', array('phone'=>$phone));
}

function uc_user_checkpw($username,$pw,$isuid=0) {
	return call_user_func(UC_API_FUNC, 'user', 'check_pw', array('username'=>$username, 'pw'=>$pw, 'isuid'=>$isuid));
}

function uc_user_addprotected($username, $admin='') {
	return call_user_func(UC_API_FUNC, 'user', 'addprotected', array('username'=>$username, 'admin'=>$admin));
}

function uc_user_deleteprotected($username) {
	return call_user_func(UC_API_FUNC, 'user', 'deleteprotected', array('username'=>$username));
}

function uc_user_getprotected() {
	$return = call_user_func(UC_API_FUNC, 'user', 'getprotected', array('1'=>1));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_get_user($username, $isuid=0) {
	$return = call_user_func(UC_API_FUNC, 'user', 'get_user', array('username'=>$username, 'isuid'=>$isuid));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_get_userall($username, $isuid=0) {
	$return = call_user_func(UC_API_FUNC, 'user', 'get_userall', array('username'=>$username, 'isuid'=>$isuid));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_user_merge($oldusername, $newusername, $uid, $password, $email) {
	return call_user_func(UC_API_FUNC, 'user', 'merge', array('oldusername'=>$oldusername, 'newusername'=>$newusername, 'uid'=>$uid, 'password'=>$password, 'email'=>$email));
}

function uc_user_merge_remove($username) {
	return call_user_func(UC_API_FUNC, 'user', 'merge_remove', array('username'=>$username));
}

function uc_user_getcredit($appid, $uid, $credit) {
	return uc_api_post('user', 'getcredit', array('appid'=>$appid, 'uid'=>$uid, 'credit'=>$credit));
}

function uc_pm_location($uid, $newpm = 0) {
	$apiurl = uc_api_url('pm_client', 'ls', "uid=$uid", ($newpm ? '&folder=newbox' : ''));
	@header("Expires: 0");
	@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
	@header("location: $apiurl");
}

function uc_pm_checknew($uid, $more = 0) {
	$return = call_user_func(UC_API_FUNC, 'pm', 'check_newpm', array('uid'=>$uid, 'more'=>$more));
	return (!$more || UC_CONNECT == 'mysql') ? $return : uc_unserialize($return);
}

function uc_pm_send($fromuid, $msgto, $subject, $message, $instantly = 1, $replypmid = 0, $isusername = 0, $type = 0) {
	if($instantly) {
		$replypmid = @is_numeric($replypmid) ? $replypmid : 0;
		return call_user_func(UC_API_FUNC, 'pm', 'sendpm', array('fromuid'=>$fromuid, 'msgto'=>$msgto, 'subject'=>$subject, 'message'=>$message, 'replypmid'=>$replypmid, 'isusername'=>$isusername, 'type' => $type));
	} else {
		$fromuid = intval($fromuid);
		$subject = rawurlencode($subject);
		$msgto = rawurlencode($msgto);
		$message = rawurlencode($message);
		$replypmid = @is_numeric($replypmid) ? $replypmid : 0;
		$replyadd = $replypmid ? "&pmid=$replypmid&do=reply" : '';
		$apiurl = uc_api_url('pm_client', 'send', "uid=$fromuid", "&msgto=$msgto&subject=$subject&message=$message$replyadd");
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		@header("location: ".$apiurl);
	}
}

function uc_pm_delete($uid, $folder, $pmids) {
	return call_user_func(UC_API_FUNC, 'pm', 'delete', array('uid'=>$uid, 'pmids'=>$pmids));
}

function uc_pm_deleteuser($uid, $touids) {
	return call_user_func(UC_API_FUNC, 'pm', 'deleteuser', array('uid'=>$uid, 'touids'=>$touids));
}

function uc_pm_deletechat($uid, $plids, $type = 0) {
	return call_user_func(UC_API_FUNC, 'pm', 'deletechat', array('uid'=>$uid, 'plids'=>$plids, 'type'=>$type));
}

function uc_pm_readstatus($uid, $uids, $plids = array(), $status = 0) {
	return call_user_func(UC_API_FUNC, 'pm', 'readstatus', array('uid'=>$uid, 'uids'=>$uids, 'plids'=>$plids, 'status'=>$status));
}

function uc_pm_list($uid, $page = 1, $pagesize = 10, $folder = 'inbox', $filter = 'newpm', $msglen = 0) {
	$uid = intval($uid);
	$page = intval($page);
	$pagesize = intval($pagesize);
	$return = call_user_func(UC_API_FUNC, 'pm', 'ls', array('uid'=>$uid, 'page'=>$page, 'pagesize'=>$pagesize, 'filter'=>$filter, 'msglen'=>$msglen));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_pm_ignore($uid) {
	$uid = intval($uid);
	return call_user_func(UC_API_FUNC, 'pm', 'ignore', array('uid'=>$uid));
}

function uc_pm_view($uid, $pmid = 0, $touid = 0, $daterange = 1, $page = 0, $pagesize = 10, $type = 0, $isplid = 0) {
	$uid = intval($uid);
	$touid = intval($touid);
	$page = intval($page);
	$pagesize = intval($pagesize);
	$pmid = @is_numeric($pmid) ? $pmid : 0;
	$return = call_user_func(UC_API_FUNC, 'pm', 'view', array('uid'=>$uid, 'pmid'=>$pmid, 'touid'=>$touid, 'daterange'=>$daterange, 'page' => $page, 'pagesize' => $pagesize, 'type'=>$type, 'isplid'=>$isplid));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_pm_view_num($uid, $touid, $isplid) {
	$uid = intval($uid);
	$touid = intval($touid);
	$isplid = intval($isplid);
	return call_user_func(UC_API_FUNC, 'pm', 'viewnum', array('uid' => $uid, 'touid' => $touid, 'isplid' => $isplid));
}

function uc_pm_viewnode($uid, $type, $pmid) {
	$uid = intval($uid);
	$type = intval($type);
	$pmid = @is_numeric($pmid) ? $pmid : 0;
	$return = call_user_func(UC_API_FUNC, 'pm', 'viewnode', array('uid'=>$uid, 'type'=>$type, 'pmid'=>$pmid));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_pm_chatpmmemberlist($uid, $plid = 0) {
	$uid = intval($uid);
	$plid = intval($plid);
	$return = call_user_func(UC_API_FUNC, 'pm', 'chatpmmemberlist', array('uid'=>$uid, 'plid'=>$plid));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_pm_kickchatpm($plid, $uid, $touid) {
	$uid = intval($uid);
	$plid = intval($plid);
	$touid = intval($touid);
	return call_user_func(UC_API_FUNC, 'pm', 'kickchatpm', array('uid'=>$uid, 'plid'=>$plid, 'touid'=>$touid));
}

function uc_pm_appendchatpm($plid, $uid, $touid) {
	$uid = intval($uid);
	$plid = intval($plid);
	$touid = intval($touid);
	return call_user_func(UC_API_FUNC, 'pm', 'appendchatpm', array('uid'=>$uid, 'plid'=>$plid, 'touid'=>$touid));
}

function uc_pm_blackls_get($uid) {
	$uid = intval($uid);
	return call_user_func(UC_API_FUNC, 'pm', 'blackls_get', array('uid'=>$uid));
}

function uc_pm_blackls_set($uid, $blackls) {
	$uid = intval($uid);
	return call_user_func(UC_API_FUNC, 'pm', 'blackls_set', array('uid'=>$uid, 'blackls'=>$blackls));
}

function uc_pm_blackls_add($uid, $username) {
	$uid = intval($uid);
	return call_user_func(UC_API_FUNC, 'pm', 'blackls_add', array('uid'=>$uid, 'username'=>$username));
}

function uc_pm_blackls_delete($uid, $username) {
	$uid = intval($uid);
	return call_user_func(UC_API_FUNC, 'pm', 'blackls_delete', array('uid'=>$uid, 'username'=>$username));
}

function uc_domain_ls() {
	$return = call_user_func(UC_API_FUNC, 'domain', 'ls', array('1'=>1));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_credit_exchange_request($uid, $from, $to, $toappid, $amount) {
	$uid = intval($uid);
	$from = intval($from);
	$toappid = intval($toappid);
	$to = intval($to);
	$amount = intval($amount);
	return uc_api_post('credit', 'request', array('uid'=>$uid, 'from'=>$from, 'to'=>$to, 'toappid'=>$toappid, 'amount'=>$amount));
}

function uc_tag_get($tagname, $nums = 0) {
	$return = call_user_func(UC_API_FUNC, 'tag', 'gettag', array('tagname'=>$tagname, 'nums'=>$nums));
	return UC_CONNECT == 'mysql' ? $return : uc_unserialize($return);
}

function uc_avatar($uid, $type = 'virtual', $returnhtml = 1) {
	$uid = intval($uid);
	$uc_input = uc_api_input("uid=$uid");
	$uc_avatarflash = UC_API.'/images/camera.swf?inajax=1&appid='.UC_APPID.'&input='.$uc_input.'&agent='.md5($_SERVER['HTTP_USER_AGENT']).'&ucapi='.urlencode(str_replace('http://', '', UC_API)).'&avatartype='.$type.'&uploadSize=2048';
	if($returnhtml) {
		return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="'.$uc_avatarflash.'" />
			<param name="menu" value="false" />
			<embed src="'.$uc_avatarflash.'" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
	} else {
		return array(
			'width', '450',
			'height', '253',
			'scale', 'exactfit',
			'src', $uc_avatarflash,
			'id', 'mycamera',
			'name', 'mycamera',
			'quality','high',
			'bgcolor','#ffffff',
			'menu', 'false',
			'swLiveConnect', 'true',
			'allowScriptAccess', 'always'
		);
	}
}

function uc_mail_queue($uids, $emails, $subject, $message, $frommail = '', $charset = 'gbk', $htmlon = FALSE, $level = 1) {
	return call_user_func(UC_API_FUNC, 'mail', 'add', array('uids' => $uids, 'emails' => $emails, 'subject' => $subject, 'message' => $message, 'frommail' => $frommail, 'charset' => $charset, 'htmlon' => $htmlon, 'level' => $level));
}

function uc_check_avatar($uid, $size = 'middle', $type = 'virtual') {
	$url = UC_API."/avatar.php?uid=$uid&size=$size&type=$type&check_file_exists=1";
	$res = uc_fopen2($url, 500000, '', '', TRUE, UC_IP, 20);
	if($res == 1) {
		return 1;
	} else {
		return 0;
	}
}

function uc_check_version() {
	$return = uc_api_post('version', 'check', array());
	$data = uc_unserialize($return);
	return is_array($data) ? $data : $return;
}

//企业认证
function uc_company_rz($add) {
	$company_name=$add['company_name'];
	$user_id=$add['user_id'];
	$grade_id=(int)$add['grade_id'];
	$industry_type=(int)$add['industry_type'];
	$company_address=$add['company_address'];
	$bank_reg_num=$add['bank_reg_num'];
	$reg_money=$add['reg_money'];
	$organization_id=(int)$add['organization_id'];
	$threeinone=(int)$add['threeinone'];
	$social_credit_code=$add['social_credit_code'];
	$orgnization_code=$add['orgnization_code'];
	$business_licence_number=$add['business_licence_number'];
	$taxreg_number=$add['taxreg_number'];
	$business_licence_cert=$add['business_licence_cert'];
	$taxreg_cert=$add['taxreg_cert'];
	$orgnization_cert=$add['orgnization_cert'];
	$bank_reg_cert=$add['bank_reg_cert'];
	$legal_identity_cert=$add['legal_identity_cert'];
	$legal_person=$add['legal_person'];
	$legal_identity_num=$add['legal_identity_num'];
	$legal_phone=$add['legal_phone'];
	$operator_name=$add['operator_name'];
	$operator_identity_num=$add['operator_identity_num'];
	$operator_phone=$add['operator_phone'];
	$is_lo=(int)$add['is_lo'];
	$operator_identity_cert=$add['operator_identity_cert'];
	$certificate=$add['certificate'];
	$seal_owner_name=$add['seal_owner_name'];
	$seal_owner_phone=$add['seal_owner_phone'];
	$seal_owner_idCode=$add['seal_owner_idCode'];
	return call_user_func(UC_API_FUNC, 'company', 'rz', array('company_name'=>$company_name,'user_id'=>$user_id,'grade_id'=>$grade_id,'industry_type'=>$industry_type,'company_address'=>$company_address,'bank_reg_num'=>$bank_reg_num,'reg_money'=>$reg_money,'organization_id'=>$organization_id,'threeinone'=>$threeinone,'social_credit_code'=>$social_credit_code,'orgnization_code'=>$orgnization_code,'business_licence_number'=>$business_licence_number,'taxreg_number'=>$taxreg_number,'business_licence_cert'=>$business_licence_cert,'taxreg_cert'=>$taxreg_cert,'orgnization_cert'=>$orgnization_cert,'bank_reg_cert'=>$bank_reg_cert,'legal_identity_cert'=>$legal_identity_cert,'legal_person'=>$legal_person,'legal_identity_num'=>$legal_identity_num,'legal_phone'=>$legal_phone,'operator_name'=>$operator_name,'operator_identity_num'=>$operator_identity_num,'operator_phone'=>$operator_phone,'is_lo'=>$is_lo,'operator_identity_cert'=>$operator_identity_cert,'certificate'=>$certificate,'seal_owner_name'=>$seal_owner_name,'seal_owner_phone'=>$seal_owner_phone,'seal_owner_idCode'=>$seal_owner_idCode));
}

//查询企业认证情况
function uc_company_isrz($user_id,$iscompanyname=0) {
	$return=call_user_func(UC_API_FUNC, 'company', 'isrz', array('user_id'=>$user_id,'iscompanyname'=>$iscompanyname));
	return uc_unserialize($return);
}

//查询企业认证情况（模糊匹配）
function uc_company_isrz_list($company_name, $page = 0, $limit = 10) {
	$return=call_user_func(UC_API_FUNC, 'company', 'isrz_list', array('company_name' => $company_name, 'page' => $page, 'pagesize' => $limit));
	return uc_unserialize($return);
}

//企业修改用章管理人信息【软签】
function uc_company_changeseal($add) {
    $user_id=$add['user_id'];
    $seal_owner_name=$add['seal_owner_name'];
    $seal_owner_phone=$add['seal_owner_phone'];
    $seal_owner_idCode=$add['seal_owner_idCode'];
    $return = call_user_func(UC_API_FUNC, 'company', 'seal_apply', array('user_id'=>$user_id,'seal_owner_name'=>$seal_owner_name,'seal_owner_phone'=>$seal_owner_phone,'seal_owner_idCode'=>$seal_owner_idCode));
    return uc_unserialize($return);
}

//企业用章管理人信息修改记录【软签】
function uc_company_changeseal_log($add) {
    $user_id=$add['user_id'];
	$return = call_user_func(UC_API_FUNC, 'company', 'seal_log', array('user_id'=>$user_id));
    return uc_unserialize($return);
}

//企业用章管理人信息最后申请记录处理结果【软签】
function uc_company_seal_last_apply($add) {
    $user_id=$add['user_id'];
    $return = call_user_func(UC_API_FUNC, 'company', 'seal_latest_log', array('user_id'=>$user_id));
    return uc_unserialize($return);
}

//查询企业审核记录
function uc_company_checklog($user_id,$all=0) {
	$return=call_user_func(UC_API_FUNC, 'company', 'clog', array('user_id'=>$user_id,'all'=>$all));
	return uc_unserialize($return);
}

//CA查询
function uc_get_ca_by_user_company($search,$type=1,$page=1,$limit=10){
	$return = call_user_func(UC_API_FUNC, 'ca', 'get_ca_by_user_company', array('search'=>$search,'type'=>$type,'page'=>$page,'limit'=>$limit,'app_id'=>UC_APPID));
	return uc_unserialize($return);
}

//CA申请补发
function uc_add_calog($data){
	$return = call_user_func(UC_API_FUNC, 'calog', 'add_calog', array('data'=>$data,'app_id'=>UC_APPID));
	return uc_unserialize($return);
}

//投保人付费给担保机构
function uc_user_pay_money($uid,$order_id,$amount,$chanel,$fee,$code,$to_uid,$to_type,$to_amount,$cz_amount,$btc_uid,$btc_amount,$note){
    $data = array('uid'=>$uid,'order_id'=>$order_id,'amount'=>$amount,'chanel'=>$chanel,'fee'=>$fee,'code'=>$code,'to_uid'=>$to_uid,'to_type'=>$to_type,'to_amount'=>$to_amount,'cz_amount'=>$cz_amount,'btc_uid'=>$btc_uid,'btc_amount'=>$btc_amount,'note'=>$note,'app_id'=>UC_APPID);
    $return = call_user_func(UC_API_FUNC, 'faccount', 'user_pay_money', $data);
    return uc_unserialize($return);
}

//（分离式中）担保公司支付给银行
function uc_company_pay_to_bank($uid,$bank_uid,$order_id,$amount,$note){
    $return = call_user_func(UC_API_FUNC, 'faccount', 'company_pay_to_bank', array('uid'=>$uid,'bank_uid'=>$bank_uid,'order_id'=>$order_id,'amount'=>$amount,'note'=>$note,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}

//银行提现（银行进账后，马上提现平账）
function uc_bank_withdrawal($order_id,$fee,$code,$note){
    $return = call_user_func(UC_API_FUNC, 'faccount', 'bank_withdrawal', array('order_id'=>$order_id,'fee'=>$fee,'code'=>$code,'note'=>$note,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}

//开函成功,资金解冻
function uc_unfrozen_capital($order_id,$note,$f_order_id=0){
    $return = call_user_func(UC_API_FUNC, 'faccount', 'unfrozen_capital', array('order_id'=>$order_id,'note'=>$note,'f_order_id'=>$f_order_id,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}

//根据用户id获取资金账户信息
function uc_faccount($uid){
    $return = call_user_func(UC_API_FUNC, 'faccount', 'faccount', array('uid'=>$uid,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//根据用户id获取该用户的所有资金流水
function uc_faccountlogs($uid,$start_date='',$end_date='',$page=1,$limit=10){
    $return = call_user_func(UC_API_FUNC, 'faccountlog', 'faccountlogs', array('uid'=>$uid,'start_date'=>$start_date,'end_date'=>$end_date,'page'=>$page,'limit'=>$limit,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//提现申请
function uc_addfwithdrawal($insert){
    $insert['app_id'] = UC_APPID;
    $return = call_user_func(UC_API_FUNC, 'fwithdrawal', 'addfwithdrawal', $insert);
    return uc_unserialize($return);
}
//根据用户id获取该用户的所有提现记录
function uc_fwithdrawals($uid,$start_date='',$end_date='',$page=1,$limit=10){
    $return = call_user_func(UC_API_FUNC, 'fwithdrawal', 'fwithdrawals', array('uid'=>$uid,'start_date'=>$start_date,'end_date'=>$end_date,'page'=>$page,'limit'=>$limit,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//提现总和
function uc_fwithdrawal_sum($uid,$state=2,$start_date='',$end_date=''){
    $return = call_user_func(UC_API_FUNC, 'fwithdrawal', 'fwithdrawal_sum', array('uid'=>$uid,'state'=>$state,'start_date'=>$start_date,'end_date'=>$end_date,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//根据用户id获取该用户的所有交易记录
function uc_ftransactions($uid,$start_date='',$end_date='',$page=1,$limit=10){
    $return = call_user_func(UC_API_FUNC, 'ftransaction', 'ftransactions', array('uid'=>$uid,'start_date'=>$start_date,'end_date'=>$end_date,'page'=>$page,'limit'=>$limit,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//保费收入
function uc_ftransaction_sum($uid,$type=1,$start_date='',$end_date=''){
    $return = call_user_func(UC_API_FUNC, 'ftransaction', 'ftransaction_sum', array('uid'=>$uid,'type'=>$type,'start_date'=>$start_date,'end_date'=>$end_date,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}

function uc_get_ca_by_uid($uid){
    $return = call_user_func(UC_API_FUNC, 'ca', 'get_ca_by_uid', array('uid'=>$uid));
    return uc_unserialize($return);
}

//获取银行卡列表
function uc_get_userbanks($uid){
    $return = call_user_func(UC_API_FUNC, 'userbank', 'get_userbanks', array('uid'=>$uid,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//获取提现银行卡
function uc_get_default_userbank($uid){
    $return = call_user_func(UC_API_FUNC, 'userbank', 'get_default_userbank', array('uid'=>$uid,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//添加银行卡
function uc_add_userbank($data){
    $return = call_user_func(UC_API_FUNC, 'userbank', 'add_userbank', array('data'=>$data,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//设置默认银行卡
function uc_set_userbank($id,$uid){
    $return = call_user_func(UC_API_FUNC, 'userbank', 'set_userbank', array('uid'=>$uid,'id'=>$id,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}
//删除银行卡
function uc_del_userbank($id,$uid){
    $return = call_user_func(UC_API_FUNC, 'userbank', 'del_userbank', array('uid'=>$uid,'id'=>$id,'app_id'=>UC_APPID));
    return uc_unserialize($return);
}

//添加保函信息
function uc_add_guarantee($insert){
    $insert['app_id'] = UC_APPID;
    $return = call_user_func(UC_API_FUNC, 'guarantee', 'add_guarantee', $insert);
    return uc_unserialize($return);
}

/**
 * 获取企业信息变更历史
 * @param string $search_value 搜索的值
 * @param int $type 1用户ID2公司ID3公司名称,默认1用户ID
 * @param int $page 当前页数,从1开始
 * @param int $limit 每页条数,默认每页10条
 * @return array|string
 */
function uc_get_change_history($search_value, $type=1, $page=1, $limit=10){
    $return = call_user_func(UC_API_FUNC, 'company', 'get_change_history', ['search_value'=>$search_value,'type'=>$type,'page'=>$page,'limit'=>$limit,'app_id'=>UC_APPID]);
    return uc_unserialize($return);
}

/**
 * @param int $id 企业id
 * @param array $update 待更新的企业信息
 * @return array|string
 */
function uc_edit_company($id, $update){
    $return = call_user_func(UC_API_FUNC, 'company', 'edit_company', ['id'=>$id,'data'=>$update,'app_id'=>UC_APPID]);
    return uc_unserialize($return);
}

?>