<?php
/**
 * サーバーステータスの取得
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

if (!defined('ENVI_SERVER_STATUS_CONF')) {
    define('ENVI_SERVER_STATUS_CONF',
        realpath(dirname(__FILE__)
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'env'
            .DIRECTORY_SEPARATOR
            .'ServerStatus.conf'
        )
    );
}

/**
 * +-- シングルトン用
 *
 * @return EnviServerStatus
 */
function EnviServerStatus()
{
    return EnviServerStatus::singleton();
}
/* ----------------------------------------- */

/**
 * サーバーステータスの取得
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
class EnviServerStatus
{
    const DEVELOPER  = 'dev';
    const STAGE      = 'stg';
    const PRODUCTION = 'prod';

    private $_stutus_conf;
    private static  $instance;
    /**
     * +-- コンストラクタ
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        $this->_stutus_conf = file_get_contents(ENVI_SERVER_STATUS_CONF);
    }
    /* ----------------------------------------- */

    /**
     * +-- シングルトン
     *
     * @access public
     * @static
     * @return EnviServerStatus
     */
    public static function &singleton()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EnviServerStatus();
        }
        return self::$instance;
    }
    /* ----------------------------------------- */

    /**
     * +-- サーバーステータスを取得する
     *
     * @access public
     * @return string
     */
    public function getServerStatus()
    {
        return $this->_stutus_conf;
    }
    /* ----------------------------------------- */

}

