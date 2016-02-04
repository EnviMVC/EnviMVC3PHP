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

// ネームスペース
$model_name_space       = '';
$model_base_name_space  = '';
$model_name_space_name       = '';
$model_base_name_space_name  = '';

$model_name_space_use   = '';
$base_model_name_space_use   = '';
$model_base_name_space_use   = '';
if (PHP_MINOR_VERSION >= 3) {
    $model_name_space            = isset($config['SETTING']['model_name_space']) ? $config['SETTING']['model_name_space']: '';
    $model_base_name_space       = isset($config['SETTING']['model_base_name_space']) ? $config['SETTING']['model_base_name_space']: '';
    $config['SETTING']['model_name_space'] = $model_name_space;
    $config['SETTING']['model_base_name_space'] = $model_base_name_space;

}
if (strlen($model_name_space)) {
    $model_name_space_name = "\\".$model_name_space."\\";
    $model_name_space = 'namespace ' . $model_name_space .';';
    $model_name_space_use = "\nuse \\EnviOrMapBase;\nuse \\EnviOrMapPeerBase;\nuse \\EnviDBInstance;\nuse \\EnviDB;\nuse \\EnviException;\nuse \\EnviDBIBase;\nuse \\EnviDataCache;\n";
}
if (strlen($model_base_name_space)) {
    $model_base_name_space_name = "\\".$model_base_name_space."\\";
    $model_base_name_space     = 'namespace ' . $model_base_name_space .';';
    $model_base_name_space_use = "\nuse \\EnviOrMapBase;\nuse \\EnviOrMapPeerBase;\nuse \\EnviDBInstance;\nuse \\EnviDB;\nuse \\EnviException;\nuse \\EnviDBIBase;\nuse \\EnviDataCache;\n";
}



foreach ($config['SCHEMA'] as $table_name => &$schema) {
    $enable_magic = '';
    $default_array = array();
    $instance_name = isset($schema['instance_name']) ? $schema['instance_name'] : $config['SETTING']['default_instance_name'];
    $auto_schema   = isset($schema['auto_schema']) ? $schema['auto_schema'] : $config['SETTING']['default_auto_schema'];
    $use_cache     = isset($schema['use_cache']) ? $schema['use_cache'] == true : false;
    $use_apc_cache = $use_cache ? $schema['use_cache'] == 'apc' : false;
    $foreign_key = isset($schema['foreign_key']) ? $schema['foreign_key']: array();


    $php_type = 'mysql';
    if (!isset($schema['php_type'])) {
        $schema['php_type'] = 'mysql';
    }

    include dirname(__FILE__).DIRECTORY_SEPARATOR.'driver'.DIRECTORY_SEPARATOR.'autoScheme'.DIRECTORY_SEPARATOR.$schema['php_type'].'.php';


    $cache_hydrate = '';
    if ($use_apc_cache) {
        $cache_hydrate = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'UseApcCache.php.snp');
    } elseif ($use_cache) {
        $cache_hydrate = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'UseCache.php.snp');
    }


    $magic_method = isset($schema['magic_method']) ? $schema['magic_method'] : $config['SETTING']['default_magic_method'];
    if (!$magic_method) {
        $enable_magic = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'magicEnable.class.php.snp');
    }
    $getter_setter = '';
    // var_dump($schema);
    $class_name = isset($schema['class_name']) ? $schema['class_name'] : pascalize($table_name);

    $insert_date = isset($schema['insert_date']) ? $schema['insert_date'] : '';
    $update_date = isset($schema['update_date']) ? $schema['update_date'] : '';
    $time_stamp = isset($schema['time_stamp']) ? $schema['time_stamp'] : '';
    $auto_increment = isset($schema['auto_increment']) ? $schema['auto_increment'] : '';


    $sql = "SELECT * FROM __TABLE__ ";
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
        if (isset($status['type']) && $status['type'] === 'timestamp') {
            // クエリ作成に関係ないけど、タイムスタンプ型のチェック
            $time_stamp = $column;
        }
        if (isset($status['auto_increment']) && $status['auto_increment'] == true) {
            // クエリ作成に関係ないけど、auto_incrementのチェック
            $auto_increment = $column;
        }
        $getter_setter .= str_replace(
            array('%%method%%', '%%column%%'),
            array(pascalize($column), $column),
            $getter_setter_text
        );
        $default_array[$column] = isset($status['default']) ? $status['default'] : NULL;
    }

    $default_array = var_export($default_array, true);

    $fk_getter = '';
    $fk_cache_item = '';

    $fk_getter_txt     = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fkey_func.php.snp');
    $fk_cache_item_txt = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'fkey_cache_param.php.snp');
    foreach ($foreign_key as $fk_name => $foreign_key_items) {
        $fkeys = $foreign_key_items['columns'];
        $fk_class_name = isset($foreign_key_items['class_name']) ? $foreign_key_items['class_name'] : $fk_name;
        foreach ($fkeys as $k => $fkey) {
            $fkeys[$k]= '$this->_from_hydrate['."'".$fkey."'".']';
        }
        $get_pks = join(', ', $fkeys);
        $fk_cache_item .= str_replace(
            array('%%get_pks%%', '%%fk_class_name%%', '%%fk_name%%'),
            array($get_pks, $fk_class_name, $fk_name),
            $fk_cache_item_txt
        );
        $fk_getter .= str_replace(
            array('%%get_pks%%', '%%fk_class_name%%', '%%fk_name%%'),
            array($get_pks, $fk_class_name, $fk_name),
            $fk_getter_txt
        );
    }

    $cache_load = '';
    if ($use_cache) {
        $cache_load = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache_load.php.snp');
        $cache_load = str_replace(
            array('%%model_name_space_name%%', '%%model_base_name_space_name%%', '%%model_name_space%%', '%%model_base_name_space%%', '%%model_name_space_use%%', '%%model_base_name_space_use%%', '%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%',
                '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%', '%%cache_hydrate%%', '%%fk_getter%%', '%%fk_cache_item%%', '%%use_apc%%'),
            array($model_name_space_name, $model_base_name_space_name, $model_name_space, $model_base_name_space, $model_name_space_use, $model_base_name_space_use, $class_name, $instance_name, $sql, join(".':'.", $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array, $cache_hydrate, $fk_getter, $fk_cache_item, $use_apc_cache ? 'true' : 'false'),
            $cache_load
        );
    }

    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'OrmapPeer.class.php.snp');
    $text = str_replace(
        array('%%model_name_space_name%%', '%%model_base_name_space_name%%', '%%model_name_space%%', '%%model_base_name_space%%', '%%model_name_space_use%%', '%%model_base_name_space_use%%', '%%insert_date%%', '%%update_date%%', '%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($model_name_space_name, $model_base_name_space_name, $model_name_space, substr($model_base_name_space, 0, -1)."\\", $model_name_space_use, $model_base_name_space_use, $insert_date, $update_date, $class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );
    if (!is_file($model_dir.$class_name.'Peer.class.php')) {
        echo $model_dir.$class_name.'Peer.class.php'."\n";
        file_put_contents($model_dir.$class_name.'Peer.class.php', $text);
    }


    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Ormap.class.php.snp');
    $text = str_replace(
        array('%%model_name_space_name%%', '%%model_base_name_space_name%%', '%%model_name_space%%', '%%model_base_name_space%%', '%%model_name_space_use%%', '%%model_base_name_space_use%%', '%%insert_date%%', '%%update_date%%', '%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%', '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%'),
        array($model_name_space_name, $model_base_name_space_name, $model_name_space, substr($model_base_name_space, 0, -1)."\\", $model_name_space_use, $model_base_name_space_use, $insert_date, $update_date, $class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array),
        $text
    );
    if (!is_file($model_dir.$class_name.'.class.php')) {
        echo $model_dir.$class_name.'.class.php'."\n";
        file_put_contents($model_dir.$class_name.'.class.php', $text);
    }



    $base_model_name_space_use = $model_name_space_use;
    if (strlen($base_model_name_space_use) > 0) {
        $base_model_name_space_use .= "\nuse \\{$config['SETTING']['model_name_space']}\\{$class_name};\n\nuse \\{$config['SETTING']['model_name_space']}\\{$class_name}Peer;\n";
    }

    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'BasePeer.class.php.snp');
    $text = str_replace(
        array('%%model_name_space%%', '%%model_base_name_space%%', '%%model_name_space_use%%', '%%model_base_name_space_use%%', '%%insert_date%%', '%%update_date%%', '%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%',
            '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%', '%%cache_load%%'),
        array($model_name_space, $model_base_name_space, $base_model_name_space_use, $model_base_name_space_use, $insert_date, $update_date, $class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name, $getter_setter, $enable_magic, $default_array, $cache_load),
        $text
    );
    echo $om_dir.'Base'.$class_name.'Peer.class.php'."\n";
    file_put_contents($om_dir.'Base'.$class_name.'Peer.class.php', $text);


    $text = file_get_contents($task_plugin_dir.$module.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Base.class.php.snp');
    $text = str_replace(
        array('%%model_name_space%%', '%%model_base_name_space%%', '%%model_name_space_use%%', '%%model_base_name_space_use%%', '%%insert_date%%', '%%update_date%%', '%%class_name%%', '%%instance_name%%', '%%sql%%', '%%args%%', '%%pkeys%%', '%%table_name%%',
            '%%getter_setter%%', '%%enable_magic%%', '%%default_array%%', '%%cache_hydrate%%', '%%fk_getter%%', '%%fk_cache_item%%', '%%cache_load%%', '%%time_stamp%%', '%%auto_increment%%'),
        array($model_name_space, $model_base_name_space, $base_model_name_space_use, $model_base_name_space_use, $insert_date, $update_date, $class_name, $instance_name, $sql, join(',', $func_args), join(',', $pkeys), $table_name,
            $getter_setter, $enable_magic, $default_array, $cache_hydrate, $fk_getter, $fk_cache_item, $cache_load, $time_stamp, $auto_increment),
        $text
    );

    echo $om_dir.'Base'.$class_name.'.class.php'."\n";
    file_put_contents($om_dir.'Base'.$class_name.'.class.php', $text);
}
unset($schema);
if ($config['SETTING']['reverse_yaml']) {
    // リバースする
    $file_name = $argv[2].'.reverse.yml';
    $yaml = Spyc::YAMLDump($config, 2, 60);
    file_put_contents($file_name, $yaml);
    echo $file_name."\n";
}
