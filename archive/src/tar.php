<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/1/16
 * Time: 14:22
 */

$ret = tarPathFiles('../backup/archive.tar','../backup_tmp');
var_dump($ret);

function tarPathFiles($tar_name,$tar_path,$tar_file_suffix=['sql']){
    try {
        $phar = new PharData($tar_name);
        $files = getPathFiles($tar_path);
        foreach ($files as $file){
            if (in_array(pathinfo($file,PATHINFO_EXTENSION),$tar_file_suffix)){
                $phar->setAlias();
                $phar->addFile($tar_path.'/'.$file);
            }
        }
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function getPathFiles($path)
{
    $files = [];
    if (is_dir($path)) {
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && pathinfo($file, PATHINFO_EXTENSION)) {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
    }
    return $files;
}
