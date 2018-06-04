<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/5/18
 * Time: 16:44
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use Respect\Validation\Validator as v;

var_dump(v::url()->validate('http://www.conzhu.com/public/images/home/guarantee/type3.png'));

var_dump(v::intVal()->min(1)->validate(1));