<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/12/7
 * Time: 10:11
 */

require_once __DIR__.'/config/db.php';

$company = $db->select('uc_company',['user_id','company_name']);

if ($company){
    foreach($company as $item){
        $ret = $db->select('uc_faccount','*',['uid'=>$item['user_id']]);
        if (!$ret){
            $insert['uid'] = $item['user_id'];
            $insert['company_name'] = $item['company_name'];
            $db->insert('uc_faccount',$insert);
        }
    }
}