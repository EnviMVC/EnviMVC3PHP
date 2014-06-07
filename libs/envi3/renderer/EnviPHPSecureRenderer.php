<?php
/**
 * PHPレンダラー
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Renderer
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */

/**
 * PHPレンダラー
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Renderer
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviPHPSecureRenderer
{
    private $_system_conf;
    private $parameter;
    private $display_ref;
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
        $this->parameter[$name] = htmlspecialchars($value, ENT_QUOTES);
    }

    public function display($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        //
        if ($this->parameter) {
            extract($this->parameter);
        }
        include $this->_system_conf['DIRECTORY']['modules'].EnviRequest::getThisModule().DIRECTORY_SEPARATOR.$this->_system_conf['DIRECTORY']['templates'].$file_name;
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
        ob_start();
        if ($this->parameter) {
            extract($this->parameter);
        }
        include $this->_system_conf['DIRECTORY']['modules'].EnviRequest::getThisModule().DIRECTORY_SEPARATOR.$this->_system_conf['DIRECTORY']['templates'].$file_name;
        $this->display_ref = ob_get_contents();
        ob_end_clean();
        return $this->display_ref;
    }

}
