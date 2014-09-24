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

function pascalize($string)
{
    $string = strtolower($string);
    $string = str_replace('_', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    return $string;
}


$sql = '';
foreach ($config['SCHEMA'] as $table_name => &$schema) {
    $index = array();
    $unique = array();
    $primary = array();
    $comma = '';
    $sql .= "DROP TABLE IF EXISTS `{$table_name}`;\nCREATE TABLE `{$table_name}` (\n";
    foreach ($schema['schema'] as $column => $val) {
        $sql .= $comma;
        $sql .= "`{$column}` {$val['type']} ";
        if (isset($val['not_null'])) {
            $sql .= 'NOT NULL ';
        }
        if (isset($val['auto_increment'])) {
            $sql .= ' AUTO_INCREMENT';
        } elseif (isset($val['default'])) {
            $sql .= ' DEFAULT ';
            $sql .= (strtolower($val['default']) === 'null' && !isset($val['not_null'])) ? 'NULL' : '"'.$val['default'].'"';
        }
        if (isset($val['index'])) {
            foreach ($val['index'] as $item) {
                $index[$item][] = $column;
            }
        }
        if (isset($val['unique'])) {
            foreach ($val['unique'] as $item) {
                $unique[$item][] = $column;
            }
        }
        if (isset($val['primary'])) {
            $primary[] = $column;
        }
        $comma = ",\n";
    }
    if (count($primary)) {
        $sql .= $comma.'PRIMARY KEY ('.join(',', $primary).')';
    }
    if (count($unique)) {
        foreach ($unique as $index_name => $idx_column) {
            $sql .= $comma.'UNIQUE KEY `'.$index_name.'` ('.join(',', $idx_column).')';
        }
    }
    if (count($index)) {
        foreach ($index as $index_name => $idx_column) {
            $sql .= $comma.'KEY `'.$index_name.'` ('.join(',', $idx_column).')';
        }
    }
    $sql .= ") ENGINE=InnoDB;\n";
}
unset($schema);
echo $sql;