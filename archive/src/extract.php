<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/1/16
 * Time: 14:19
 */

//$ret = extractZip('../backup/20190116_141846-backup.zip',$extractPath='../extract/');
$ret = getPathFiles('../backup',1);
var_dump($ret);

/**
 * 解压zip文件到指定目录
 * @param string $zipName 压缩文件名
 * @param string $extractPath 解压到的目录
 * @return bool|mixed 返回解压路径
 */
function extractZip($zipName, $extractPath){
    $zip = new ZipArchive;
    if ($zip->open($zipName) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        $zip_path = pathinfo(zip_entry_name(zip_read(zip_open($zipName))),PATHINFO_DIRNAME);
        return realpath($extractPath.$zip_path);
    } else {
        return false;
    }
}


function getPathFiles($path, $isFirst = false)
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
    if ($isFirst) {
        return $files[count($files)-1];
    }else{
        return $files;
    }
}