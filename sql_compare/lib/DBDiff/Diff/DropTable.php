<?php namespace DBDiff\Diff;


class DropTable {

    function __construct($table,$struct) {
        $this->table = $table;
        $this->struct = $struct;
    }
}
