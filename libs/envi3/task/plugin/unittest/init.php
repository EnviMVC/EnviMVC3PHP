<?php

if (!isset($argv[2])) {
    eecho('引数が足りません。envi unittest-help');
    die;
}
if (!mb_ereg('^[0-9a-zA-Z_]*$', $argv[2])) {
    eecho('英数と_以外の文字は使えません。');
    die;
}

$test_dir = $argv[2];

$ds = DIRECTORY_SEPARATOR;

$arr[] = getcwd().$ds.$test_dir.$ds;

foreach ($arr as $item) {
    mkdir($item);
    echo $item."\n";
}

$default_config_dir = dirname(__FILE__)."{$ds}..{$ds}..{$ds}..{$ds}default_config{$ds}";
copy($default_config_dir.'main_test.yml', getcwd().$ds.$test_dir.'_test.yml');
copy(dirname(__FILE__).$ds.'data'.$ds.'scenario.php.snp', getcwd().$ds.$test_dir.$ds.'scenario.php');
copy(dirname(__FILE__).$ds.'data'.$ds.'testCaseBase.php.snp', getcwd().$ds.$test_dir.$ds.'testCaseBase.php');

