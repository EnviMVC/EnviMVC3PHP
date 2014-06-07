<?php
/**
 * マニュアル
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


// ヘルプ表示
cecho('Name:', 33);
cecho('    envi ', 34, '\n         Enviに対する操作を行います');
cecho('Usage:', 33);
    echo '    envi task_name [arguments] [Options]'."\n";
cecho('Task name:', 33);
cecho('    console-log                                         ', 32, '\n       コンソールログを見ます\n       envi console-log <log_dir> <app_key> (<system:console:query:included_files>) (<backtrace:performance:log_text:memory_get_usage>');

exit;
die;