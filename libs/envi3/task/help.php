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
 * @copyright  2011-2012 Artisan Project
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      File available since Release 1.0.0
 */


if (isOption('--help') || isOption('-h') || isOption('-?') || !isset($argv[1])) {
    // ヘルプ表示
    cecho('Name:', 33);
    cecho('    envi ', 34, '\n         Enviに対する操作を行います');
    cecho('Usage:', 33);
    echo '    envi task_name [arguments] [Options]'."\n";
    cecho('Options:', 33);
    cecho('    --help,-h,-?                         ', 32, '\n         このヘルプメッセージを表示します。');
    cecho('    --debug                              ', 32, '\n         タスクをデバッグモードで実行する。');
    cecho('Available tasks:', 33);
    cecho('    -help                                ', 32, '\n         マニュアルの表示');
    cecho('build:', 33);
    cecho('    -model                               ', 32, '\n         OrMapモデルオブジェクトを生成する');
    cecho('    -query                               ', 32, '\n         Create文を作成する');

    cecho('clear:', 33);
    cecho('    -cache                               ', 32, '\n         キャッシュの削除(cc)');
    cecho('init:', 33);
    cecho('    -project                             ', 32, '\n       プロジェクトの作成');
    cecho('    -app                                 ', 32, '\n       アプリケーション(アプリキー)の作成(app)');
    cecho('    -filesession                         ', 32, '\n       ファイルbaseのセッションディレクトリの作成');
    cecho('    -module                              ', 32, '\n       モジュールの作成(module)');
    cecho('    -action                              ', 32, '\n       アクションの作成(action)');
    cecho('    -view                                ', 32, '\n       ビューの作成(view)');

    exit;
    die;
}