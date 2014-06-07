<?php
/**
 * テストのダミークラス
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
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * テスト用dummy
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class dummyBase
{
    public function __get($key)
    {
    }
    public function __set($key, $val)
    {
    }
    public function __call($func_name, $arg_arr)
    {
    }
}

/**
 * テスト用dummy
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class Logger extends dummyBase
{
}


/**
 * テスト用dummy
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class Envi extends dummyBase
{
    public function getLogger()
    {
        return new Logger();
    }

    public static function singleton()
    {
        static $Envi;
        if (!isset($Envi)) {
            $Envi = new Envi;
        }
        return $Envi;
    }

    public function getConfiguration()
    {
        return false;
    }
}

/**
 * +-- Envi
 *
 * Envi
 *
 * @return Envi
 */
function Envi()
{
    return Envi::singleton();
}
/* ----------------------------------------- */

/**
 * テスト用例外
 *
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviTestException extends exception
{

}

if (!defined('ENVI_ENV')) {
    define('ENVI_ENV', 'unittest');
}

/**
 * test用エクステンションloader
 *
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class extension
{
    private $configuration;
    private $extensions;
    private static $instance;

    /**
     * +-- コンストラクタ
     *
     * @access private
     * @param  $configuration
     * @return void
     */
    private function __construct()
    {
        $this->configuration = EnviTest::singleton()->system_conf['extension'];
        foreach ($this->configuration as $name => $v) {
            if (!$v['constant']) {
                continue;
            }
            include_once $v['class']['resource'];
            $class_name = $v['class']['class_name'];
            $this->extensions[$name] = new $class_name(EnviTest::singleton()->parseYml(basename($v['router']['resource']), dirname($v['router']['resource']).DIRECTORY_SEPARATOR));
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- エクステンションのオブジェクト取得(magicmethod)
     *
     * @access public
     * @param  $name
     * @param  $arguments
     * @return object
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->configuration[$name])) {
            throw new EnviTestException($name.' extensionが見つかりませんでした');
        }
        $class_name = $this->configuration[$name]['class']['class_name'];

        if (!isset($this->configuration[$name]['class']['singleton']) || !$this->configuration[$name]['class']['singleton']) {
            if (!isset($this->extensions[$name])) {
                include_once $this->configuration[$name]['class']['resource'];
                $this->extensions[$name] = array();
            }
            $c = count($this->extensions[$name]);
            $this->extensions[$name][$c] = $class_name(EnviTest::singleton()->parseYml(basename($this->configuration[$name]['router']['resource']), dirname($this->configuration[$name]['router']['resource']).DIRECTORY_SEPARATOR));
            return $this->extensions[$name][$c];
        } elseif (!isset($this->extensions[$name])) {
            include_once $this->configuration[$name]['class']['resource'];
            $this->extensions[$name] = new $class_name(EnviTest::singleton()->parseYml(basename($this->configuration[$name]['router']['resource']), dirname($this->configuration[$name]['router']['resource']).DIRECTORY_SEPARATOR));
        }
        return $this->extensions[$name];
    }
    /* ----------------------------------------- */

    /**
     * +-- メインのAction実行が完了したタイミングで、暗黙的に実行されるMethodを実行
     *
     * @access public
     * @return void
     */
    public function executeLastShutdownMethod()
    {
        foreach ($this->extensions as $name => $val) {
            $shutdownMethod = false;
            if (isset($this->configuration[$name]['class']['lastShutdownMethod'])) {
                $shutdownMethod = $this->configuration[$name]['class']['lastShutdownMethod'];
            }
            if (!$shutdownMethod) {
                continue;
            }
            if (is_array($val)) {
                foreach ($val as $obj) {
                    $obj->$shutdownMethod();
                }
            } else {
                $val->$shutdownMethod();
            }
        }
    }
    /* ----------------------------------------- */

    public static function _singleton($configuration = NULL)
    {
        if (!isset(self::$instance)) {
            self::$instance = new extension($configuration);
        }
        return self::$instance;
    }

    public function free()
    {
        $this->extensions = array();
    }

}

/**
 * +-- シングルトン
 *
 * @return extension
 */
function extension()
{
    return extension::_singleton();
}
/* ----------------------------------------- */
