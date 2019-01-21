<?php

$zip = new ZipArchive();
$ret = $zip->open('a/application.zip', ZipArchive::OVERWRITE);
if ($ret !== TRUE) {
    printf('Failed with code %d', $ret);
} else {
    $directory = realpath(__DIR__.'/myphp-backup-files');
    $options = array('add_path' => 'backup/', 'remove_path' => $directory);
    $zip->addPattern('/\.(?:'.implode('|',['sql']).')$/', $directory, $options);
    $zip->close();
}