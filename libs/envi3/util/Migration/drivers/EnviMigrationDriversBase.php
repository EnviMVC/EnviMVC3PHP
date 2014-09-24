<?php
/**
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
/**
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
abstract class EnviMigrationDriversBase
{
    protected $migration;

    public function __construct($migration)
    {
        $this->migration = $migration;
    }

    public function Migration()
    {
        return $this->migration;
    }
    public function DBI()
    {
        return $this->Migration()->DBI();
    }

    public function query($sql, array $arr = array())
    {
        if ($this->Migration()->isDryRun()) {
            echo "[DRY]".$sql."\n";
            return;
        }
        $this->DBI()->query($sql, $arr);

    }
    abstract public function addColumn($table_name, $column_name, $type, array $options = array());
    abstract public function addIndex($table_name, $column_name, array $options = array());
    abstract public function addTimestamps($table_name);
    abstract public function changeColumn($table_name, $column_name, $type, array $options = array());
    abstract public function changeColumnDefault($table_name, $column_name, $default_val);
    abstract public function createTable($table_name, array $options = array());
    abstract public function dropTable($table_name, array $options = array());
    abstract public function removeColumn($table_name, $column_names);
    abstract public function removeIndex($table_name, array $options = array());
    abstract public function removeTimestamps($table_name);
    abstract public function renameColumn($table_name, $column_name, $new_column_name);
    abstract public function renameIndex($table_name, $old_name, $new_name);
    abstract public function renameTable($table_name, $new_name);
    // abstract public function changeTable($table_name, array $options = array());
}
