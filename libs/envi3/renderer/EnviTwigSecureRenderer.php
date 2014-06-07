<?php
/**
 * Twigレンダラー
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

$ds = DIRECTORY_SEPARATOR;
require ENVI_BASE_DIR."..{$ds}Twig{$ds}Autoloader.php";

/**
 * Twigレンダラー
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
class EnviTwigSecureRenderer
{
    public $_system_conf;
    public $_compile_id;
    private $loader;
    private $twig;
    private $compile_dir;
    private $etc_dir;
    private $config_dir;
    private $template_dir;
    private $_attributer;

    public function __construct()
    {
        $this->_system_conf = Envi()->getConfigurationAll();
        Twig_Autoloader::register();
        $this->setting(EnviRequest::getThisModule());
    }


    public function setting($module_dir)
    {
        $this->compile_dir  = $this->_system_conf['DIRECTORY']['templatec'];
        $this->etc_dir      = $this->_system_conf['DIRECTORY']['templateetc'];
        $this->config_dir   = $this->_system_conf['DIRECTORY']['config'];
        $this->template_dir = $this->_system_conf['DIRECTORY']['modules'].$module_dir.DIRECTORY_SEPARATOR.$this->_system_conf['DIRECTORY']['templates'];

        $this->loader = new Twig_Loader_Filesystem($this->template_dir);
        if (isset($this->_system_conf['DIRECTORY']['base_templates'])) {
            $this->loader->addPath($this->_system_conf['DIRECTORY']['base_templates']);
        }
        $this->twig   = new Twig_Environment($this->loader, array(
            'cache' => $this->compile_dir,
            'debug' => Envi()->isDebug(),
            )
        );
        $this->twig->addExtension('Escaper');

        $this->_attributer = array();
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
        $this->_attributer[$name] = $value;
    }

    public function display($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        echo $this->twig->render($file_name, $this->_attributer);
    }

    public function displayRef($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        return $this->twig->render($file_name, $this->_attributer);
    }
}
