<?php
/**
 * UCenter 应用程序开发 API Example
 *
 * 此文件为 api/uc.php 文件的开发样例，用户处理 UCenter 通知给应用程序的任务
 */

define('UC_VERSION', '1.0.0');		//UCenter 版本标识

define('API_DELETEUSER', 1);		//用户删除 API 接口开关
define('API_RENAMEUSER', 1);		//用户改名 API 接口开关
define('API_UPDATEPW', 1);		//用户改密码 API 接口开关
define('API_GETTAG', 0);		//获取标签 API 接口开关
define('API_SYNLOGIN', 1);		//同步登录 API 接口开关
define('API_SYNLOGOUT', 1);		//同步登出 API 接口开关
define('API_UPDATEBADWORDS', 1);	//更新关键字列表 开关
define('API_UPDATEHOSTS', 1);		//更新域名解析缓存 开关
define('API_UPDATEAPPS', 1);		//更新应用列表 开关
define('API_UPDATECLIENT', 1);		//更新客户端缓存 开关
define('API_UPDATECREDIT', 1);		//更新用户积分 开关
define('API_GETCREDITSETTINGS', 1);	//向 UCenter 提供积分设置 开关
define('API_UPDATECREDITSETTINGS', 1);	//更新应用积分设置 开关

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

define('UC_CLIENT_ROOT', dirname(__DIR__).'/client/');
require_once dirname(__DIR__).'/config.inc.php';
require_once UC_CLIENT_ROOT.'/client.php';
chdir('../');

$code = $_GET['code'];
parse_str(authcode($code, 'DECODE', UC_KEY), $get);
if(MAGIC_QUOTES_GPC) {
	$get = dstripslashes($get);
}

if(time() - $get['time'] > 3600) {
	exit('Authracation has expiried');
}
if(empty($get)) {
	exit('Invalid Request');
}
$action = $get['action'];
$timestamp = time();

//导入文件
if($action=='deleteuser'||$action=='renameuser'||$action=='updatepw'||$action=='synlogin'||$action=='synlogout'||$action == 'syncheckcompany'||$action=='synreissueca'||$action == 'bk_check_is_branch_bank'||$action== 'bk_gua_list'||$action== 'bk_gua_detail'||$action== 'bk_gua_audit'||$action== 'bk_gua_all_audit'||$action== 'orders'||$action=='bank_account'||$action=='faccount_detail_orders'||$action=='order_total'||$action=='order_expire_total')
{
	include_once dirname(__DIR__).'/class/connect.php';
    include_once dirname(__DIR__).'/class/db_sql.php';
    include_once dirname(__DIR__).'/member/class/user.php';
    include_once dirname(__DIR__).'/service/DemandService.php';
    include_once dirname(__DIR__).'/service/OrderService.php';
	$link=db_connect();
	$empire=new mysqlquery();
}

if($action == 'test') {

	exit(API_RETURN_SUCCEED);

} elseif($action == 'deleteuser') {

	!API_DELETEUSER && exit(API_RETURN_FORBIDDEN);

	//用户删除 API 接口

	$uids = $get['ids'];
	$uids=str_replace("'",'',$uids);

	$ur=explode(',',$uids);
	$count=count($ur);
	$b=0;
	$trueuids='';
	$dh='';
	for($i=0;$i<$count;$i++)
	{
		$thisuid=(int)$ur[$i];
		//删除短信息
		$userr=$empire->fetch1("select username from {$dbtbpre}enewsmember where userid='".$thisuid."'");
		$b=1;
		$del=$empire->query("delete from {$dbtbpre}enewsqmsg where to_username='".$userr['username']."' limit 1");
		$trueuids.=$dh.$thisuid;
		$dh=',';
	}
	if($b==1)
	{
		//删除会员
		$sql=$empire->query("delete from {$dbtbpre}enewsmember where userid in ($trueuids)");
		$sql=$empire->query("delete from {$dbtbpre}enewsmemberadd where userid in ($trueuids)");
		$sql=$empire->query("delete from {$dbtbpre}enewsmemberpub where userid in ($trueuids)");
		//删除收藏
		$del=$empire->query("delete from {$dbtbpre}enewsfava where userid in ($trueuids)");
		$del=$empire->query("delete from {$dbtbpre}enewsfavaclass where userid in ($trueuids)");
		//删除购买记录
		$del=$empire->query("delete from {$dbtbpre}enewsbuybak where userid in ($trueuids)");
		//删除下载记录
		$del=$empire->query("delete from {$dbtbpre}enewsdownrecord where userid in ($trueuids)");
		//删除好友记录
		$del=$empire->query("delete from {$dbtbpre}enewshy where userid in ($trueuids)");
		$del=$empire->query("delete from {$dbtbpre}enewshyclass where userid in ($trueuids)");
		//删除留言
		$del=$empire->query("delete from {$dbtbpre}enewsmembergbook where userid in ($trueuids)");
		//删除反馈
		$del=$empire->query("delete from {$dbtbpre}enewsmemberfeedback where userid in ($trueuids)");
		//删除绑定
		$del=$empire->query("delete from {$dbtbpre}enewsmember_connect where userid in ($trueuids);");
	}

	exit(API_RETURN_SUCCEED);

} elseif($action == 'renameuser') {

	!API_RENAMEUSER && exit(API_RETURN_FORBIDDEN);

	//用户改名 API 接口
	$uid = $get['uid'];
	$usernamenew = $get['newusername'];
	$usernameold = $get['oldusername'];

	$uid=(int)$uid;
	$usernamenew=RepPostVar($usernamenew);
	$usernameold=RepPostVar($usernameold);

	//会员表
	$sql=$empire->query("update {$dbtbpre}enewsmember set username='$usernamenew' where userid='$uid'");
	//短信息
	$sql=$empire->query("update {$dbtbpre}enewsqmsg set to_username='$usernamenew' where to_username='$usernameold'");
	$sql=$empire->query("update {$dbtbpre}enewsqmsg set from_username='$usernamenew' where from_username='$usernameold'");
	//收藏
	$sql=$empire->query("update {$dbtbpre}enewsfava set username='$usernamenew' where userid='$uid'");
	//购买记录
	$sql=$empire->query("update {$dbtbpre}enewsbuybak set username='$usernamenew' where userid='$uid'");
	//下载记录
	$sql=$empire->query("update {$dbtbpre}enewsdownrecord set username='$usernamenew' where userid='$uid'");
	//信息表
	$tbsql=$empire->query("select tbname from {$dbtbpre}enewstable");
	while($tbr=$empire->fetch($tbsql))
	{
		$usql=$empire->query("update {$dbtbpre}ecms_".$tbr['tbname']." set username='$usernamenew' where userid='$uid' and ismember=1");
		$usql=$empire->query("update {$dbtbpre}ecms_".$tbr['tbname']."_check set username='$usernamenew' where userid='$uid' and ismember=1");
	}

	exit(API_RETURN_SUCCEED);

} elseif($action == 'updatepw') {

	!API_UPDATEPW && exit(API_RETURN_FORBIDDEN);

	//更改用户密码
	$username=$get['username'];
	$password=$get['password'];

	$username=RepPostVar($username);
	$salt=make_password(6);
	$password=md5(md5($password).$salt);
	$sql=$empire->query("update {$dbtbpre}enewsmember set password='$password',salt='$salt' where username='$username' limit 1");

	exit(API_RETURN_SUCCEED);

} elseif($action == 'gettag') {

	!API_GETTAG && exit(API_RETURN_FORBIDDEN);

	//获取标签 API 接口
	exit(API_RETURN_SUCCEED);

} elseif($action == 'synlogin' && $_GET['time'] == $get['time']) {

	!API_SYNLOGIN && exit(API_RETURN_FORBIDDEN);

	//同步登录 API 接口

	$uid = intval($get['uid']);

	$ur=$empire->fetch1("select userid,username,groupid from {$dbtbpre}enewsmember where userid='$uid'");
	$logincookie=time()+86400*365;//cookie保存时间
	require_once './member/class/user.php';
	if(!$ur['userid'])
	{
		include_once("./client/client.php");
		if($data = uc_get_userall($uid,1)) {
	          list($uid,$username,$email,$phone,$password,$regip,$regdate,$salt) = $data;
			  if($uid&&$username&&$password&&$phone&&$salt){
				  $rnd=make_password(20);
				  $lasttime=time();
				  $user_groupid=eReturnMemberDefGroupid();
				  $checked=1;
				  $userkey=eReturnMemberUserKey();
				  $isql=$empire->query("insert into ".eReturnMemberTable()."(".eReturnInsertMemberF('userid,username,password,rnd,email,registertime,groupid,userfen,userdate,money,zgroupid,havemsg,checked,salt,userkey').") values('$uid','$username','$password','$rnd','$email','$regdate','$user_groupid','$public_r[reggetfen]','0','0','0','0','$checked','$salt','$userkey');");
				  //附加表
				  $addr=$empire->fetch1("select * from {$dbtbpre}enewsmemberadd where userid='$uid'");
				  if(!$addr[userid])
				  {
					  $sql1=$empire->query("insert into {$dbtbpre}enewsmemberadd(userid,regip,lasttime,lastip,loginnum,phone) values('$uid','$regip','$lasttime','$regip','0','$phone');");
				  }
				  $doactive=1;
				  $ur=$empire->fetch1("select userid,username,groupid from {$dbtbpre}enewsmember where userid='$uid'");
			  }
        }
		
	}
	
	if($ur['userid'])
	{
		
		$rnd=make_password(12);
		//默认会员组
		if(empty($ur['groupid']))
		{
			$ur['groupid']=$public_r['defaultgroupid'];
		}
		$usql=$empire->query("update {$dbtbpre}enewsmember set rnd='$rnd',groupid='$ur[groupid]' where userid='$uid'");
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$set1=esetcookie("mlusername",$ur['username'],$logincookie);
		$set2=esetcookie("mluserid",$ur['userid'],$logincookie);
		$set3=esetcookie("mlgroupid",$ur['groupid'],$logincookie);
		$set4=esetcookie("mlrnd",$rnd,$logincookie);
		esetcookie("mldoactive","",0);
		qGetLoginAuthstr($ur['userid'],$ur['username'],$rnd,$ur['groupid'],$logincookie);
	}else{
		$set5=esetcookie("mldoactive",$uid,$logincookie);
	}

} elseif($action == 'synlogout') {

	!API_SYNLOGOUT && exit(API_RETURN_FORBIDDEN);

	//同步登出 API 接口
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	$set1=esetcookie("mlusername","",0);
	$set2=esetcookie("mluserid","",0);
	$set3=esetcookie("mlgroupid","",0);
	$set4=esetcookie("mlrnd","",0);
	$set5=esetcookie("mlauth","",0);

} elseif($action == 'updatebadwords') {

	!API_UPDATEBADWORDS && exit(API_RETURN_FORBIDDEN);

	//更新关键字列表
	exit(API_RETURN_SUCCEED);

} elseif($action == 'updatehosts') {

	!API_UPDATEHOSTS && exit(API_RETURN_FORBIDDEN);

	//更新HOST文件
	exit(API_RETURN_SUCCEED);

} elseif($action == 'updateapps') {

	!API_UPDATEAPPS && exit(API_RETURN_FORBIDDEN);

	//更新应用列表
	exit(API_RETURN_SUCCEED);

} elseif($action == 'updateclient') {

	!API_UPDATECLIENT && exit(API_RETURN_FORBIDDEN);

	//更新客户端缓存
	exit(API_RETURN_SUCCEED);

} elseif($action == 'updatecredit') {

	!UPDATECREDIT && exit(API_RETURN_FORBIDDEN);

	//更新用户积分
	exit(API_RETURN_SUCCEED);

} elseif($action == 'getcreditsettings') {

	!GETCREDITSETTINGS && exit(API_RETURN_FORBIDDEN);

	//向 UCenter 提供积分设置
	echo uc_serialize2($credits);

} elseif($action == 'updatecreditsettings') {

	!API_UPDATECREDITSETTINGS && exit(API_RETURN_FORBIDDEN);

	//更新应用积分设置
	exit(API_RETURN_SUCCEED);

} elseif($action == 'syncheckcompany') {
    $user_id = $get['user_id'];
	$status=(int)$get['status'];
	$name=$get['name'];
	$cid=(int)$get['cid'];
	$industry_type=(int)$get['industry_type'];
	$bz=addslashes($get['bz']);
    if ($status == 1) {
        $title = "企业认证审核通过";
        $msgtext = "恭喜您，您的企业认证通过了！";
        $msg_data['msgtext'] = '恭喜您，您的企业认证通过了！';
        $msg_data['type'] = 1;
        $type = "Successful_certification";
    } elseif ($status == -2) {
        $title = "企业认证审核不通过";
        $msgtext = "抱歉，您的企业未通过认证，原因：" . $bz;
        if ($bz){
            $msg_data['msgtext'] = '对不起,您的企业认证失败了,【'.$bz.'】,请重新提交企业认证审核!';
        }else{
            $msg_data['msgtext'] = '对不起,您的企业认证失败了,请重新提交企业认证审核!';
        }
        $msg_data['type'] = 2;
        $type = "Authentication_failed";
    } else {
        $title = "企业信息已关闭";
        $msgtext = "抱歉，您的企业信息已关闭，原因：" . $bz;
        $msg_data['msgtext'] = $msgtext;
    }
    $r = $empire->fetch1("select userid, username, havemsg from {$dbtbpre}enewsmember where userid='$user_id' limit 1");
    //eSendMsg(addslashes($title), addslashes($msgtext), $r['username'], 0, '', 1, 1, 0);

    //如果是交易中心,写入btc表
    if ($industry_type==5){
        $btc = $empire->fetch1("select name from {$dbtbpre}ecms_btc where uid= {$user_id} limit 1");
        if (!$btc['name']){
            $insert_data['uid'] = $user_id;
            $insert_data['cid'] = $cid;
            $insert_data['name'] = trim($name);
            $insert_data['created_at'] = time();
            $sql = format_data_to_sql($insert_data);
            $empire->query("insert into {$dbtbpre}ecms_btc set {$sql}");
        }
    } else if($industry_type == 6){ //如果是交招标代理,写入bidding_agency表
        $biddingAgency = $empire->fetch1("select id from {$dbtbpre}bidding_agency where uid = {$user_id} limit 1");
        if (empty($biddingAgency)) {
            $insert_data['uid'] = $user_id;
            $insert_data['cid'] = $cid;
            $insert_data['name'] = trim($name);
            $insert_data['created_at'] = time();
            $insert_data['created_at'] = time();
            $sql = format_data_to_sql($insert_data);
            $empire->query("insert into {$dbtbpre}bidding_agency set {$sql}");
        }
    }

    $msg_data['title'] = '系统【企业认证】提醒';
    $msg_data['msgtime'] = time();
    $msg_data['to_uid'] = $user_id;
    if ($r['username']){
        $msg_data['to_username'] = $r['username'];
    }else{
        $uc_user_data = uc_get_user($user_id,1);
        list($uid,$username,$email,$phone,$password,$regip,$regdate,$salt) = $uc_user_data;
        $msg_data['to_username'] = $username;
    }
    $msg_data['from_userid'] = 0;
    $msg_data['from_username'] = '';
    $msg_data['issys'] = 1;
    $empire->query("insert into {$dbtbpre}enewsqmsg set ".format_data_to_sql($msg_data));
} elseif($action == 'synreissueca') {
    $user_id = $get['user_id'];

    $msg_data['title'] = '系统【证书补发】提醒';
    $msg_data['msgtext'] = '恭喜您，您申请补发的CA证书已下发成功！';
    $msg_data['msgtime'] = time();
    $msg_data['to_uid'] = $user_id;
    $r = $empire->fetch1("select userid, username, havemsg from {$dbtbpre}enewsmember where userid='$user_id' limit 1");
    if ($r['username']){
        $msg_data['to_username'] = $r['username'];
    }else{
        $uc_user_data = uc_get_user($user_id,1);
        list($uid,$username,$email,$phone,$password,$regip,$regdate,$salt) = $uc_user_data;
        $msg_data['to_username'] = $username;
    }
    $msg_data['from_userid'] = 0;
    $msg_data['from_username'] = '';
    $msg_data['issys'] = 1;
    $msg_data['type'] = 10;
    require_once ECMS_PATH.'e/member/class/msgfun.php';
    send_message($msg_data,$type);


} elseif ($action == 'bk_check_is_branch_bank') { // 校验是否分行
	$uid = (int)$get['uid'];
	$res = getAllBanks($uid);
    if ($res['code'] != 0) {
    	exit(json_encode(['status' => -1, 'msg' => '未有权限！']));
    } else {
    	exit(json_encode(['status' => 0, 'msg' => '查询成功']));
    }
}elseif($action == 'bk_gua_list') {//微信保函待审/已审遍历
    $uid = (int)$get['uid'];
    $res = getAllBanks($uid);
    if ($res['code'] != 0) exit(json_encode(['status' => 0, 'msg' => '该分行审核暂不可用！']));
    $bankIds = $res['data']['bankIds'];
    $branchBank = $res['data']['branchBank'];

    if ( ! $branchBank['uid']) exit(json_encode(['status'=>0,'msg'=>'未有权限']));
    $page = $get['page'];
    $page .= ',5';
    $bh_mode = $get['as'];
    $bh_mode = (int)$bh_mode;

    $queryCountSql = "select COUNT(*) AS total from {$dbtbpre}signature sign INNER JOIN {$dbtbpre}order o ON sign.did = o.id";
    // 待审核总数
	$totalUncheckGuaranteeQuery = "$queryCountSql where o.bh_mode=0 and sign.is_check=1 and o.a_uid in ($bankIds)";
	$totalUncheckVoucherQuery = "$queryCountSql where o.bh_mode in (1,2) and sign.is_check=1 and o.a_uid in ($bankIds)";
	$data['ds_guarantee_num'] = $empire->gettotal($totalUncheckGuaranteeQuery);
	$data['ds_voucher_num'] = $empire->gettotal($totalUncheckVoucherQuery);

	// 选择搜索电子保函还是电子凭证
	$bh_mode_string = $bh_mode ?: 0;
	if ($bh_mode == 0) {
		$bh_mode_string = ' =0';
	} else if ($bh_mode == 1 || $bh_mode == 2) {
		$bh_mode_string = ' in (1,2)';
	}

	// 所有总数
	$totalQuery = "$queryCountSql where o.bh_mode$bh_mode_string and sign.is_check<>0 and o.a_uid in ($bankIds)";
	$data['total_num'] = $empire->gettotal($totalQuery);

	$orderService = OrderService::getInstance();
	// 列表数据
    $querySql = "SELECT sign.pdf_url2,sign.backletter_num,sign.signature_time,sign.id,sign.userid,sign.did,sign.is_check,sign.audit_time,o.id o_id,o.ktype,o.is_fd,o.a_cname,o.b_cname,o.cname,o.price,o.is_encrypt,o.data_encrypt_id FROM {$dbtbpre}signature sign INNER JOIN {$dbtbpre}order o ON sign.did = o.id";
	$query = "$querySql where o.bh_mode$bh_mode_string and sign.is_check<>0 and o.a_uid in ($bankIds) order by sign.is_check asc,sign.audit_time desc,sign.id asc limit $page";
    $orderList = $orderService->getOrderListBySql($query);
//	$sql = $empire->query($query);
	// 获取订单关联的需求信息
	$i = 0;
	$demandService = DemandService::getInstance();
	foreach ($orderList as $oneSignAndOrder) {
//        $oneDemand = $empire->fetch1("SELECT d.project,d.guarantee_date1,d.guarantee_date2,d.jsdw_name,d.money,d.cname as guaranteed FROM {$dbtbpre}order o INNER JOIN {$dbtbpre}demand d ON o.demand_id = d.id where o.id={$oneSignAndOrder['did']}");
        $oneDemand = $demandService->getDemandBySql("SELECT d.project,d.guarantee_date1,d.guarantee_date2,d.jsdw_name,d.money,d.cname as guaranteed,d.is_encrypt,d.data_encrypt_id FROM {$dbtbpre}order o INNER JOIN {$dbtbpre}demand d ON o.demand_id = d.id where o.id={$oneSignAndOrder['did']}");
        $ds_data[$i] = array_merge($oneSignAndOrder, $oneDemand);
        $i++;
    }
//	while ($oneSignAndOrder = $empire->fetch($sql)){
//        $oneDemand = $empire->fetch1("SELECT d.project,d.guarantee_date1,d.guarantee_date2,d.jsdw_name,d.money,d.cname as guaranteed FROM {$dbtbpre}order o INNER JOIN {$dbtbpre}demand d ON o.demand_id = d.id where o.id={$oneSignAndOrder['did']}");
//        $ds_data[$i] = array_merge($oneSignAndOrder, $oneDemand);
//        $i++;
//    }

    // 审核状态
    foreach ($ds_data as $key => $oneSignAndOrderAndDemand) {
    	if ($oneSignAndOrderAndDemand['is_check'] == 1) $ds_data[$key]['state'] = '待审核';
    	if ($oneSignAndOrderAndDemand['is_check'] == 2) $ds_data[$key]['state'] = '已通过';
        if ($oneSignAndOrderAndDemand['is_check'] == 3) $ds_data[$key]['state'] = '已驳回';
    }

    $data['ds_data'] = $ds_data;

    exit(json_encode($data));

}  elseif ($action == 'bk_gua_detail') {//微信审核保函详情
    $uid = $get['uid'];
	$res = getAllBanks($uid);
    if ($res['code'] != 0) exit(json_encode(['status' => 0, 'msg' => '该分行审核暂不可用！']));
    $bankIds = $res['data']['bankIds'];
    $branchBank = $res['data']['branchBank'];
    if ( ! $branchBank['uid']) exit(json_encode(['status'=>0,'msg'=>'未有权限']));
    $signatureId = $get['gua_id'];

    $data = $empire->fetch1("SELECT s.pdf_url2,d.no,d.project,d.ktype,d.guarantee_date1,d.guarantee_date2,d.money,d.jsdw_name,o.price,s.backletter_num,s.signature_time,s.id,s.is_check,d.bbzr_name,d.bbzr_name,d.is_encrypt as demand_is_encrypt,d.data_encrypt_id as demand_data_encrypt_id,o.is_fd,o.a_cname,o.cname,o.b_cname,o.is_encrypt as order_is_encrypt,o.data_encrypt_id as order_data_encrypt_id,s.userid FROM {$dbtbpre}signature s,{$dbtbpre}order o,{$dbtbpre}demand d where s.did=o.id and o.demand_id=d.id and s.is_signature=1 and s.is_del=0 and d.del=0 and s.is_check<>0 and o.a_uid in ($bankIds) and s.id='$signatureId'");
    $demandData = $data;
    $demandData['is_encrypt'] = $demandData['demand_is_encrypt'];
    $demandData['data_encrypt_id'] = $demandData['demand_data_encrypt_id'];
    $data = DemandService::getInstance()->getViewDemandData($demandData);
    $orderData = $data;
    $orderData['is_encrypt'] = $orderData['order_is_encrypt'];
    $orderData['data_encrypt_id'] = $orderData['order_data_encrypt_id'];
    $data = OrderService::getInstance()->getViewOrderData($orderData);
/*    $orderService = OrderService::getInstance();
    $orderService->getOrderListBySql("SELECT s.pdf_url2,d.no,d.project,d.ktype,d.guarantee_date1,d.guarantee_date2,d.money,d.jsdw_name,o.price,s.backletter_num,s.signature_time,s.id,s.is_check,d.bbzr_name,d.bbzr_name,o.is_fd,o.a_cname,o.cname,o.b_cname,s.userid FROM {$dbtbpre}signature s,{$dbtbpre}order o,{$dbtbpre}demand d where s.did=o.id and o.demand_id=d.id and s.is_signature=1 and s.is_del=0 and d.del=0 and s.is_check<>0 and o.a_uid in ($bankIds) and s.id='$signatureId'");
   */ if ($data['is_check']) {
        $audit_sql = $empire->query("SELECT * FROM {$dbtbpre}ccbaudit WHERE sid='$signatureId' ORDER BY newstime DESC , id DESC");
        while ($r=$empire->fetch($audit_sql)){
            $data['audit'][]=$r;
        }
    }

    // 签章人
    if ($data['is_fd'] == 1) {
		$data['signator'] = $data['a_cname'];
	} else {
		if ($data['ktype'] == 1) {
			$data['signator'] = $data['a_cname'];
		} else if ($data['ktype'] == 2) {
			$data['signator'] = $data['b_cname'];
		}
	}

    if ( ! $data) exit(json_encode(['status'=>0,'msg'=>'参数错误！']));
    exit(json_encode($data));

} elseif ($action == 'bk_gua_audit') {
	$uid = $get['uid'];
	$signatureId = $get['audit_id'];
	$approveStatus = $get['drg'];
	$content = $get['content'];

	// 判断是否为空
    if(!$approveStatus) exit(json_encode(['status'=>0,'msg'=>'参数错误！']));
    $res = getAllBanks($uid);
    if ($res['code'] != 0) exit(json_encode(['status' => 0, 'msg' => '该分行审核暂不可用！']));
    $bankIds = $res['data']['bankIds'];
    $branchBank = $res['data']['branchBank'];

    $orderService = OrderService::getInstance();
    $order = $orderService->getOrder(['signature_id' => $signatureId,'a_uids' => $bankIds]);
//    $order = $empire->fetch1("select * from {$dbtbpre}order where signature_id='$signatureId' and a_uid in ($bankIds)");
    if (!$order['id']) exit(json_encode(['status'=>0,'msg'=>'参数错误！']));
    // 订单状态
    if ($order['zt'] != 0) exit(json_encode(['status'=>0,'msg'=>'该订单状态不允许该操作！']));
    $signature = $empire->fetch1("select * from {$dbtbpre}signature where id=".$signatureId." and is_check=1");
//    $demand = $empire->fetch1("select * from {$dbtbpre}demand where id='".$order['demand_id']."'");
    $demandService = DemandService::getInstance();
    $demand = $demandService->getDemand(['id' => $order['demand_id']]);
    if(!$signature['id']) exit(json_encode(['status'=>0,'msg'=>'参数错误！']));

    $inspectons_create_time = time();

    if ($approveStatus == 3) {
        // 审核驳回
        // 状态修改、记录
        if ($order['ktype'] == 1) {
            // 直开式银行
            $empire->query("update {$dbtbpre}order set state='4',step='4' where id='$order[id]' limit 1");
        } else if ($order['ktype'] == 2) {
            // 分离式银行
            $empire->query("update `{$dbtbpre}order` set state='4',step='4',isf=1 where id='$order[id]' limit 1");
        }
    } else if ($approveStatus == 2) {
        // 审核通过
        if ($order['step'] != 4) exit(json_encode(['status'=>0,'msg'=>'该订单状态不允许该操作！']));
        // 操作副单
        $fdsql = $empire->query("update `{$dbtbpre}order` set state='5',step='5',zt='1',out_time=$inspectons_create_time where signature_id='$signatureId'");
        if ($order['zid']) { // 如果是分离式则需要操作主单
            $zdsql = $empire->query("update `{$dbtbpre}order` set state='5',step='5',zt='1',is_insured_read='1',is_guarantee_read='1',out_time=$inspectons_create_time where id='$order[zid]'");
        }
        if ( ! $fdsql) exit(json_encode(['status'=>0,'msg'=>'操作失败！']));       

        // 发送提醒消息
        if ($order['ktype'] == 1) {
            // 直开式银行
            // 开函
            $inspectionsService = InspectionsService::getInstance();
            $inspectionsService->insertInspections([
                'userid' => $order['a_uid'],
                'y_userid' => $signature['y_userid'],
                'order_id' => $order['id'],
                'btc_id' => $demand['btc_id'],
                'signature_id' => $signature['id'],
                'demand_no' => $demand['no'],
                'project' => $demand['project'],
                'project_no' => $demand['number'],
                'signature_no' => $signature['backletter_num'],
                'date1' => $demand['guarantee_date1'],
                'date2' => $demand['guarantee_date2'],
                'money' => $demand['money'],
                'je' => $order['price'],
                'ktype' => $demand['ktype'],
                'pdfurl' => $signature['pdf_url2'],
                'pass' => $signature['pass'],
                'eno' => $signature['eno'],
                'create_time' => $inspectons_create_time,
                'gtype' => $demand['gtype'],
                'block_number' => $demand['block_number'],
                'guarantee_variety' => $demand['guarantee_variety'],
                'beneficiary_name' => $demand['jsdw_name'],
                'guarantor_name' => $demand['bbzr_name'],
            ]);
//            $empire->query("insert into {$dbtbpre}inspections(userid,y_userid,order_id,btc_id,signature_id,demand_no,project,project_no,signature_no,date1,date2,money,je,ktype,pdfurl,pass,eno,create_time,gtype,block_number) values('".$order['a_uid']."','".$signature['y_userid']."','".$order['id']."','".$demand['btc_id']."','".$signature['id']."','".$demand['no']."','".$demand['project']."','".$demand['number']."','".$signature['backletter_num']."','".$demand['guarantee_date1']."','".$demand['guarantee_date2']."','".$demand['money']."','".$order['price']."','".$demand['ktype']."','".$signature['pdf_url2']."','".$signature['pass']."','".$signature['eno']."','".$inspectons_create_time."','".$demand['gtype']."','".$demand['block_number']."');");
            // 资金操作记录
            include_once dirname(dirname(__DIR__))."/config.inc.php";
            include_once dirname(dirname(__DIR__))."/client/client.php";
            $note = "开函成功，资金解冻";
            $ret = OrderUnfrozenCapital($order, $note);
        } else if ($order['ktype'] == 2) {
            // 分离式银行
            // 开函
            $inspectionsService->insertInspections([
                'userid' => $order['a_uid'],
                'z_userid' => $signature['z_userid'],
                'order_id' => $order['id'],
                'btc_id' => $demand['btc_id'],
                'signature_id' =>  $signature['id'],
                'z_order_id' => $order['zid'],
                'demand_no' => $demand['no'],
                'project' => $demand['project'],
                'project_no' => $demand['number'],
                'signature_no' => $signature['backletter_num'],
                'date1' => $demand['guarantee_date1'],
                'date2' => $demand['guarantee_date2'],
                'money' => $demand['money'],
                'je' => $order['price'],
                'ktype' => $demand['ktype'],
                'pdfurl' => $signature['pdf_url2'],
                'pass' => $signature['pass'],
                'eno' => $signature['eno'],
                'create_time' => $inspectons_create_time,
                'gtype' => $demand['gtype'],
                'y_userid' => $signature['y_userid'] ,
                'block_number' => $demand['block_number'],
                'guarantee_variety' => $demand['guarantee_variety'],
                'beneficiary_name' => $demand['jsdw_name'],
                'guarantor_name' => $demand['bbzr_name'],
            ]);
//            $empire->query("insert into {$dbtbpre}inspections(userid,z_userid,order_id,btc_id,z_order_id,signature_id,demand_no,project,project_no,signature_no,date1,date2,money,je,ktype,pdfurl,pass,eno,create_time,gtype,y_userid,block_number) values('".$order['a_uid']."','".$signature['z_userid']."','".$order['id']."','".$demand['btc_id']."','".$order['zid']."','".$signature['id']."','".$demand['no']."','".$demand['project']."','".$demand['number']."','".$signature['backletter_num']."','".$demand['guarantee_date1']."','".$demand['guarantee_date2']."','".$demand['money']."','".$order['price']."','".$demand['ktype']."','".$signature['pdf_url2']."','".$signature['pass']."','".$signature['eno']."','".$inspectons_create_time."','".$demand['gtype']."','".$signature['y_userid']."','".$demand['block_number']."');");
            // 资金操作记录
            include_once dirname(dirname(__DIR__))."/config.inc.php";
            include_once dirname(dirname(__DIR__))."/client/client.php";
            $note = "开函成功，资金解冻";
            $ret = OrderUnfrozenCapital($order, $note);
        }
        // 设置 swoole 任务回调
        add_redirect_url_task($demand['no'], $inspectons_create_time);

        // 记录需求已完成
        $sql = $empire->query("update {$dbtbpre}demand set state=3 where id='$order[demand_id]' limit 1");
    }
    // 记录审核信息
    $signature_id=$empire->query("update {$dbtbpre}signature set is_check='$approveStatus',audit_time='$inspectons_create_time' where id=".$signatureId."");
    $ccbaudit=$empire->query("insert into {$dbtbpre}ccbaudit(sid,uid,newstime,content,status) VALUES('$signatureId','$branchBank[uid]','$inspectons_create_time','$content','$approveStatus')");
    if($signature_id && $ccbaudit){
    	exit(json_encode(['status'=>0,'msg'=>'审核成功！']));
    }else{
        exit(json_encode(['status'=>0,'msg'=>'审核失败！']));
    }
} elseif ($action=='orders'){
    $expire_type = $get['expire_type'];
    $ret['page'] = $get['page'];
    $ret['limit'] = $get['limit'];
    $get_where = $get['where'];

    $offset = ($get['page']-1)*$get['limit'];
    $orderService = OrderService::getInstance();
    if ($expire_type){
        $expire_date = time()-$get['expire_date'];
        $where .= " od.time<{$expire_date} and o.is_fd=0 ";
        if ($expire_type==1){
            //担保逾期报价
            $where .= " and od.step=2 and o.state=2 and o.step=2 and o.ktype!=1 ".$get_where;
            $sql_total .= " select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_sum_total .= " select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_data = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} order by o.id desc limit {$offset},".$ret['limit'];
        }elseif($expire_type==2){
            //逾期未支付
            $where .= " and od.step=3 and o.state=3 and o.step=3 ".$get_where;
            $sql_total .= " select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_sum_total .= " select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_data = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} order by o.id desc limit {$offset},".$ret['limit'];
        }elseif($expire_type==3){
            //逾期提交资料
            $where .= " and od.step=6 and o.state=6 and o.step=6 and o.ktype=2 ".$get_where;
            $sql_total .= " select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_sum_total .= " select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where}  ";
            $sql_data = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} order by o.id desc limit {$offset},".$ret['limit'];
        }elseif($expire_type==4){
            //银行逾期报价
            $where .= " and od.step=1 and o.state=1 and o.step=1 and o.ktype!=3 ".$get_where;
            $sql_total .= " select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where}  ";
            $sql_sum_total .= " select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where}  ";
            $sql_data = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where} order by o.id desc limit {$offset},".$ret['limit'];
        }elseif($expire_type==5){
            //担保逾期未支付
            $where .= " and od.step=2 and o.state=2 and o.step=2 and o.ktype=2 ".$get_where;
            $sql_total .= " select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where}  ";
            $sql_sum_total .= " select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where}  ";
            $sql_data = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where} order by o.id desc limit {$offset},".$ret['limit'];
        }
        $ret['total'] = $empire->fetch1($sql_total)['total'];
        $ret['price_total'] = $empire->fetch1($sql_sum_total)['total'];
        $data = [];
//        $query = $empire->query($sql_data);
        $orderList = $orderService->getOrderListBySql($sql_data);
//        while($item = $empire->fetch($query)){
        foreach ($orderList as $item){
            if ($item['ktype']==2 && $item['fid']>0){
                $item['dno_child'] = $empire->fetch1("select dno from {$dbtbpre}order where id=".$item['fid'])['dno'];
            }
            $item['last_time'] = $empire->fetch1("select time from {$dbtbpre}order_data where did=".$item['id']." order by id desc")['time'];
            $data[] = $item;
        }
    }else{
        $where = " o.id>0 and o.is_fd=0 ";
        if ($get_where){
            $where .= $get_where;
        }
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id where {$where} ")['total'];
        $ret['price_total'] = $empire->fetch1("select sum(o.price) as total from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id where {$where} ")['total'];

        $data = [];
        $sql = "select o.*,d.no as demand_no from {$dbtbpre}order as o inner join {$dbtbpre}demand as d on d.id = o.demand_id where {$where} order by o.id desc limit {$offset},".$ret['limit'];
//        $query = $empire->query($sql);
        $orderList = $orderService->getOrderListBySql($sql);
//        while($item = $empire->fetch($query)){
        foreach ($orderList as $item){
            if ($item['ktype']==2 && $item['fid']>0){
                $item['dno_child'] = $empire->fetch1("select dno from {$dbtbpre}order where id=".$item['fid'])['dno'];
            }
            $data[] = $item;
        }
    }

    $ret['data'] = $data;
    $ret['status'] = 200;
    exit(json_encode($ret));
}elseif($action=='order_total'){
    $start_date = $get['start_date'];
    $end_date = $get['end_date'];

    $where = " is_fd=0 ";
    if($start_date && $end_date) {
        $where .= " AND time >= {$start_date} AND time <= {$end_date} ";
    }elseif($start_date && !$end_date){
        $where .= " AND time >= {$start_date}";
    }elseif(!$start_date && $end_date){
        $where .= " AND time <= {$end_date} ";
    }

    //申请单数量
    $apply_total_where = $where;
    $ret['apply_total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order where {$apply_total_where} ")['total'];
    //已支付申请单
    $pay_apply_total_where = $where." and isf=1 ";
    $ret['pay_apply_total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order where {$pay_apply_total_where} ")['total'];
    //已支付金额
    $pay_amount_total_where = $where." and isf=1 ";
    $ret['pay_amount_total'] = $empire->fetch1("select sum(real_pay) as total from {$dbtbpre}order where {$pay_amount_total_where} ")['total'];
    $ret['pay_amount_total'] = $ret['pay_amount_total']?$ret['pay_amount_total']:'0.00';
    //已出函数量
    $success_total_where = $where." and isf=1 and state=5 ";
    $ret['success_total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order where {$success_total_where} ")['total'];
    //待出函数量
    $wait_success_total_where = $where." and isf=1 and state=4 ";
    $ret['wait_success_total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order where {$wait_success_total_where} ")['total'];

    $ret['status'] = 200;
    exit(json_encode($ret));
}elseif($action=='order_expire_total'){
    $expire_type = $get['expire_type'];
    $expire_date = time()-$get['expire_date'];
    $where = " od.time<{$expire_date} and o.is_fd=0 ";
    if ($expire_type==1){
        //担保逾期报价
        $where .= "  and od.step=2 and o.state=2 and o.step=2 and o.ktype!=1 ";
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} ")['total'];
    }elseif($expire_type==2){
        //逾期未支付
        $where .= " and od.step=3 and o.state=3 and o.step=3 ";
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} ")['total'];
    }elseif($expire_type==3){
        //逾期提交资料
        $where .= " and od.step=6 and o.state=6 and o.step=6  and o.ktype=2";
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}order_data as od on od.did=o.id where {$where} ")['total'];
    }elseif($expire_type==4){
        //银行逾期报价
        $where .= " and od.step=1 and o.state=1 and o.step=1 and o.ktype!=3 ";
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where} ")['total'];
    }elseif($expire_type==5){
        //担保逾期未支付
        $where .= " and od.step=2 and o.state=2 and o.step=2  and o.ktype=2";
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order as o inner join {$dbtbpre}order_data as od on od.did=o.fid where {$where} ")['total'];
    }

    $ret['status'] = 200;
    exit(json_encode($ret));
}elseif($action=='faccount_detail_orders'){
    $ret['page'] = $get['page'];
    $ret['limit'] = $get['limit'];
    $get_where = $get['where'];

    $offset = ($get['page']-1)*$get['limit'];
    $orderService = OrderService::getInstance();
    if (strpos($get_where,'btc_id') === false){
        $where = " id>0 and is_fd=0 ";
        if ($get_where){
            $where .= $get_where;
        }
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order where {$where} ")['total'];
        $ret['price_total'] = $empire->fetch1("select sum(price) as total from {$dbtbpre}order where {$where} ")['total'];

        $data = [];
        $sql = "select * from {$dbtbpre}order where {$where} order by id desc limit {$offset},".$ret['limit'];
//        $query = $empire->query($sql);
//        while($item = $empire->fetch($query)){
        $orderList = $orderService->getOrderListBySql($sql);
        foreach ($orderList as $item){
            if ($item['ktype']==2 && $item['fid']){
                $item['child_order'] = $empire->fetch1("select * from {$dbtbpre}order where id=".$item['fid']);
            }
            $data[] = $item;
        }
    }else{
        //交易中心,从demand表里查询btc_id
        $btc_uid = explode('=',$get_where);
        $btc = $empire->fetch1("select * from {$dbtbpre}ecms_btc where uid=".$btc_uid[1]);
        $where = " o.id>0 and o.is_fd=0 ";
        if ($get_where){
            $where .= " and d.btc_id=".$btc['id'];
        }
        $ret['total'] = $empire->fetch1("select count(*) as total from {$dbtbpre}order o inner join {$dbtbpre}demand d on d.id=o.demand_id where {$where} ")['total'];
        $ret['price_total'] = $empire->fetch1("select sum(o.price) as total from {$dbtbpre}order o inner join {$dbtbpre}demand d on d.id=o.demand_id where {$where} ")['total'];

        $data = [];
        $sql = "select o.* from {$dbtbpre}order o inner join {$dbtbpre}demand d on d.id=o.demand_id where {$where} order by o.id desc limit {$offset},".$ret['limit'];
//        $query = $empire->query($sql);
//        while($item = $empire->fetch($query)){
        $orderList = $orderService->getOrderListBySql($sql);
        foreach ($orderList as $item){
            if ($item['ktype']==2 && $item['fid']){
                $item['child_order'] = $empire->fetch1("select * from {$dbtbpre}order where id=".$item['fid']);
            }
            $data[] = $item;
        }
    }

    $ret['data'] = $data;
    $ret['status'] = 200;
    exit(json_encode($ret));
} else {

	exit(API_RETURN_FAILED);

}

/*
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

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

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

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
	$keycy=createRandomStr2($ckey_length);
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
		$vkeya=createRandomStr2($a_length);
		$vkeyb=createRandomStr2($b_length);
		$vkeyc=createRandomStr2($c_length);
		//$keyc=Do_strtoupper($keyc);
		return $vkeya.$keyc.$vkeyc.str_replace('=', '', base64_encode($result)).$vkeyb;
	}

}
//随机数
function createRandomStr2($length){ 
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
	$strlen = 62; 
	while($length > $strlen){ 
	$str .= $str; 
	$strlen += 62; 
	} 
	$str = str_shuffle($str); 
	return substr($str,0,$length); 
} 


function dsetcookie($var, $value, $life = 0, $prefix = 1) {
	global $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie($var, $value,
		$life ? $timestamp + $life : 0, $cookiepath,
		$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function dstripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

function uc_serialize2($arr, $htmlon = 0) {
	include_once UC_CLIENT_ROOT.'./lib/xml.class.php';
	return xml_serialize($arr, $htmlon);
}

function uc_unserialize2($s) {
	include_once UC_CLIENT_ROOT.'./lib/xml.class.php';
	return xml_unserialize($s);
}

function add_redirect_url_task($no, $inspectons_create_time){
    global $empire,$dbtbpre;
    //找交易中心id
    $demand = $empire->fetch1("select * from {$dbtbpre}demand where no = '".$no."'");
    $inspectons = $empire->fetch1("select * from {$dbtbpre}inspections where demand_no='".$no."'");
    if ($demand['btc_id']){
        $enter_institution_api = $empire->fetch1("select * from {$dbtbpre}ecms_enter_institution_api where enter_institution_id = ".$demand['btc_id']);
        if ($enter_institution_api['redirect_url']){
            //写入交易中心出函成功回调任务表
            $empire->query("insert into {$dbtbpre}redirect_url_task set redirect_url='".$enter_institution_api['redirect_url']."',status=200,msg='success',btc_id=".$demand['btc_id'].",api_secret='".$enter_institution_api['api_secret']."',demand_no='".$demand['no']."',project_name='".$demand['project']."',project_number='".$demand['number']."',pass='".$inspectons['pass']."',eno='".$inspectons['eno']."',guarantee_done_time=".$inspectons_create_time.",created_at=".time());
        }
    }
}

function getAllBanks($uid)
{
	global $empire,$dbtbpre;
	// 是否分行
	$branchBankQuery = "select * from {$dbtbpre}ecms_enter_institution where type=6 and uid=$uid";
	$branchBank = $empire->fetch1($branchBankQuery);
	if (!$branchBank['id']) return ['code' => -1];
	// 获取分行下的所有支行
	$banksQuery = "select uid from {$dbtbpre}ecms_enter_institution where branch_id=$branchBank[id] and type=3";
	$sql = $empire->query($banksQuery);
	$banks = [];
	while ($bank = $empire->fetch($sql)) {
	    $banks[] = $bank;
	}
	$bankIds = $banks ? implode(',', array_column($banks, 'uid')) : 0;

	return [
		'data' => ['bankIds' => $bankIds, 'branchBank' => $branchBank],
		'code' => 0
	];
}