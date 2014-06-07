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
if (!isset($argv[2])) {
    eecho('引数が足りません。');
    die;
}

include_once ENVI_BASE_DIR.'spyc.php';
include_once ENVI_BASE_DIR.'vendor'.DIRECTORY_SEPARATOR.'EnviDB.php';

ob_start();
include $argv[2];
$buff      = ob_get_contents();
ob_end_clean();

$config = spyc_load($buff);

if (!is_dir($config['DIRECTORY']['model_dir'])) {
    mkdir($config['DIRECTORY']['model_dir']);
    echo $config['DIRECTORY']['model_dir']."\n";
}
$model_dir = $config['DIRECTORY']['model_dir'].DIRECTORY_SEPARATOR;
$om_dir    = $model_dir.'om'.DIRECTORY_SEPARATOR;
if (!is_dir($om_dir)) {
    mkdir($om_dir);
    echo $om_dir."\n";
}


ob_start();
include $config['SETTING']['database_yaml'];
$buff      = ob_get_contents();
ob_end_clean();

$database_yaml = spyc_load($buff);

if (!isset($database_yaml[$config['SETTING']['env']])) {
    $database_yaml[$config['SETTING']['env']] = array();
}
$database_yaml = array_merge((array)$database_yaml['all'], (array)$database_yaml[$config['SETTING']['env']]);


$EnviDBInstance = NULL;
function pascalize($string)
{
    $string = strtolower($string);
    $string = str_replace('_', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    return $string;
}

$getter_setter_text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'getSet.class.php.snp');

foreach ($config['SCHEMA'] as $table_name => &$schema) {
    $enable_magic = '';
    $default_array = array();
    $instance_name = isset($schema['instance_name']) ? $schema['instance_name'] : $config['SETTING']['default_instance_name'];
    $auto_schema   = isset($schema['auto_schema']) ? $schema['auto_schema'] : $config['SETTING']['default_auto_schema'];

    // DBに接続して、自動的にスキーマ情報を取得する
    if ($auto_schema) {
        $schema['schema'] = array();
        if (!isset($EnviDBInstance)) {
            $EnviDBInstance = new EnviDBInstance($database_yaml);
        }
        $dbi = $EnviDBInstance->getInstance($instance_name);
        $schema_arr = $dbi->getAll('desc '.$table_name);
        $index_schema_arr = $dbi->getAll('SHOW INDEX FROM '.$table_name);

        foreach ($schema_arr as $k => $arr) {
            $schema['schema'][$arr['Field']]['type']    = $arr['Type'];
            switch ($arr['Default']) {
            case 'CURRENT_TIMESTAMP':
                $arr['Default'] = NULL;
            break;
            default:
                break;
            }
            $schema['schema'][$arr['Field']]['default'] = $arr['Default'];

            foreach ($index_schema_arr as $index_arr) {
                if ($index_arr['Column_name'] === $arr['Field']) {
                    if ($index_arr['Non_unique'] == 0 && strtolower($index_arr['Key_name']) === 'primary') {
                        $schema['schema'][$arr['Field']]['primary'] = $index_arr['Key_name'];
                    } elseif ($index_arr['Non_unique'] == 0) {
                        $schema['schema'][$arr['Field']]['unique'][] = $index_arr['Key_name'];
                    } else {
                        $schema['schema'][$arr['Field']]['index'][] = $index_arr['Key_name'];
                    }
                }
            }
            if (strtolower($arr['Null']) === 'no') {
                $schema['schema'][$arr['Field']]['not_null'] = true;
            }
            if (strtolower($arr['Extra']) === 'auto_increment') {
                $schema['schema'][$arr['Field']]['auto_increment'] = true;
            }

        }
    }


    $magic_method = isset($schema['magic_method']) ? $schema['magic_method'] : $config['SETTING']['default_magic_method'];
    if (!$magic_method) {
        $enable_magic = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'magicEnable.class.php.snp');
    }
    $getter_setter = '';
    // var_dump($schema);
    $class_name = isset($schema['class_name']) ? $schema['class_name'] : pascalize($table_name);
    $sql = "SELECT * FROM {$table_name} ";
    $func_args = array();
    $pkeys = array();
    $comma = '';
    $and = 'WHERE ';



    foreach ($schema['schema'] as $column => $status) {
        if (isset($status['primary'])) {
            $sql .= $and.$column.' = ? ';
            $and = 'AND ';
            $func_args[] = '$pkey'.count($func_args);
            $pkeys[] = "'{$column}'";
        }
        $getter_setter .= str_replace(
            array('%%method%%', '%%column%%'),
            array(pascalize($column), $column),
            $getter_setter_text
        );
        $default_array[$column] = isset($status['default']) ? $status['default'] : NULL;
    }

    $default_array = var_export($default_array, true);



    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'BasePeer.class.php.snp');
    $text = str_replace(
        array('%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );
    echo $om_dir.'Base'.$class_name.'Peer.class.php'."\n";
    file_put_contents($om_dir.'Base'.$class_name.'Peer.class.php', $text);


    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Base.class.php.snp');
    $text = str_replace(
        array('%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );

    echo $om_dir.'Base'.$class_name.'.class.php'."\n";
    file_put_contents($om_dir.'Base'.$class_name.'.class.php', $text);

    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'OrmapPeer.class.php.snp');
    $text = str_replace(
        array('%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );
    if (!is_file($model_dir.$class_name.'Peer.class.php')) {
        echo $model_dir.$class_name.'Peer.class.php'."\n";
        file_put_contents($model_dir.$class_name.'Peer.class.php', $text);
    }


    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Ormap.class.php.snp');
    $text = str_replace(
        array('%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );
    if (!is_file($model_dir.$class_name.'.class.php')) {
        echo $model_dir.$class_name.'.class.php'."\n";
        file_put_contents($model_dir.$class_name.'.class.php', $text);
    }
}
unset($schema);
if ($config['SETTING']['reverse_yaml']) {
    // リバースする
    $file_name = $argv[2].'.reverse.yml';
    $yaml = Spyc::YAMLDump($config, 2, 60);
    file_put_contents($file_name, $yaml);
    echo $file_name."\n";
}
