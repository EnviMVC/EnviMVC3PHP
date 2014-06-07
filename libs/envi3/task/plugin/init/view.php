<?php
/**
 *  Viewの追加タスク
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
if (!isset($argv[4])) {
    eecho('引数が足りません。');

}

$project_name = $argv[2];
$module_name = $argv[3];
$action_name = $argv[4];
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $project_name)) {
    eecho('英数と._-以外の文字は使えません。');
    die;
}
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $module_name)) {
    eecho('英数と._-以外の文字は使えません。');
    die;
}
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $action_name)) {
    eecho('英数と._-以外の文字は使えません。');
    die;
}


$project_dir = $current_work_dir;
while (!is_file($project_dir.'envi.prj') && strlen($project_dir) > 2) {
    $project_dir = dirname($project_dir).DIRECTORY_SEPARATOR;
}

if (!is_file($project_dir.'envi.prj')) {
    echo 'please change directory. envi project directory';
    die;
}

$module_dir = $project_dir."apps".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR;
$module_test_dir = $project_dir."tests".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."modulesTest".DIRECTORY_SEPARATOR;


$text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'View.class.php.snp');
$text = str_replace(array('%%module_name%%', '%%action_name%%'), array($module_name, $action_name), $text);

$list = array('default', 'success', 'error', 'confirm', 'commit');

if (!isset($argv[5])) {
    foreach ($list as $view_suffix) {
        if (!is_file($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$action_name."View_{$view_suffix}.class.php")) {
            file_put_contents($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$action_name."View_{$view_suffix}.class.php", $text);
        }
    }

} else {
    $view_suffix = strtolower($argv[5]);
    if (!is_file($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$action_name."View_{$view_suffix}.class.php")) {
        file_put_contents($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.$action_name."View_{$view_suffix}.class.php", $text);
    }
}

