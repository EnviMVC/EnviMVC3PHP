<?php

if (!isset($argv[2])) {
    eecho('引数が足りません。envi unittest-help');
    die;
}
if (!mb_ereg('^[0-9a-zA-Z_]*$', $argv[2])) {
    eecho('英数と_以外の文字は使えません。');
    die;
}

$contents = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'test.class.php.snp');

file_put_contents(getcwd().DIRECTORY_SEPARATOR.$argv[2].'Test.php', str_replace('%%class_name%%', $argv[2], $contents));

