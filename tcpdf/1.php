<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/3/4
 * Time: 16:09
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h1>Hello world!</h1>');
$mpdf->Output();