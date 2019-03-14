<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/1/30
 * Time: 13:35
 */

require_once "config.inc.php";
require_once "client/client.php";


$insert = [
    'user_id'=>1,
    'grade_id'=>1,
    'company_name'=>'大大分有限公司',
    'industry_type'=>1,
    'company_address'=>'深圳',
    'bank_reg_num'=>'12r214gt3g',
    'reg_money'=>63000,
    'organization_id'=>1,
    'threeinone'=>1,
    'social_credit_code'=>'5s5dfwef5e5gw',
    'orgnization_code'=>'weg3453535',
    'business_licence_cert'=>'https://company-image.oss-cn-szfinance.aliyuncs.com/a/sdg.jpg',
    'bank_reg_cert'=>'https://company-image.oss-cn-szfinance.aliyuncs.com/a/sdg.jpg',
    'legal_identity_cert'=>'https://company-image.oss-cn-szfinance.aliyuncs.com/a/sdg.jpg',
    'operator_identity_cert'=>'https://company-image.oss-cn-szfinance.aliyuncs.com/a/sdg.jpg',
    'certificate'=>'https://company-image.oss-cn-szfinance.aliyuncs.com/a/sdg.jpg',
    'operator_name'=>'1213',
    'operator_identity_num'=>'2gtwegwe',
    'operator_phone'=>'1365847845',
    'legal_person'=>'32gswdeg',
    'legal_identity_num'=>'45g4wgwe',
    'legal_phone'=>'13458475896'
];
$ret = uc_edit_company(306,['bank_account'=>'建设银行','basic_account_num'=>'56878d9756896']);
var_export($ret);

//$ret = uc_get_change_history(1);
//var_export($ret);