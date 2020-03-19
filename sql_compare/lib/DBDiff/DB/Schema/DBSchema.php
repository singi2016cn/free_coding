<?php namespace DBDiff\DB\Schema;

use Diff\Differ\ListDiffer;

use DBDiff\Params\ParamsFactory;
use DBDiff\Diff\SetDBCollation;
use DBDiff\Diff\SetDBCharset;
use DBDiff\Diff\DropTable;
use DBDiff\Diff\AddTable;
use DBDiff\Diff\AlterTable;



class DBSchema {


    
    function getDiff() {
        $params = ParamsFactory::get();

        $diffs = [];
        
        // Tables
        $sourceTables_data = $this->_get_tables($params->server1);
        $targetTables_data = $this->_get_tables($params->server2);
        $sourceTables = array_keys($sourceTables_data);
        $targetTables = array_keys($targetTables_data);

        $tableSchema = new TableSchema($sourceTables_data,$targetTables_data);

        if (isset($params->tablesToIgnore)) {
            $sourceTables = array_diff($sourceTables, $params->tablesToIgnore);
            $targetTables = array_diff($targetTables, $params->tablesToIgnore);
        }

        $addedTables = array_diff($sourceTables, $targetTables);
        foreach ($addedTables as $table) {
            $diffs[] = new AddTable($table, $sourceTables_data[$table]);
        }

        $commonTables = array_intersect($sourceTables, $targetTables);
        foreach ($commonTables as $table) {
            $tableDiff = $tableSchema->getDiff($table);
            $diffs = array_merge($diffs, $tableDiff);
        }
        $deletedTables = array_diff($targetTables, $sourceTables);
        foreach ($deletedTables as $table) {
            $diffs[] = new DropTable($table, $targetTables_data[$table]);
        }

        return $diffs;
    }

    protected function getDBVariable($connection, $var) {
        $result = $this->manager->getDB($connection)->select("show variables like '$var'");
        return $result[0]['Value'];
    }

    private function _get_tables($sqlfile)
    {
        $text = file_get_contents($sqlfile);
        preg_match_all('/CREATE TABLE `([^`]+)`[\s\S]*?;(?=[\s]*?(\n|$))/',$text,$res);
        foreach ($res[1] as $key=>$val){
            $tables[$val] = $res[0][$key];
        }
        return $tables;
    }

}
