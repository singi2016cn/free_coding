<?php namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;


class ParamsFactory {
    public static $params = array();
    public static function set($params){
        if(!is_array($params)){
            throw new CLIException("set params error");
        }
        self::$params = $params;
    }
    public static function get() {

        $params = new DefaultParams;

        $params = self::merge($params,self::$params);

        if (empty($params->server1)) {
            throw new CLIException("A server is required");
        }

        return $params;

    }
/*    public static function get() {

        $params = new DefaultParams;

        $cli = new CLIGetter;
        $paramsCLI = $cli->getParams();

        if (!isset($paramsCLI->debug)) {
            error_reporting(E_ERROR);
        }

        $fs = new FSGetter($paramsCLI);
        $paramsFS = $fs->getParams();
        $params = self::merge($params, $paramsFS);

        $params = self::merge($params, $paramsCLI);
        
        if (empty($params->server1)) {
            throw new CLIException("A server is required");
        }
        return $params;

    }*/

    protected static function merge($obj1, $obj2) {
        return (object) array_merge((array) $obj1, (array) $obj2);
    }
}