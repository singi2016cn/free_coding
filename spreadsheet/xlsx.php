<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/6/4
 * Time: 9:57
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

error_reporting(-1);

use \PhpOffice\PhpSpreadsheet\IOFactory;


$spreadsheet = IOFactory::load("book1.xlsx");

$hello = $spreadsheet->getActiveSheet();

var_dump($hello);


