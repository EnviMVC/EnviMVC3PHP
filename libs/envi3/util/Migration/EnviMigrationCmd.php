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
class EnviMigrationCmd
{
    protected $current_work_dir;
    protected $project_dir;
    protected $db_dir;
    protected $migration_dir;
    protected $migration_class_files;
    protected $env;
    protected $migration_status;
    protected $migration_file;
    protected $app_key;

    public function __construct($current_work_dir)
    {
        global $argv;
        // アプリキー
        $this->app_key = $argv[2];
        $this->current_work_dir = $current_work_dir;

        // project dir
        $this->project_dir = $current_work_dir;
        while (!is_file($this->project_dir.'envi.prj') && strlen($this->project_dir) > 2) {
            $this->project_dir = dirname($this->project_dir).DIRECTORY_SEPARATOR;
        }

        $this->db_dir = $this->project_dir.'db'.DIRECTORY_SEPARATOR;
        $this->migration_dir = $this->project_dir.'db'.DIRECTORY_SEPARATOR.'migrate'.DIRECTORY_SEPARATOR;

        $this->migration_class_files = glob($this->migration_dir.$this->app_key.'_*.php');

        sort($this->migration_class_files);

        $this->env = trim(file_get_contents($this->db_dir.'env'));
        foreach ($argv as $k => $arg) {
            if ($k < 2) {
                continue;
            }
            switch (true) {
                case (strpos($arg, 'env:') !== false):
                    list(, $this->env) = explode(':', $arg);
                    break;
            }
        }

        $this->migration_file = $this->db_dir.'envi_migration_'.$this->app_key.'_'.$this->env.'.php';
        $this->migration_status = array('last_version' => '0', 'executed' => array());
        if (is_file($this->migration_file)) {
            $this->migration_status = include_once $this->migration_file;
        }
    }

    public function getMigrationList()
    {
        $res = array();
        foreach ($this->migration_class_files as $migration_class_file) {
            if (in_array($migration_class_file, $this->migration_status['executed']) ) {
                continue;
            }
            $res[] = $migration_class_file;
        }
        return $res;
    }


    public function getRollbackList()
    {
        $res = array();
        $migration_class_files = $this->migration_class_files;
        rsort($migration_class_files);
        foreach ($migration_class_files as $migration_class_file) {
            list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4), 3);
            if ($version > $this->migration_status['last_version']) {
                continue;
            }
            $res[] = $migration_class_file;
        }
        return $res;
    }
    public function getMigrationStatus()
    {
        return $this->migration_status;
    }

    public function setMigrationStatus($setter)
    {
        $this->migration_status = $setter;
    }

    public function saveMigrationStatus()
    {
        if (!isOption('--dry_run')) {
            file_put_contents($this->migration_file, "<?php\n return ".var_export($this->migration_status, true).';');
        }
    }




    public function executeRollback()
    {
        $migration = $this->getMigrationStatus();
        $migration_class_file = array_pop($migration['executed']);

        list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4), 3);
        echo $migration_class_file,"\n";
        include $migration_class_file;
        $class_name = $app.'_'.$migration_class;
        $obj = new $class_name;
        $obj->env = $this->env;
        // ダウン
        $obj->is_up = false;
        $obj->change();
        $obj->down();

        // バージョンを戻す
        if (count($migration['executed']) === 0) {
            // 実行履歴がゼロになった場合
            $migration['last_version'] = 0;
        } else {
            $migration_class_file = $migration['executed'][count($migration['executed']) - 1];
            list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4));
            $migration['last_version'] = $version < $migration['last_version'] ? $version : $migration['last_version'];
        }


        $this->setMigrationStatus($migration);
        $this->saveMigrationStatus();
    }

    public function executeMigrate()
    {
        $migration = $this->getMigrationStatus();
        foreach ($this->getMigrationList() as $migration_class_file) {
            list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4), 3);
            echo $migration_class_file,"\n";
            include $migration_class_file;
            $class_name = $app.'_'.$migration_class;
            $obj = new $class_name;
            $obj->env = $this->env;
            $obj->change();
            $obj->up();
            $migration['last_version'] = $version > $migration['last_version'] ? $version : $migration['last_version'];
            $migration['executed'][]   = $migration_class_file;
            $this->setMigrationStatus($migration);
            $this->saveMigrationStatus();
        }
    }


    public function executeCursorUp()
    {
        $migration = $this->getMigrationStatus();
        foreach ($this->getMigrationList() as $migration_class_file) {
            list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4), 3);
            echo $migration_class_file,"\n";
            include $migration_class_file;
            $class_name = $app.'_'.$migration_class;
            $obj = new $class_name;
            $obj->env = $this->env;
            $migration['last_version'] = $version > $migration['last_version'] ? $version : $migration['last_version'];
            $migration['executed'][]   = $migration_class_file;
            $this->setMigrationStatus($migration);
            $this->saveMigrationStatus();
            return;
        }
    }



    public function executeCursorDown()
    {
        $migration = $this->getMigrationStatus();
        $migration_class_file = array_pop($migration['executed']);

        list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4), 3);
        echo $migration_class_file,"\n";
        include $migration_class_file;
        $class_name = $app.'_'.$migration_class;
        $obj = new $class_name;
        $obj->env = $this->env;
        // ダウン
        $obj->is_up = false;

        // バージョンを戻す
        if (count($migration['executed']) === 0) {
            // 実行履歴がゼロになった場合
            $migration['last_version'] = 0;
        } else {
            $migration_class_file = $migration['executed'][count($migration['executed']) - 1];
            list($app, $version, $migration_class) = explode('_', substr(basename($migration_class_file), 0, -4));
            $migration['last_version'] = $version < $migration['last_version'] ? $version : $migration['last_version'];
        }

        $this->setMigrationStatus($migration);
        $this->saveMigrationStatus();
    }
}