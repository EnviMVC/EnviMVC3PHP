<?php

umask(0);

if (!isset($argv[3])) {
    eecho('引数が足りません。');
    die;
}

$project_dir = $current_work_dir;
while (!is_file($project_dir.'envi.prj') && strlen($project_dir) > 2) {
    $project_dir = dirname($project_dir).DIRECTORY_SEPARATOR;
}


$arr = array('___class_name___' => $argv[2].'_'.$argv[3], '___app_name___' => $argv[2], '___project_dir___' => $project_dir);

if (!is_dir($project_dir.'db'.DIRECTORY_SEPARATOR.'migrate')) {
    $cmd = 'mkdir -p '.$project_dir.'db'.DIRECTORY_SEPARATOR.'migrate';
    echo `$cmd`;
}
if (!is_file($project_dir.'db'.DIRECTORY_SEPARATOR.'env')) {
    copy(dirname(__FILE__).DIRECTORY_SEPARATOR.'migration'.DIRECTORY_SEPARATOR.'env', $project_dir.'db'.DIRECTORY_SEPARATOR.'env');
}
$contents = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'migration'.DIRECTORY_SEPARATOR.'migration.php');
$contents = strtr($contents, $arr);
$file_path = $project_dir.'db'.DIRECTORY_SEPARATOR.'migrate'.DIRECTORY_SEPARATOR.$argv[2].'_'.date('YmdHis').'_'.$argv[3].'.php';
file_put_contents($file_path, $contents);

echo $file_path,"\n";
