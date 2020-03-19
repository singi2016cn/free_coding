<?php namespace DBDiff\Diff;


class AddTable {

    function __construct($table, $struct) {
        $this->table = $table;
        $this->struct = $struct;
    }
}
