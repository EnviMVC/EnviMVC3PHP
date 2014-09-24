<?php
/**
 *  コードの自動生成
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.4.0
 */
umask(0);
if (!isset($argv[4])) {
    eecho('引数が足りません。');
    die;
}
$i = 2;

// タイプの設定
$type = 'type:default';
if (strpos($argv[$i], 'type:')) {
    $type = $argv[$i];
    $i++;
}
list(, $type) = explode(':', $type, 2);

// プロジェクト名
$project_name = $argv[$i];
$i++;

// モジュール名＆モデル名
if (!strpos($argv[$i], ':')) {
    $module_name  = ($argv[$i]);
    $model_name  = ($argv[$i]);
} else {
    list($module_name, $model_name) = explode(':', $argv[$i], 2);
}
$module_name = camelize($module_name);
$model_name  = pascalize($model_name);
$i++;


// SpyCの読み込み
include_once ENVI_BASE_DIR.'spyc.php';


// 入力チェック
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $project_name)) {
    eecho('英数と._-以外の文字は使えません。');
    die;
}
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $module_name)) {
    eecho('英数と._-以外の文字は使えません。');
    die;
}
if (!preg_match('/^[a-zA-Z0-9.\-_]+$/', $model_name)) {
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


// モジュールの作成
if (!isset($argv[$i])) {
    eecho('引数が足りません。');
    die;
}


// ./module.php からのコピペ
// includeではうまく動かない
$module_dir = $project_dir."apps".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR;
$action_dir = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR.'actions'.DIRECTORY_SEPARATOR;
$template_dir = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;


$module_test_dir = $project_dir."tests".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."modulesTest".DIRECTORY_SEPARATOR;
$action_test_dir = $module_test_dir.DIRECTORY_SEPARATOR.$module_name.'Test';


$model_dir = $project_dir."apps".DIRECTORY_SEPARATOR.$project_name.DIRECTORY_SEPARATOR."libs".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;




$arr[] = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR;
$arr[] = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR;
$arr[] = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR;
$arr[] = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."actions".DIRECTORY_SEPARATOR;
$arr[] = $module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."libs".DIRECTORY_SEPARATOR;

// テスト用
$arr[] = $module_test_dir;
$arr[] = $action_test_dir;;


foreach ($arr as $item) {
    if (is_dir($item)) {
        continue;
    }
    mkdir($item);
    echo $item."\n";
}

$text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'actions.class.php.snp');
$text = str_replace(array('%%module_name%%'), array($module_name), $text);
if (!is_file($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."actions".DIRECTORY_SEPARATOR.'actions.class.php')) {
    file_put_contents($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."actions".DIRECTORY_SEPARATOR.'actions.class.php', $text);
}

$text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'views.class.php.snp');
$text = str_replace(array('%%module_name%%'), array($module_name), $text);
if (!is_file($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.'views.class.php')) {
    file_put_contents($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR.'views.class.php', $text);
}

$text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'config.php.snp');
$text = str_replace(array('%%module_name%%'), array($module_name), $text);
if (!is_file($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR.'config.php')) {
    file_put_contents($module_dir.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR.'config.php', $text);
}

// ./module.php からのコピペここまで

// scheme yaml
ob_start();
include $project_dir.'config'.DIRECTORY_SEPARATOR.$project_name.'_schema.yml';
$buff      = ob_get_contents();
ob_end_clean();

$schema_yaml = spyc_load($buff);

// ネームスペース(build-modelからのコピペ)
$model_name_space       = '';
$model_base_name_space  = '';

$model_name_space_use        = '';
$model_base_name_space_use   = '';
if (PHP_MINOR_VERSION >= 3) {
    $model_name_space            = isset($schema_yaml['SETTING']['model_name_space']) ? $schema_yaml['SETTING']['model_name_space']: '';
    $model_base_name_space       = isset($schema_yaml['SETTING']['model_base_name_space']) ? $schema_yaml['SETTING']['model_base_name_space']: '';
}
$dao_use = '';
if (strlen($model_name_space)) {
    $dao_use = 'use '.$model_name_space."\\".pascalize($model_name).'Peer;'."\n".'use '.$model_name_space."\\".pascalize($model_name).';'."\n";
    $model_name_space_use = 'namespace ' . $model_name_space .';'."\nuse \\EnviOrMapBase;\nuse \\EnviDBInstance;\nuse \\EnviDB;\nuse \\EnviException;\nuse \\EnviDBIBase;\n";

}
if (strlen($model_base_name_space)) {
    $model_base_name_space_use = 'namespace ' . $model_base_name_space .';'."\nuse \\EnviOrMapBase;\nuse \\EnviDBInstance;\nuse \\EnviDB;\nuse \\EnviException;\nuse \\EnviDBIBase;\n";
}

// ネームスペース(build-modelからのコピペここまで)


// アクションの作成

// 定義済みバリデータ
$validators = array(
    'equal'             => '_typeEqual',
    'notequal'          => '_typeNotEqual',
    'xdigit'            => '_typeXdigit',
    'digit'             => '_typeDigit',
    'cntrl'             => '_typeCntrl',
    'graph'             => '_typeGraph',
    'lower'             => '_typeLower',
    'upper'             => '_typeUpper',
    'print'             => '_typePrint',
    'punct'             => '_typePunct',
    'space'             => '_typeSpace',
    'notxdigit'         => '_typeNotXdigit',
    'withoutdigit'      => '_typeWithoutDigit',
    'withoutcntrl'      => '_typeWithoutCntrl',
    'withoutgraph'      => '_typeWithoutGraph',
    'withoutlower'      => '_typeWithoutLower',
    'withoutupper'      => '_typeWithoutUpper',
    'withoutprint'      => '_typeWithoutPrint',
    'withoutpunct'      => '_typeWithoutPunct',
    'withoutspace'      => '_typeWithoutSpace',
    'withoutalphabet'              => '_typeWithoutAlphabet',
    'withoutalphabetornumber'      => '_typeWithoutAlphabetOrNumber',
    'number'           => '_typeNumber',
    'naturalnumber'    => '_typeNaturalNumber',
    'integer'          => '_typeInteger',
    'numbermax'        => '_typeNumberMax',
    'numbermin'        => '_typeNumberMin',
    'alphabet'         => '_typeAlphabet',
    'alphabetornumber' => '_typeAlphabetOrNumber',
    'rome'             => '_typeRome',
    'maxlen'           => '_typeMaxLen',
    'minlen'           => '_typeMinLen',
    'maxwidth'         => '_typeMaxWidth',
    'minwidth'         => '_typeMinWidth',
    'blank'            => '_typeBlank',
    'noblank'          => '_typeNoBlank',
    'nosubmit'         => '_typeNoSubmit',
    'encoding'         => '_typeEncoding',
    'notags'           => '_typeNoTags',
    'depend'           => '_typeDepend',
    'mailformat'       => '_typeMailFormat',
    'mailsimple'       => '_typeMailFormatSymple',
    'mail'             => '_typeMail',
    'hiragana'         => '_typeHiragana',
    'katakana'         => '_typeKatakana',
    'hfurigana'        => '_typeHFurigana',
    'kfurigana'        => '_typeKFurigana',
    'urlformat'        => '_typeUrlFormat',
    'url'              => '_typeUrl',
    'postcodeformat'   => '_typePostcodeFormat',
    'telephone'        => '_typeTelephoneFormat',
    'whitelist'        => '_typeWhiteList',
    'date'             => '_typeDate',
    'time'             => '_typeTime',
    'array'            => '_typeArray',
    'notarray'         => '_typeNotArray',
    'arraykeyexists'   => '_typeArrayKeyExists',
    'arraynumber'      => '_typeArrayNumber',
    'arraynumbermax'   => '_typeArrayNumberMax',
    'arraynumbermin'   => '_typeArrayNumberMin',
    'arraycountmax'    => '_typeArrayCountMax',
    'arraycountmin'    => '_typeArrayCountMin',
    'arrayunique'      => '_typeArrayUnique',
    'maxbr'            => '_typeMaxBr',
    'minbr'            => '_typeMinBr',
    'dirpath'          => '_typeDirPath',
    'file'             => '_typeFile',
    'ereg'             => '_typeEreg',
    'preg'             => '_typePreg',
);

// テーブルschema設定初期化
$table_schema_setting = array(
    // Pkeyは標準でつける
    'id' => array(
        'type'           => 'int(11)',
        'primary'        => 'PRIMARY',
        'auto_increment' => true,
        'not_null'       => true,
        'default'        => NULL,
    )
);

// Insert時のSetter
$setter_text = '';

// バリデーター
$validate_text = '';

// ユニークキーチェック(insert用)
$validate_unique_check_text = '';
// ユニークキーチェック(update用)
$validate_unique_check_update_text = '';
// Ormap用ユニークキーチェッククエリ
$unique_check_query = '';
// Ormap用ユニークキーチェックメソッド
$unique_check_method = '';

// 追加変数
$add_input_data_text = '';

// フォームテンプレート
$form_text = '';
$form_update_text = '';
// Confirmテンプレート
$confirm_text = '';
// 表示用テンプレート
$show_text = '';

// アトリビュート用
$attribute_text = '';

// 残りの引数を使って、コードをジェネレートする
while (isset($argv[$i]) ? $scaffold_data = $argv[$i] : false) {
    // scafold 引数を展開
    $scaffold_data   = explode(',', $scaffold_data);
    $scaffold_data_f = array();

    // 第一引数
    $scaffold_form = array_shift($scaffold_data);
    $scaffold_form = explode(':', $scaffold_form);
    $scaffold_name = $scaffold_form[0];

    // 残りをパースする
    foreach ($scaffold_data as $val) {
        $val_p = explode(':', $val);
        if (count($val_p) === 1) {
            $scaffold_data_f[$val] = true;
        } elseif (count($val_p) === 2) {
            $val_key = array_shift($val_p);
            $scaffold_data_f[$val_key] = $val_p[0];
        } else {
            $val_key = array_shift($val_p);
            $scaffold_data_f[$val_key] = $val_p;
        }
    }

    // データ型の調整
    $scaffold_type      = isset($scaffold_form[1]) ? $scaffold_form[1] : 'string';
    $scaffold_form_name = isset($scaffold_form[2]) ? $scaffold_form[2] : $scaffold_type;

    // スキーマ(column情報)の初期化
    $table_schema_setting[$scaffold_name] = array();

    // バリデーション初期設定ファイルのファイルパス生成
    $file_poth = dirname(__FILE__).'/scaffold/default/validate_start';
    // notnullならかえる
    if (isset($scaffold_data_f['notnull'])) {
        $file_poth .= '_notnull';
    }
    $file_poth .= '.php';
    if (!is_file($file_poth)) {
        throw new exception($file_poth.'is not exists.');
    }

    // バリデータの初期化
    $validate = array();
    foreach ($scaffold_data_f as $key => $item) {
        if (isset($validators[$key])) {
            $validate[$key] = $item;
        }
    }

    $use_validator = true;
    if ($scaffold_type === 'flag') {
        // flagはバリデータを使用せず、直接追加する
        $use_validator = false;
        $add_input_data_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name, $scaffold_form_name),
            file_get_contents(dirname(__FILE__).'/scaffold/default/___flag_add_input.php')
        );
    } elseif ($scaffold_type === 'zip_code') {
        // zip_codeはバリデータをニコ登録する
        $validate_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name.'_f', $scaffold_form_name),
            file_get_contents($file_poth)
        );
        $validate_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name.'_a', $scaffold_form_name),
            file_get_contents($file_poth)
        );
        $add_input_data_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name, $scaffold_form_name),
            file_get_contents(dirname(__FILE__).'/scaffold/default/___zip_code_add_input.php')
        );
    } elseif ($scaffold_type === 'date_time' || $scaffold_type === 'datetime') {
        // date_timeはバリデータをニコ登録する
        $validate_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name.'_date', $scaffold_form_name),
            file_get_contents($file_poth)
        );
        $validate_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name.'_time', $scaffold_form_name),
            file_get_contents($file_poth)
        );
        $add_input_data_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name, $scaffold_form_name),
            file_get_contents(dirname(__FILE__).'/scaffold/default/___date_time_add_input.php')
        );
    } else {
        // バリデーション初期設定(validate()->autoPrepare(...))
        $validate_text .= str_replace(
            array(
                '_____scaffold_name_____',
                '_____scaffold_form_name_____',
            ),
            array($scaffold_name, $scaffold_form_name),
            file_get_contents($file_poth)
        );
    }

    // フォームタイプの初期化
    $scaffold_form_type = 'text';

    // $scaffold_typeに合わせて、固定のバリデーションとスキーマを定義する
        switch ($scaffold_type) {
        case 'date':
            $table_schema_setting[$scaffold_name]['type'] = 'date';
            $validate['date'] = true;
            break;
        case 'time':
            $table_schema_setting[$scaffold_name]['time'] = 'time';
            $validate['date'] = true;
            break;
        case 'date_time':
        case 'datetime':
            $scaffold_form_type = 'datetime';
            $table_schema_setting[$scaffold_name]['type'] = 'datetime';
            // 手動で追加する
            $use_validator = false;
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_date', $scaffold_form_name, 'date', true),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_time', $scaffold_form_name, 'time', 4),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );

            break;
        case 'integer':
        case 'int':
            $scaffold_form_type = 'number';
            $table_schema_setting[$scaffold_name]['type'] = 'int(11)';
            $validate['integer'] = true;
            if (isset($scaffold_data_f['unsigned']) && $scaffold_data_f['unsigned']) {
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 2147483647;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : -2147483648;
            } else {
                $table_schema_setting[$scaffold_name]['unsigned'] = true;
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 4294967295;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            }
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            break;
        case 'naturalnumber':
            $scaffold_form_type = 'number';
            $validate['naturalnumber'] = true;
            $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 4294967295;
            $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            $table_schema_setting[$scaffold_name]['type'] = 'int(11)';
            $table_schema_setting[$scaffold_name]['unsigned'] = true;
            break;
        case 'bigint':
            $scaffold_form_type = 'number';
            $validate['integer'] = true;
            $table_schema_setting[$scaffold_name]['type'] = 'bigint(20)';
            if (isset($scaffold_data_f['unsigned']) && $scaffold_data_f['unsigned']) {
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 9223372036854775807;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : -9223372036854775808;
            } else {
                $table_schema_setting[$scaffold_name]['unsigned'] = true;
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : '18446744073709551615';
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            }
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            break;
        case 'tinyint':
            $scaffold_form_type = 'number';
            $table_schema_setting[$scaffold_name]['type'] = 'tinyint(4)';
            $validate['integer'] = true;
            if (isset($scaffold_data_f['unsigned']) && $scaffold_data_f['unsigned']) {
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 127;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : -128;
            } else {
                $table_schema_setting[$scaffold_name]['unsigned'] = true;
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 255;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            }
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            break;
        case 'smallint':
            $scaffold_form_type = 'number';
            $table_schema_setting[$scaffold_name]['type'] = 'smallint(6)';
            $validate['integer'] = true;
            if (isset($scaffold_data_f['unsigned']) && $scaffold_data_f['unsigned']) {
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 32767;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : -32768;
            } else {
                $table_schema_setting[$scaffold_name]['unsigned'] = true;
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 65535;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            }
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            break;
        case 'mediumint':
            $scaffold_form_type = 'number';
            $table_schema_setting[$scaffold_name]['type'] = 'mediumint(7)';
            $validate['integer'] = true;
            if (isset($scaffold_data_f['unsigned']) && $scaffold_data_f['unsigned']) {
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 8388607;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : -8388608;
            } else {
                $table_schema_setting[$scaffold_name]['unsigned'] = true;
                $validate['numbermax'] = isset($validate['numbermax']) ? $validate['numbermax'] : 16777215;
                $validate['numbermin'] = isset($validate['numbermin']) ? $validate['numbermin'] : 0;
            }
            $validate['maxwidth'] = isset($validate['maxwidth']) ? $validate['maxwidth'] : max(strlen($validate['numbermax']), strlen($validate['numbermin']));
            break;
        case 'string':
        case 'varchar':
            $scaffold_form_type = 'text';
            if (!isset($validate['maxwidth']) && !isset($validate['maxlen'])) {
                $validate['maxlen'] = 255;
            }
            if (isset($validate['maxwidth'])) {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxwidth'].')';
            } else {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxlen'].')';
            }

            break;
        case 'email':
            $scaffold_form_type = 'email';
            if (!isset($validate['maxwidth']) && !isset($validate['maxlen'])) {
                $validate['maxlen'] = 255;
            }
            if (isset($validate['maxwidth'])) {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxwidth'].')';
            } else {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxlen'].')';
            }

            $validate['mailsimple'] = true;
            break;
        case 'password':
            $scaffold_form_type = 'password';
            if (!isset($validate['maxwidth']) && !isset($validate['maxlen'])) {
                $validate['maxlen'] = 255;
            }
            if (isset($validate['maxwidth'])) {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxwidth'].')';
            } else {
                $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxlen'].')';
            }
            $validate['rome'] = true;
            break;
        case 'flag':
            $scaffold_form_type = 'flag';
            $table_schema_setting[$scaffold_name]['type'] = 'tinyint(4)';
            break;
        case 'radio':
            $scaffold_form_type = 'radio';
            if (!isset($validate['maxwidth'])) {
                $validate['maxwidth'] = 10;
            }
            if (!isset($validate['whitelist'])) {
                $validate['whitelist'] = array();
            }
            $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxwidth'].')';
            break;
        case 'select':
            $scaffold_form_type = 'select';
            if (!isset($validate['maxwidth'])) {
                $validate['maxwidth'] = 10;
            }
            if (!isset($validate['whitelist'])) {
                $validate['whitelist'] = array();
            }
            $table_schema_setting[$scaffold_name]['type'] = 'varchar('.$validate['maxwidth'].')';
            break;
        case 'textarea':
        case 'text':
            $scaffold_form_type = 'textarea';
            if (!isset($validate['maxwidth'])) {
                $validate['maxwidth'] = 1000;
            }
            $table_schema_setting[$scaffold_name]['type'] = 'text';
            break;
        case 'zip_code':
            $scaffold_form_type = 'zip_code';
            // 手動で追加する
            $use_validator = false;
            $table_schema_setting[$scaffold_name]['type'] = 'varchar(10)';
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_f', $scaffold_form_name, 'maxwidth', '3'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_f', $scaffold_form_name, 'minwidth', '3'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_a', $scaffold_form_name, 'maxwidth', '4'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_a', $scaffold_form_name, 'minwidth', '4'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_f', $scaffold_form_name, 'number', 'true'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name.'_a', $scaffold_form_name, 'number', 'true'),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );

            break;
        default:
            throw new exception($scaffold_type . 'は存在しないデータ型です。');
            break;
    }

    // Not Null対応
    $table_schema_setting[$scaffold_name]['not_null'] = false;
    if (isset($scaffold_data_f['notnull'])) {
        $table_schema_setting[$scaffold_name]['not_null'] = true;
    }

    // ユニーク対応
    if (isset($scaffold_data_f['unique'])) {
        $table_schema_setting[$scaffold_name]['unique'] = array('uq_'.$scaffold_name);
    }

    // デフォルト値対応
    $table_schema_setting[$scaffold_name]['default'] = isset($scaffold_data_f['default']) ? (int)$scaffold_data_f['default'] : NULL;

    // 定義されたバリデータから、バリデータ用のコードをジェネレートする
    foreach ($validate as $validator => &$val) {
        $val = var_export($val, true);
        if ($use_validator) {
            $validate_text .= str_replace(
                array(
                    '_____scaffold_name_____',
                    '_____scaffold_form_name_____',
                    '_____scaffold_validate_type_____',
                    '_____scaffold_validate_value_____',
                ),
                array($scaffold_name, $scaffold_form_name, $validator, $val),
                file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_chain.php')
            );
        }
    }
    unset($val);

    $inner_replace_from =         array(
        '_____scaffold_name_____',
        '_____scaffold_pascal_case_name_____',
        '_____module_pascal_case_name_____',
        '_____model_pascal_case_name_____',
        '_____scaffold_form_name_____',
        '_____scaffold_form_type_____',
        '_____scaffold_form_default_____',
    );
    $inner_replace_to =         array(
        $scaffold_name,
        pascalize($scaffold_name),
        pascalize($module_name),
        pascalize($model_name),
        $scaffold_form_name,
        $scaffold_form_type,
        $table_schema_setting[$scaffold_name]['default'],
    );
    switch ($scaffold_type) {
        case 'select':
        case 'radio':
        case 'checkbox':
        case 'flag':
            $attribute_text.= str_replace(
                $inner_replace_from,
                $inner_replace_to,
                file_get_contents(dirname(__FILE__).'/scaffold/default/___attribute_text.php')
            );
            break;
        default:
            break;
    }

    // フォーム定義
    $form_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
        file_get_contents(dirname(__FILE__).'/scaffold/default/___form_column.tpl')
    );
    // フォーム定義
    $form_update_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
        file_get_contents(dirname(__FILE__).'/scaffold/default/___form_column_update.tpl')
    );

    // conform定義
    $confirm_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
        file_get_contents(dirname(__FILE__).'/scaffold/default/___confirm_column.tpl')
    );

    // 表示定義
    $show_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
        file_get_contents(dirname(__FILE__).'/scaffold/default/___show_column.tpl')
    );

    // セッター定義
    $setter_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
        file_get_contents(dirname(__FILE__).'/scaffold/default/___setter.php')
    );

    // uniqueCheck処理の追加
    if (isset($scaffold_data_f['unique'])) {
        $validate_unique_check_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
            file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_unique_check.php')
        );
        $validate_unique_check_update_text .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
            file_get_contents(dirname(__FILE__).'/scaffold/default/___validate_unique_check_update.php')
        );
        $unique_check_query .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
            file_get_contents(dirname(__FILE__).'/scaffold/default/___unique_check_query.php')
        );
        $unique_check_method .= str_replace(
            $inner_replace_from,
            $inner_replace_to,
            file_get_contents(dirname(__FILE__).'/scaffold/default/___unique_check_method.php')
        );
    }
    $i++;
}

// テーブルスキーマ定義の追加
$table_schema_setting['insert_date'] = array(
    'type' => 'datetime',
    'insert_date' => true,
    'not_null' => true,
    'default' => NULL,
);
$table_schema_setting['update_date'] = array(
    'type' => 'datetime',
    'update_date' => true,
    'not_null' => true,
    'default' => NULL,
);
$table_schema_setting['owner'] = array(
    'type' => 'varchar(128)',
    'not_null' => true,
    'default' => 'system_insert',
);
$table_schema_setting['time_stamp'] = array(
    'type' => 'timestamp',
    'not_null' => true,
    'default' => NULL,
);


// テーブル定義のYamlを生成する
$yaml = Spyc::YAMLDump(array('SCHEMA' => array(snake_case($model_name) => array('schema' => $table_schema_setting))), 2, 60);
writeAction($yaml, $project_name.'_'.snake_case($model_name) , $project_dir.'config'.DIRECTORY_SEPARATOR, '.yml');


if (!is_dir($project_dir.'db'.DIRECTORY_SEPARATOR.'migrate')) {
    $cmd = 'mkdir -p '.$project_dir.'db'.DIRECTORY_SEPARATOR.'migrate';
    echo `$cmd`;
}
$date_time = date('YmdHis');
$yaml = Spyc::YAMLDump(array('force' => true, 'SCHEMA' => array(snake_case($model_name) => array('schema' => $table_schema_setting))), 2, 60);

writeAction($yaml, $project_name.'_'.$date_time.'_create_table_'.snake_case($model_name) , $project_dir.'db'.DIRECTORY_SEPARATOR.'migrate'.DIRECTORY_SEPARATOR, '.yml');

$contents = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'scaffold'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'migration.php');


$arr = array(
    '___class_name___' => $project_name.'_create_table_'.snake_case($model_name),
    '___app_name___' => $project_name,
    '___project_dir___' => $project_dir,
    '___file_name___' => $project_name.'_'.$date_time.'_create_table_'.snake_case($model_name)
);

$contents = strtr($contents, $arr);
$file_path = $project_dir.'db'.DIRECTORY_SEPARATOR.'migrate'.DIRECTORY_SEPARATOR.$project_name.'_'.$date_time.'_create_table_'.snake_case($model_name).'.php';
file_put_contents($file_path, $contents);


// 各テンプレートの置き換え変数を定義する
$replace_from = array(
    '_____action_name_____',
    '/*%%validate_text%%*/',
    '/*%%setter_text%%*/',
    '/*%%form_text%%*/',
    '/*%%form_update_text%%*/',
    '/*%%confirm_text%%*/',
    '/*%%show_text%%*/',
    '/*%%validate_unique_check_text%%*/',
    '/*%%validate_unique_check_update_text%%*/',
    '/*%%unique_check_query%%*/',
    '/*%%unique_check_method%%*/',
    '/*%%add_input_data_text%%*/',
    '/*%%attribute_text%%*/',
    '/*%%model_name_space%%*/',
    '/*%%model_base_name_space%%*/',
    '/*%%model_name_space_use%%*/',
    '/*%%model_base_name_space_use%%*/',
    '/*%%dao_use%%*/',

    '_____module_name_____',
    '_____module_pascal_case_name_____',
    '_____model_pascal_case_name_____',
);
$replace_to = array(
    'new',
    $validate_text,
    $setter_text,
    $form_text,
    $form_update_text,
    $confirm_text,
    $show_text,
    $validate_unique_check_text,
    $validate_unique_check_update_text,
    $unique_check_query,
    $unique_check_method,
    $add_input_data_text,
    $attribute_text,
    $model_name_space ? $model_name_space.';' : '',
    $model_base_name_space ? "\\".$model_base_name_space."\\" : '',
    $model_name_space_use,
    $model_base_name_space_use,
    $dao_use,

    $module_name,
    pascalize($module_name),
    pascalize($model_name),
);

// 新規作成フォームテンプレート
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/new.tpl')
);
writeAction($contents, 'new', $template_dir, '.tpl');


// 編集フォームテンプレート
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/edit.tpl')
);
writeAction($contents, 'edit', $template_dir, '.tpl');


// 新規作成確認画面テンプレート
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/new_confirm.tpl')
);
writeAction($contents, 'new_confirm', $template_dir, '.tpl');

// 編集確認画面テンプレート
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/edit_confirm.tpl')
);
writeAction($contents, 'edit_confirm', $template_dir, '.tpl');


// フォームヘルパー(includeファイル)
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/_form_helper.tpl')
);
writeAction($contents, '_form_helper', $template_dir, '.tpl');

// 入力確認ヘルパー(includeファイル)
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/_confirm_helper.tpl')
);
writeAction($contents, '_confirm_helper', $template_dir, '.tpl');

// ヘッダ(includeファイル)
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/_header.tpl')
);
writeAction($contents, '_header', $template_dir, '.tpl');

// フッタ(includeファイル)
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/_footer.tpl')
);
writeAction($contents, '_footer', $template_dir, '.tpl');

// エラー(includeファイル)
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/_error.tpl')
);
writeAction($contents, '_error', $template_dir, '.tpl');



// 共通エラー画面
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/common_error.tpl')
);
writeAction($contents, 'common_error', $template_dir, '.tpl');

// 詳細表示
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/show.tpl')
);
writeAction($contents, 'show', $template_dir, '.tpl');

// 一覧表示
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/index.tpl')
);
writeAction($contents, 'index', $template_dir, '.tpl');

// 削除
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/destroy.tpl')
);
writeAction($contents, 'destroy', $template_dir, '.tpl');


// アクション定義

// 新規作成フォーム
$replace_to[0] = 'new';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/newAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));


// 新規作成
$replace_to[0] = 'create';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/createAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// 詳細表示
$replace_to[0] = 'show';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/showAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// 編集フォーム
$replace_to[0] = 'edit';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/editAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// 更新
$replace_to[0] = 'update';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/updateAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// 削除
$replace_to[0] = 'destroy';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/destroyAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// 一覧
$replace_to[0] = 'index';
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/indexAction.php')
);
writeAction($contents, $replace_to[0].'Action', $action_dir, '.class.php', !isOption('no_override:action'));

// Ormap
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/OrmapPeer.php')
);
writeAction($contents, pascalize($model_name).'Peer', $schema_yaml['DIRECTORY']['model_dir'], '.class.php', !isOption('no_override:model'));

// データオブジェクト
$contents = str_replace(
    $replace_from,
    $replace_to,
    file_get_contents(dirname(__FILE__).'/scaffold/default/Ormap.php')
);
writeAction($contents, pascalize($model_name), $schema_yaml['DIRECTORY']['model_dir'], '.class.php', !isOption('no_override:model'));

// 関数定義

/**
 * +-- テキストをパスカルケースにする
 *
 * @param       string $string
 * @return      string
 */
function pascalize($string)
{
    $string = preg_replace('/([A-Z])/', '_$1', $string);
    $string = strtolower($string);
    $string = str_replace('_', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    return $string;
}
/* ----------------------------------------- */

/**
 * +-- テキストをスネークケースにする
 *
 * @param       string $string
 * @return      string
 */
function snake_case($string)
{
    $string[0] = strtolower($string[0]);
    $string = preg_replace('/([A-Z])/', '_$1', $string);
    $string = strtolower($string);
    return ltrim($string, '_');
}
/* ----------------------------------------- */

/**
 * +-- テキストをキャメルケースにする
 *
 * @param       string $string
 * @return      string
 */
function camelize($string)
{
    $string = pascalize($string);
    $string[0] = strtolower($string[0]);
    return $string;
}
/* ----------------------------------------- */

/**
 * +-- ファイルの書き込み
 *
 * @param       string $contents
 * @param       string $file_name
 * @param       string $dir
 * @param       string $ext OPTIONAL:'.class.php'
 * @param       boolean $over_ride OPTIONAL:true
 * @return      void
 */
function writeAction($contents, $file_name, $dir, $ext = '.class.php', $over_ride = true)
{
    // 強制上書きじゃなければ、バックアップを保存する
    if (!$over_ride && is_file($dir.$file_name.$ext)) {
        $rename = $dir.$file_name.$ext.'.'.time();
        rename($dir.$file_name.$ext, $rename);
        sysMessage('rename:', $dir.$file_name.$ext, $rename);
    }
    file_put_contents($dir.$file_name.$ext, $contents);
}
/* ----------------------------------------- */


function sysMessage()
{
    $message = join('', func_get_args());
    echo $message,"\n";
}