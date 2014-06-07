<?php
/**
 *  アプリキーの追加タスク
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */

umask(0);
if (!isset($argv[2])) {
    eecho('引数が足りません。');
    die;
}
$project_name = $argv[2];

$project_dir = $current_work_dir;
while (!is_file($project_dir.'envi.prj') && strlen($project_dir) > 2) {
    $project_dir = dirname($project_dir).DIRECTORY_SEPARATOR;
}

if (!is_file($project_dir.'envi.prj')) {
    echo 'please change directory. envi project directory';
    die;
}

$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."1".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."2".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."3".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."4".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."5".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."6".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."7".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."8".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."9".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."0".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."a".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."b".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."c".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."d".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."e".DIRECTORY_SEPARATOR;
$arr[] = $project_dir."var/sessions".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."f".DIRECTORY_SEPARATOR;


foreach ($arr as $item) {
    mkdir($item);
    echo $item."\n";
}