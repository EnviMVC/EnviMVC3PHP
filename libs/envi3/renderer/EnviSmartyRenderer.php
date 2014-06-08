<?php
/**
 * ArtisanSmartyレンダラー
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

ini_set('include_path', ini_get('include_path') . (DIRECTORY_SEPARATOR === '/' ? ':' : ';') . realpath(ENVI_BASE_DIR.'..'.DIRECTORY_SEPARATOR.'Smarty'));

require 'ArtisanSmarty.class.php';

/**
 * ArtisanSmartyレンダラー
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
class EnviSmartyRenderer
{
    public $_system_conf;
    public $_compile_id;
    public $Smarty;

    public function __construct()
    {
        $this->_compile_id  = EnviRequest::getThisModule();
        $this->_system_conf = Envi()->getConfigurationAll();
        $this->setting(EnviRequest::getThisModule());
    }


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
        $this->Smarty = new Smarty;
        $this->Smarty->compile_dir  = $this->_system_conf['DIRECTORY']['templatec'];
        $this->Smarty->etc_dir      = $this->_system_conf['DIRECTORY']['templateetc'];
        $this->Smarty->config_dir   = $this->_system_conf['DIRECTORY']['config'];
        $this->Smarty->template_dir = $this->_system_conf['DIRECTORY']['modules'].$module_dir.DIRECTORY_SEPARATOR.'templates';
        $this->Smarty->assign('Envi', Envi::singleton());
        $this->Smarty->assign('base_url', Envi::singleton()->getBaseUrl());
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
        $this->Smarty->assign($name, $value);
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
        $this->Smarty->display($file_name, $cache_id, $this->_compile_id);
    }
    /* ----------------------------------------- */

    /**
     * +-- キャッシュ済みかどうか確認する
     *
     * @access      public
     * @param       string $file_name templateのパス
     * @param       string $cache_id キャッシュID OPTIONAL:NULL
     * @param       stiring $dummy2 ダミー変数 OPTIONAL:NULL
     * @return      void
     */
    public function is_cached($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        return $this->Smarty->is_cached($file_name, $cache_id, $this->_compile_id);
    }
    /* ----------------------------------------- */

    /**
     * +-- キャッシュを削除する
     *
     * @access      public
     * @param       string $file_name templateのパス
     * @param       string $cache_id キャッシュID OPTIONAL:NULL
     * @param       stiring $dummy2 ダミー変数 OPTIONAL:NULL
     * @return      void
     */
    public function clear_cache($file_name, $cache_id  = NULL, $dummy2 = NULL)
    {
        return $this->Smarty->clear_cache($file_name, $cache_id, $this->_compile_id);
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
        return $this->Smarty->fetch($file_name, $cache_id, $this->_compile_id);
    }
    /* ----------------------------------------- */
}
