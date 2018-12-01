<?php


/**
 * @param $password
 * @param mixed ...$param
 * @return mixed
 */
function getSign($password, ...$param){if ($param){foreach($param as $item){$password.=$item;}}return str_replace(['\r', '\n'],'' ,base64_encode($password));}

echo getSign('65484566');