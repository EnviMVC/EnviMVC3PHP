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
abstract class EnviMigrationBase
{

    protected $databases_yml;
    public $env = 'dev';
    public $is_up = true;

    protected $instance_name = 'default_master';

    protected $driver;
    protected $dbi;


    abstract public function change();
    abstract public function up();
    abstract public function down();

    /**
     * +-- EnviDBIを返す
     *
     * @access      public
     * @return      EnviDBIBase
     */
    public function &DBI()
    {
        return $this->dbi;
    }
    /* ----------------------------------------- */

    /**
     * +-- ドライランかどうか
     *
     * @access      public
     * @return      boolean
     */
    public function isDryRun()
    {
        return isOption('--dry_run');
    }
    /* ----------------------------------------- */

    /**
     * +-- ドライバーを帰す
     *
     * @access      protected
     * @return      EnviMigrationDriversBase
     */
    protected function &Driver()
    {
        if ($this->driver instanceof EnviMigrationDriversBase) {
            return $this->driver;
        }
        ob_start();
        include $this->databases_yml;
        $buff      = ob_get_contents();
        ob_end_clean();
        $databases_yml = spyc_load($buff);

        if (!isset($databases_yml[$this->env])) {
            $databases_yml[$this->env] = array();
        }
        $databases_yml = array_merge((array)$databases_yml['all'], (array)$databases_yml[$this->env]);
        $dsn = $databases_yml[$this->instance_name]['params']['dsn'];
        // コネクションの取得
        $this->dbi = EnviDB::getConnection($databases_yml[$this->instance_name]['params'], $this->instance_name);
        parse_str($dsn, $conf);
        switch ($conf['phptype']) {
        case 'mysql':
            include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'drivers'.DIRECTORY_SEPARATOR.'EnviMigrationDriversMysql.php';
            $this->driver = new EnviMigrationDriversMysql($this);
            break;
        }
        return $this->driver;
    }
    /* ----------------------------------------- */



    protected function addColumn($table_name, $column_name, $type, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->removeColumn($table_name, $column_name);
            return;
        }
        $this->Driver()->addColumn($table_name, $column_name, $type, $options);
    }

    protected function addIndex($table_name, $column_name, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->removeIndex($table_name, $options );
            return;
        }
        $this->Driver()->addIndex($table_name, $column_name, $options);
    }

    protected function addTimestamps($table_name)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->removeTimestamps($table_name);
            return;
        }
        $this->Driver()->addTimestamps($table_name);
    }

    protected function changeColumn($table_name, $column_name, $type, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change') {
            throw new exception (__FUNCTION__.' は change 内で使用できません。');
        }
        $this->Driver()->changeColumn($table_name, $column_name, $type, $options);
    }

    protected function changeColumnDefault($table_name, $column_name, $default_val)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change') {
            throw new exception (__FUNCTION__.' は change 内で使用できません。');
        }
        $this->Driver()->changeColumnDefault($table_name, $column_name, $default_val);
    }

    protected function createTable($table_name, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->dropTable($table_name);
            return;
        }
        $this->Driver()->createTable($table_name, $options);
    }

    protected function dropTable($table_name, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change') {
            throw new exception (__FUNCTION__.' は change 内で使用できません。');
        }
        $this->Driver()->dropTable($table_name, $options);
    }

    protected function removeColumn($table_name, $column_names)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change') {
            throw new exception (__FUNCTION__.' は change 内で使用できません。');
        }
        $this->Driver()->removeColumn($table_name, $column_names);
    }

    protected function removeIndex($table_name, $options = array())
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change') {
            throw new exception (__FUNCTION__.' は change 内で使用できません。');
        }
        $this->Driver()->removeIndex($table_name, $options);
    }

    protected function removeTimestamps($table_name)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->addTimestamps($table_name);
            return;
        }
        $this->Driver()->removeTimestamps($table_name);
    }

    protected function renameColumn($table_name, $column_name, $new_column_name)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->renameColumn($table_name, $new_column_name, $column_name);
            return;
        }
        $this->Driver()->renameColumn($table_name, $column_name, $new_column_name);
    }

    protected function renameIndex($table_name, $old_name, $new_name)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->renameColumn($table_name, $new_name, $old_name);
            return;
        }
        $this->Driver()->renameIndex($table_name, $old_name, $new_name);
    }

    protected function renameTable($table_name, $new_name)
    {
        list(, $trace) = debug_backtrace();
        if ($trace['function'] === 'change' && !$this->is_up) {
            $this->Driver()->renameTable($new_name, $table_name);
            return;
        }
        $this->Driver()->renameTable($table_name, $new_name);
    }

    /*
    protected function changeTable($table_name, $options = array())
    {
        $this->Driver()->changeTable($table_name, $options);
    }
    */


}
