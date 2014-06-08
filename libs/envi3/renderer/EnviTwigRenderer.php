<?php
/**
 * Twigレンダラー
 *
 * Twigを利用した、テンプレートレンダラーです。
 * Twigはほぼデフォルトのまま使用されます。
 *
 * レンダラーは自由に作成することが出来るため、コピーまたは継承を用いて修正することが出来ます。
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
 * Twigを利用した、テンプレートレンダラーです。
 * Twigはデフォルトのまま使用されます。
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
class EnviTwigRenderer
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

    /**
     * +-- コンストラクタ
     *
     * @access      public
     * @return      void
     * @doc_ignore
     */
    public function __construct()
    {
        $this->_system_conf = Envi()->getConfigurationAll();
        Twig_Autoloader::register();
        $this->setting(EnviRequest::getThisModule());
    }
    /* ----------------------------------------- */


    /**
     * +-- 設定を行う
     *
     * @access      public
     * @param       var_text $module_dir
     * @return      void
     * @doc_ignore
     */
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
        $this->_attributer = array();
    }
    /* ----------------------------------------- */

    /**
     * +-- templateに値を格納する
     *
     * @param string $name 格納する名前
     * @param mixed $value 値
     * @return void
     */
    public function setAttribute($name, $value)
    {
        $this->_attributer[$name] = $value;
    }
    /* ----------------------------------------- */

    /**
     * +-- 画面に描画する
     *
     * 指定されたテンプレートを読み込み、標準出力に出力します。
     *
     * @access      public
     * @param       string $file_name templateのパス
     * @param       string $cache_id キャッシュID OPTIONAL:NULL
     * @param       stiring $dummy2 ダミー変数 OPTIONAL:NULL
     * @return      void
     */
    public function display($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        echo $this->twig->render($file_name, $this->_attributer);
    }
    /* ----------------------------------------- */


    /**
     * +-- 展開したテンプレートの出力結果を返す
     *
     * 指定されたテンプレートを読み込み、実行結果の文字列を返します。
     *
     * @access      public
     * @param       string $file_name templateのパス
     * @param       string $cache_id キャッシュID OPTIONAL:NULL
     * @param       stiring $dummy2 ダミー変数 OPTIONAL:NULL
     * @return      stiring
     */
    public function displayRef($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        return $this->twig->render($file_name, $this->_attributer);
    }
    /* ----------------------------------------- */
}
