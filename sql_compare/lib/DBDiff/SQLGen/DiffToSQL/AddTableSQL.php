<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AddTableSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }
    
    public function getUp() {
        return  $this->obj->struct;
    }

    public function getDown() {
        $table = $this->obj->table;
        return "DROP TABLE `$table`;";
    }
}
