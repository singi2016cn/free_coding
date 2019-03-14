<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/1/31
 * Time: 15:46
 */

require_once __DIR__."/config/db.php";

//获取
$bh = $db->select('cz_ecms_bh',"*",['id[>=]'=>'13914']);
if ($bh){
    foreach($bh as $add){
        $guarantee_insert['userid'] = $add['userid'];
        $guarantee_insert['username'] = $add['username'];
        $guarantee_insert['title'] = $add['title'];
        $guarantee_insert['newstime'] = time();
        $guarantee_insert['number'] = $add['bhbh'];
        if ($add['pz'] == 1) {
            //履约
            $guarantee_insert['varieties'] = 2;
            $guarantee_insert['organ'] = $guarantee_insert['guarantor'] = $add['gname'];//担保机构
            $guarantee_insert['people1'] = $add['kfs'];//受益人是开发商
            $guarantee_insert['people2'] = $add['cbs'];//投保人是承包商
            $guarantee_insert['plannedStartDate'] = $add['kgrq'];
            $guarantee_insert['plannedEndDate'] = $add['jgrq'];
        } elseif ($add['pz'] == 2) {
            //支付
            $guarantee_insert['varieties'] = 5;
            $guarantee_insert['people1'] = $add['cbs'];//受益人是承包商
            $guarantee_insert['people2'] = $add['kfs'];//投保人是开发商
        }
        $guarantee_insert['state'] = 1;//保函状态,1担保中,2索赔中,3解保中,4已索赔,5已解保
        $guarantee_insert['mode'] = 1;//担保方式,1一般保证责任2连带保证责任3见索即付
        $guarantee_insert['amount1'] = $add['dbze'];//担保总额(元)
        $guarantee_insert['amount2'] = $add['bf'];//保费(元)
        $guarantee_insert['draft'] = 1;
        $guarantee_insert['zt'] = 2;
        $guarantee_insert['del'] = 0;
        $guarantee_insert['app_id'] = 7;
        $db->insert('uc_guarantee',$guarantee_insert);
    }
}

