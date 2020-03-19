<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class DropTableSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }
    
    public function getUp() {
        $table = $this->obj->table;
        return "DROP TABLE `$table`;";
    }

    public function getDown() {

        return $this->obj->struct;
    }

}
