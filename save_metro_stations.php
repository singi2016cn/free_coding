<?php
/**
 * Created by PhpStorm.
 * User: lishuting
 * Date: 2018/4/25
 * Time: 下午10:50
 */

// If you installed via composer, just use this code to requrie autoloader on the top of your projects.
require 'vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

// Initialize
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'free_coding',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '123456'
]);

$request_data = $_POST;

print_r($request_data);

$database->insert('metro_lines',[
    'name'=>$request_data['xls'],
    'created_at'=>time()
]);

$metro_lines_id = $database->id();

// Enjoy
if ($request_data['site']){
    foreach($request_data['site'] as $station){
        $database->insert('metro_stations', [
            'metro_line_id' => $metro_lines_id,
            'name' => $station['n'],
            'code' => $station['c'],
            'created_at'=>time()
        ]);
    }
}

echo json_encode(['status_code'=>200]);