<?php
/**
 *  プロジェクトの追加タスク
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
$arr[] = $current_work_dir.'apps';
$arr[] = $current_work_dir.'web';
$arr[] = $current_work_dir.'cache';
$arr[] = $current_work_dir.'config';
$arr[] = $current_work_dir.'logs';
$arr[] = $current_work_dir.'tests';
$ds = DIRECTORY_SEPARATOR;
foreach ($arr as $item) {
    mkdir($item);
    echo $item."\n";
}
touch($current_work_dir.'envi.prj');

