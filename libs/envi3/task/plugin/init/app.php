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
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $project_name)) {
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

$ds = DIRECTORY_SEPARATOR;
$arr[] = $project_dir."apps".$ds.$project_name.$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."modules".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds."controller".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds."common".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds."constant".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds."models".$ds;
$arr[] = $project_dir."apps".$ds.$project_name.$ds."libs".$ds."models".$ds."om";

// テスト用
$arr[] = $project_dir."tests".$ds.$project_name.$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."modulesTest".$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."libsTest".$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."libsTest".$ds."controllerTest".$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."libsTest".$ds."commonTest".$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."libsTest".$ds."constantTest".$ds;
$arr[] = $project_dir."tests".$ds.$project_name.$ds."libsTest".$ds."modelsTest".$ds;



$default_config_dir = dirname(__FILE__)."{$ds}..{$ds}..{$ds}..{$ds}default_config{$ds}";

foreach ($arr as $item) {
    mkdir($item);
    echo $item."\n";
}


copy($task_plugin_dir.$module.$ds.'data'.$ds.'scenario.php.snp', $project_dir."tests".$ds.$project_name.$ds.'scenario.php');
copy($task_plugin_dir.$module.$ds.'data'.$ds.'testCaseBase.php.snp', $project_dir."tests".$ds.$project_name.$ds.'testCaseBase.php');
copy($task_plugin_dir.$module.$ds.'data'.$ds.'actionBase.class.php.snp', $project_dir."apps".$ds.$project_name.$ds."libs".$ds."controller".$ds.'actionBase.class.php');
copy($task_plugin_dir.$module.$ds.'data'.$ds.'viewBase.class.php.snp', $project_dir."apps".$ds.$project_name.$ds."libs".$ds."controller".$ds.'viewBase.class.php');

copy($default_config_dir.'main.yml', $project_dir."config{$ds}".$project_name.'.yml');
copy($default_config_dir.'main_databases.yml', $project_dir."config{$ds}".$project_name.'_databases.yml');
copy($default_config_dir.'main_di_container.yml', $project_dir."config{$ds}".$project_name.'_di_container.yml');
copy($default_config_dir.'main_web_type.yml', $project_dir."config{$ds}".$project_name.'_web_type.yml');
copy($default_config_dir.'main_send_mail.yml', $project_dir."config{$ds}".$project_name.'_send_mail.yml');
copy($default_config_dir.'main_schema.yml', $project_dir."config{$ds}".$project_name.'_schema.yml');

copy($default_config_dir.'main_test.yml', $project_dir."tests".$ds.$project_name.'_test.yml');


touch($project_dir."apps".$ds.$project_name.$ds."modules".$ds.'config.php');


$text = file_get_contents($task_plugin_dir.$module.$ds.'data'.$ds.'main.php.snp');
$text = str_replace(array('%%app_name%%'), array($project_name), $text);
$text = str_replace(array('%%base_dir%%'), array($base_dir), $text);
$text = str_replace(array('%%envi_dir%%'), array($envi_dir), $text);
$text = str_replace(array('%%project_dir%%'), array($project_dir), $text);
if (!is_file($project_dir.'web'.$ds.$project_name.'.php')) {
    file_put_contents($project_dir.'web'.$ds.$project_name.'.php', $text);
}
