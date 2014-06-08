<?php
/**
 * DI登録されたエクステンションのロード
 *
 * extention()->エクステンション名()<br />
 * でオブジェクトを取得できます。
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

/**
 * DI登録されたエクステンションのロード
 *
 * extention()->エクステンション名()
 *
 * でオブジェクトを取得できます。
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviExtension
{
    private $configuration;
    private $extensions = array();
    private static $instance;

    /**
     * +-- コンストラクタ
     *
     * @access private
     * @param  $configuration
     * @return void
     */
    private function __construct($configuration)
    {
        $this->configuration = $configuration;
        foreach ($configuration as $name => $v) {
            if (isset($v['constant']) && !$v['constant']) {
                continue;
            }
            $class_name = $v['class']['class_name'];
            $this->extensions[$name] = new $class_name(
                Envi::singleton()->parseYml(
                    basename($v['router']['resource']),
                    dirname($v['router']['resource']).DIRECTORY_SEPARATOR)
            );
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
            throw new EnviException($name.' extensionが見つかりませんでした');
        }
        $class_name = $this->configuration[$name]['class']['class_name'];

        if (!isset($this->extensions[$name])) {
            if (!class_exists($class_name, false)) {
                include $this->configuration[$name]['class']['resource'];
            }
            $this->extensions[$name] = new $class_name(
                Envi::singleton()->parseYml(
                    basename($this->configuration[$name]['router']['resource']),
                    dirname($this->configuration[$name]['router']['resource']).DIRECTORY_SEPARATOR
                )
            );
        }
        if (!isset($this->configuration[$name]['class']['singleton']) ||
            !$this->configuration[$name]['class']['singleton']) {
            return clone $this->extensions[$name];
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
            self::$instance = new EnviExtension($configuration);
        }
        return self::$instance;
    }

    public function free()
    {
        $this->extensions = array();
    }

}


function extension()
{
    return EnviExtension::_singleton();
}