<?php
/**
 * PHPレンダラー
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
 * @version    GIT: $Id:$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      File available since Release 1.0.0
 */

/**
 * PHPレンダラー
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2012 Artisan Project
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      Class available since Release 1.0.0
 */
class EnviPHPRenderer
{
    private $_system_conf;

    public function __construct()
    {
        $this->_system_conf = Envi()->getConfigurationAll();
    }

    /**
     * templateに値を格納する
     *
     * @param string $key 格納する名前
     * @param mixed $value 値
     * @return void
     */
    public function setAttribute($name, $value)
    {
        $this->assign($name, $value);
    }

    public function assign($name, $value)
    {
    }

    public function display($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        //
        include $this->_system_conf['DIRECTORY']['modules'].$module_dir.DIRECTORY_SEPARATOR.$this->_system_conf['DIRECTORY']['templates'].$file_name;
    }

    public function is_cached($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        //
    }

    public function clear_cache($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        //
    }

    public function displayRef($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        //
    }

}