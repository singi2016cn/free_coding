<?php namespace DBDiff\SQLGen;


class MigrationGenerator {

    public static function generate($diffs, $method) {
        foreach ($diffs as &$diff) {
            $reflection = new \ReflectionClass($diff);
            $className = $reflection->getShortName();
            $sqlGenClass = __NAMESPACE__."\\DiffToSQL\\".$className."SQL";

            $gen = new $sqlGenClass($diff);
            $diff->sql = $gen->$method();

            $diff->type = $className;

            if(in_array($className,['AddTable','DropTable'])){
                unset($diff->struct);
            }
        }
       return $diffs;
    }

}
