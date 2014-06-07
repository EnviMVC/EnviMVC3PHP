<?php
/**
 *  キャッシュのクリアタスク
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
$project_dir = $current_work_dir;
while (!is_file($project_dir.'envi.prj') && strlen($project_dir) > 2) {
    $project_dir = dirname($project_dir).DIRECTORY_SEPARATOR;
}

if (!is_file($project_dir.'envi.prj')) {
    echo 'please change directory. envi project directory';
    die;
}
$dir = realpath($project_dir.'/cache');
if (!is_dir($dir)) {
    return;
}
if (($handle = opendir($dir.'/'))) {
    while ($file = readdir($handle)) {
        if ($file === '..' || $file === '.') {
            continue;
        } elseif (is_file($dir . '/' . $file) && preg_match("/\\.envicc/", $file)) {
            unlink($dir . '/' . $file);
            echo $dir . '/' . $file."\n";
        }
    }
    closedir($handle);
}
