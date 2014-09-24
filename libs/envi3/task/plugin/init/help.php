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
cecho('    init-project                                         ', 32, '\n       プロジェクトの作成');
cecho('    init-app [app name]                                  ', 32, '\n       アプリケーション(アプリキー)の作成(app)');
cecho('    init-filesession [app name]                          ', 32, '\n       ファイルbaseのセッションディレクトリの作成');
cecho('    init-module [app name] [module name]                 ', 32, '\n       モジュールの作成(module)');
cecho('    init-action [app name] [module name] [action name] [return_str(Option)]  ', 32, '\n       アクションの作成(action)');
cecho('    init-view [app name] [module name] [action name]     ', 32, '\n       ビューの作成(view)');
cecho('    init-scaffold [column_name]:[data_type]:[form_name],([not_null]),([unique])     ', 32, '\n       自動的にベースファイルを作成します');
cecho('    init-migratipn [app_name] [migration_name]            ', 32, '\n       マイグレーションクラスファイルを作成します。');

exit;
die;