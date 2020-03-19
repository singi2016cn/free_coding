<?php namespace DBDiff\DB;

use DBDiff\DB\Schema\DBSchema;
use DBDiff\DB\Schema\TableSchema;
use DBDiff\DB\Data\DBData;
use DBDiff\DB\Data\TableData;
use DBDiff\Exceptions\CLIException;


class DiffCalculator {
    
    public function getDiff($params) {
        // Connect and test accessibility
        if(!is_file($params->server1)){
            throw new CLIException('server1 '.$params->server1.' not exists');
        }
        if(!is_file($params->server2)){
            throw new CLIException('server2 '.$params->server2.' not exists');
        }

        // Schema diff
        $schemaDiff = [];
        if ($params->type !== 'data') {
            $dbSchema = new DBSchema($params);
            $schemaDiff = $dbSchema->getDiff();
        }

        return [
            'schema' => $schemaDiff
        ];

    }
}
