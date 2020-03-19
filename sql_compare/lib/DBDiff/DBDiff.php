<?php namespace DBDiff;

use DBDiff\Params\ParamsFactory;
use DBDiff\DB\DiffCalculator;
use DBDiff\SQLGen\SQLGenerator;


class DBDiff {
    
    public static function run($init_params) {

        // Increase memory limit
        ini_set('memory_limit', '512M');

        try {
            ParamsFactory::set($init_params);
            $params = ParamsFactory::get();
            // Diff
            $diffCalculator = new DiffCalculator;
            $diff = $diffCalculator->getDiff($params);
            // Empty diff
            if (empty($diff['schema']) && empty($diff['data'])) {
                return [];
            } else {
                // SQL
                $sqlGenerator = new SQLGenerator($diff);

                if ($params->include !== 'down') {
                     return $sqlGenerator->getUp();
                }
                if ($params->include !== 'up') {
                     return $sqlGenerator->getDown();
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }

    }
}
