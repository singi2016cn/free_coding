<?php namespace DBDiff\DB\Schema;

use Diff\Differ\MapDiffer;
use Diff\Differ\ListDiffer;

use DBDiff\Diff\AlterTableEngine;
use DBDiff\Diff\AlterTableCollation;

use DBDiff\Diff\AlterTableAddColumn;
use DBDiff\Diff\AlterTableChangeColumn;
use DBDiff\Diff\AlterTableDropColumn;

use DBDiff\Diff\AlterTableAddKey;
use DBDiff\Diff\AlterTableChangeKey;
use DBDiff\Diff\AlterTableDropKey;

use DBDiff\Diff\AlterTableAddConstraint;
use DBDiff\Diff\AlterTableChangeConstraint;
use DBDiff\Diff\AlterTableDropConstraint;

use DBDiff\SQLGen\Schema\SQL;

use DBDiff\Logger;


class TableSchema {

    function __construct(&$source,&$target) {
        $this->source = &$source;
        $this->target = &$target;
    }

    public function getSchema($schema) {
        // collation & engine
        preg_match('/ENGINE=([a-zA-Z]+)/',$schema,$engine);
        preg_match('/COLLATION=([a-zA-Z]+)/',$schema,$collation);
        $engine = isset($engine[1])?$engine[1]:'';
        $collation = isset($collation[1])?$collation[1]:'';

        $lines = array_map(function($el) { return trim($el);}, explode("\n", $schema));
        $lines = array_slice($lines, 1, -1);
        
        $columns = [];
        $keys = [];
        $constraints = [];
        foreach ($lines as $line) {
            preg_match("/`([^`]+)`/", $line, $matches);
            $name = $matches[1];
            $line = trim($line, ',');
            if ($this->startsWith($line, '`')) { // column
                $columns[$name] = $line;
            } else if ($this->startsWith($line, 'CONSTRAINT')) { // constraint
                $constraints[$name] = $line;
            } else { // keys
                $keys[$name] = $line;
            }
        }

        return [
            'engine'      => $engine,
            'collation'   => $collation,
            'columns'     => $columns,
            'keys'        => $keys,
            'constraints' => $constraints
        ];
    }

    public function getDiff($table) {
        //Logger::info("Now calculating schema diff for table `$table`");
        
        $diffSequence = [];
        $sourceSchema = $this->getSchema($this->source[$table]);
        $targetSchema = $this->getSchema($this->target[$table]);
        // Engine
        $sourceEngine = $sourceSchema['engine'];
        $targetEngine = $targetSchema['engine'];
        if ($sourceEngine != $targetEngine) {
            $diffSequence[] = new AlterTableEngine($table, $sourceEngine, $targetEngine);
        }

        // Collation
        $sourceCollation = $sourceSchema['collation'];
        $targetCollation = $targetSchema['collation'];
        if ($sourceCollation != $targetCollation) {
            $diffSequence[] = new AlterTableCollation($table, $sourceCollation, $targetCollation);
        }

        // Columns
        $sourceColumns = $sourceSchema['columns'];
        $targetColumns = $targetSchema['columns'];
        $differ = new MapDiffer();
        $diffs = $differ->doDiff($targetColumns, $sourceColumns);
        foreach ($diffs as $column => $diff) {
            if ($diff instanceof \Diff\DiffOp\DiffOpRemove) {
                $diffSequence[] = new AlterTableDropColumn($table, $column, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpChange) {
                $diffSequence[] = new AlterTableChangeColumn($table, $column, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpAdd) {
                $diffSequence[] = new AlterTableAddColumn($table, $column, $diff);
            }
        }

        // Keys
        $sourceKeys = $sourceSchema['keys'];
        $targetKeys = $targetSchema['keys'];
        $differ = new MapDiffer();
        $diffs = $differ->doDiff($targetKeys, $sourceKeys);
        foreach ($diffs as $key => $diff) {
            if ($diff instanceof \Diff\DiffOp\DiffOpRemove) {
                $diffSequence[] = new AlterTableDropKey($table, $key, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpChange) {
                $diffSequence[] = new AlterTableChangeKey($table, $key, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpAdd) {
                $diffSequence[] = new AlterTableAddKey($table, $key, $diff);
            }
        }

        // Constraints
        $sourceConstraints = $sourceSchema['constraints'];
        $targetConstraints = $targetSchema['constraints'];
        $differ = new MapDiffer();
        $diffs = $differ->doDiff($targetConstraints, $sourceConstraints);
        foreach ($diffs as $name => $diff) {
            if ($diff instanceof \Diff\DiffOp\DiffOpRemove) {
                $diffSequence[] = new AlterTableDropConstraint($table, $name, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpChange) {
                $diffSequence[] = new AlterTableChangeConstraint($table, $name, $diff);
            } else if ($diff instanceof \Diff\DiffOp\DiffOpAdd) {
                $diffSequence[] = new AlterTableAddConstraint($table, $name, $diff);
            }
        }

        return $diffSequence;
    }
    function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }

        return false;
    }
}
