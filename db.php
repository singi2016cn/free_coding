<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/6/21
 * Time: 14:53
 */

require_once './config/db.php';

$company = $db->select('uc_company','*');
if (!empty($company)){
    foreach($company as $company_item){
        $company_edit = $db->get('uc_company_edit','*',['company_name'=>$company_item['company_name']]);
        if (empty($company_edit)){
            $company_edit_insert = $company_item;
            unset($company_edit_insert['id']);
            unset($company_edit_insert['is_from_btc_api']);
            unset($company_edit_insert['chinapay_shop_no']);
            $company_edit_insert['company_id'] = $company_item['id'];
            $company_edit_insert['recDate'] = $company_item['rz_time'];

            $member = $db->get('uc_members','*',['uid'=>$company_item['user_id']]);

            $company_edit_insert['username'] = $member['username'];
            $company_edit_insert['realname'] = $member['realname'];
            $company_edit_insert['email'] = $member['email'];
            $company_edit_insert['phone'] = $member['phone'];
            $db->insert('uc_company_edit',$company_edit_insert);
            $uc_company_edit_insert_id = $db->id();
            $db->update('uc_company',['last_edit_id'=>$uc_company_edit_insert_id],['id'=>$company_item['id']]);

            var_dump($company_item);
        }
    }
}


