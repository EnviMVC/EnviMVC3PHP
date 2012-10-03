<?php
/**
 * EnviMVCのメイン処理
 *
 * フロントのPHP内で、
 * require
 * してください。
 * 必要なコードのlordなどFW動作に必要なことを適宜行います。
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
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      File available since Release 1.0.0
 */


define('ENVI_BASE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ENVI_ROOT_DIR', ENVI_BASE_DIR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

if (!defined('LW_START_MTIMESTAMP')) {
    define('LW_START_MTIMESTAMP', microtime(true));
}


/** ユーザー定義可能な定数 */
if (!defined('ENVI_MVC_APPKEY_PATH')) {
    define('ENVI_MVC_APPKEY_PATH',     realpath(ENVI_ROOT_DIR.'config'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
}
if (!defined('ENVI_MVC_CACHE_PATH')) {
    define('ENVI_MVC_CACHE_PATH',     realpath(ENVI_ROOT_DIR.'cache'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
}

if (!defined('ENVI_SERVER_STATUS_CONF')) {
    define('ENVI_SERVER_STATUS_CONF', realpath(ENVI_ROOT_DIR.'env/ServerStatus.conf'));
}

require ENVI_BASE_DIR.'EnviServerStatus.php';
require ENVI_BASE_DIR.'EnviActionBase.php';
require ENVI_BASE_DIR.'EnviViewBase.php';
require ENVI_BASE_DIR.'Controller.php';
require ENVI_BASE_DIR.'Request.php';
require ENVI_BASE_DIR.'User.php';
require ENVI_BASE_DIR.'EnviValidator.php';
require ENVI_BASE_DIR.'EnviLogWriter.php';
require ENVI_BASE_DIR.'EnviExtension.php';


define('ENVI_ENV', EnviServerStatus()->getServerStatus());

/**
 * +-- Redirect用の例外
 *
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
class redirectException extends Exception
{
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param string $message
     * @param integer $code OPTIONAL:0
     * @param Exception $previous OPTIONAL:null
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        if (Envi()->getConfiguration('SYSTEM', 'use_i18n')) {
            $envi = Envi()->getI18n('envi');
            echo '<a href="'.$message.'">'.$envi['redirect_message'].'</a>';
        } else {
            echo '<a href="'.$message.'">リダイレクトされない場合はこちらへ</a>';
        }

        // parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */

}
/* ----------------------------------------- */

/**
 * +-- 処理中断用の例外
 *
 *
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
class killException extends Exception
{
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param string $message
     * @param integer $code OPTIONAL:0
     * @param Exception $previous OPTIONAL:null
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        // parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * +-- 404エラー
 *
 *
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
class Envi404Exception extends Exception
{
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param string $message
     * @param integer $code OPTIONAL:0
     * @param Exception $previous OPTIONAL:null
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        if (Envi::$debug) {
            echo "{$message} Envi404[{$code}]";
        } else {
            header('HTTP/1.0 404 Not Found');
        }
        // parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */
/**
 * +-- 403エラー
 *
 *
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
class Envi403Exception extends Exception
{
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param string $message
     * @param integer $code OPTIONAL:0
     * @param Exception $previous OPTIONAL:null
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        if (Envi::$debug) {
            echo "{$message} Envi403[{$code}]";
        } else {
            header('HTTP/1.0 403 Forbidden');
        }
        // parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * +-- 汎用的な例外
 *
 *
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
class EnviException extends Exception
{
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param string $message
     * @param integer $code OPTIONAL:0
     * @param Exception $previous OPTIONAL:null
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        if (Envi::$debug) {
            echo "{$message} EnviException[{$code}]";
        } else {
            header('HTTP/1.0 403 Forbidden');
        }
        // parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * +-- Envi
 *
 * @param  $app OPTIONAL:false
 * @param  $debug OPTIONAL:false
 * @return Envi
 */
function Envi($app = false, $debug = false)
{
    return Envi::singleton($app, $debug);
}
/* ----------------------------------------- */

/**
 * EnviMVCのメイン処理
 *
 *
 *
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
class Envi
{
    const DEFAULT_ACCESS = 'DEFAULT';
    const SUCCESS        = 'SUCCESS';
    const ERROR          = 'ERROR';
    const CONFORM        = 'CONFORM';
    const COMMIT         = 'COMMIT';
    const NONE           = 'NONE';

    private static $app_key;
    private $_system_conf;
    private $_i18n = array();
    public $module_dir;
    public $autoload_dirs;
    private static $instance;
    public static $debug;

    private $is_shutDown;

    public $auto_load_classes;

    /**
     * +-- コンストラクタ
     *
     * @access private
     * @param string $app
     * @param boolean $debug OPTIONAL:false
     * @return void
     */
    private function __construct($app, $debug = false)
    {
        self::$debug     = $debug;
        self::$app_key   = $app;
        $this->_system_conf      = $this->parseYml($app.'.yml');
        $autoload_constant_cache = ENVI_MVC_CACHE_PATH.$app.ENVI_ENV.'.autoload_constant.envicc';
        if ($debug || !is_file($autoload_constant_cache)) {
            $autoload_constant_dir = $this->_system_conf['AUTOLOAD_CONSTANT'];
            $autoload_constant = array();
            $autoload_constant[] = $this->_system_conf['SYSTEM']['renderer'];
            if ($autoload_constant_dir) {
                foreach ($autoload_constant_dir as $dir) {
                    if (!is_dir($dir)) {
                        continue;
                    }
                    if (!($dh = opendir($dir))) {
                        continue;
                    }
                    while (($file = readdir($dh)) !== false) {
                        if (strpos($file, '.php')) {
                            $autoload_constant[] = $dir.$file;
                        }
                    }
                    closedir($dh);
                }
            }
            $cache = "<?php\n";
            foreach ($autoload_constant as $v) {
                $cache .= "include '{$v}';\n";
            }
            file_put_contents($autoload_constant_cache, $cache);
        }

        // 国際化
        if ($this->_system_conf['SYSTEM']['use_i18n']) {
            $this->_i18n = $this->parseYml(
                $this->_system_conf['I18N'][Request::getIi8n()],
                ENVI_BASE_DIR.'i18n'.DIRECTORY_SEPARATOR
            );
        }

        $this->autoload_dirs = array_merge(
            array(
                ENVI_BASE_DIR,
            ), $this->_system_conf['AUTOLOAD']
        );
        $auto_load_classes_cache = ENVI_MVC_CACHE_PATH.self::$app_key.ENVI_ENV.'.auto_load_files.envicc';
        if (!$debug && is_file($auto_load_classes_cache)) {
            $this->auto_load_classes = $this->unserialize(file_get_contents($auto_load_classes_cache));
        } else {
            foreach ($this->autoload_dirs as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }
                if (!($dh = opendir($dir))) {
                    continue;
                }
                while (($file = readdir($dh)) !== false) {
                    if (mb_ereg('\.php', $file)) {
                        $class_name = mb_ereg_replace("^(.*)\\.php$", "\\1", $file);
                        $class_name = mb_ereg_replace("^(.*)\\.class$", "\\1", $class_name);
                        $this->auto_load_classes[$class_name] = $dir.$file;
                    }
                }
                closedir($dh);
            }
            file_put_contents($auto_load_classes_cache, $this->serialize($this->auto_load_classes));
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- テスト用
     *
     * @access public
     * @static
     * @return void
     */
    public static function _free()
    {
        self::$instance = NULL;
    }
    /* ----------------------------------------- */


    /**
     * +-- シングルトン
     *
     * @access public
     * @static
     * @return Envi
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            throw new EnviException('Dispatch をコールしてください。');
        }
        return self::$instance;
    }
    /* ----------------------------------------- */


    /**
     * +-- デバッグモードかどうか
     *
     * @access public
     * @return boolean
     */
    public function isDebug()
    {
        return self::$debug;
    }
    /* ----------------------------------------- */

    /**
     * +-- コンフィグデータをすべて返します
     *
     * @access public
     * @return array
     */
    public function &getConfigurationAll()
    {
        return $this->_system_conf;
    }
    /* ----------------------------------------- */



    /**
     * +-- コンフィグデータを返します
     *
     * @access public
     * @param string $key
     * @param string $key2 可変長引数です。OPTIONAL:....
     * @return mixed
     */
    public function getConfiguration($key)
    {
        $args = func_get_args();
        $system_conf = $this->_system_conf;
        foreach ($args as $k) {
            if (!isset($system_conf[$k])) {
                return null;
            }
            $system_conf = $system_conf[$k];
        }
        return $system_conf;
    }
    /* ----------------------------------------- */

    /**
     * +-- アプリキーを返す
     *
     * @access public
     * @return string
     */
    public function getApp()
    {
        return self::$app_key;
    }
    /* ----------------------------------------- */

    /**
     * +-- 国際化データを返します
     *
     * @access public
     * @param  $key
     * @param  $string_key OPTIONAL:NULL
     * @return array
     */
    public function getI18n($key, $string_key = NULL)
    {
        return $string_key === NULL ? $this->_i18n[$key] : $this->_i18n[$key][$string_key];
    }
    /* ----------------------------------------- */

    /**
     * +-- テキスト取得
     *
     * @access public
     * @param string $string_key
     * @param array $replace
     * @return string
     */
    public function getText($string_key, array $replace)
    {
        $rep_arr = array();
        foreach ($replace as $k => $v) {
            $rep_arr['{%'.$k.'%}'] = $v;
        }
        return str_replace(
            array_keys($rep_arr),
            array_values($rep_arr),
            $this->getI18n('gettext', $string_key)
        );
    }
    /* ----------------------------------------- */

    /**
     * +-- Extensionを取得
     *
     * @static
     * @return EnviLogWriter
     */
    public static function extension()
    {
        return EnviExtension::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Validatorを取得
     *
     * @static
     * @return EnviLogWriter
     */
    public static function validator()
    {
        return EnviValidator::singleton();
    }
    /* ----------------------------------------- */

    /**
     * +-- Loggerを取得
     *
     * @static
     * @return EnviLogWriter
     */
    public static function logger()
    {
        return EnviLogWriter::singleton();
    }
    /* ----------------------------------------- */

    /**
     * +-- Extensionを取得(後方互換用)
     *
     * @static
     * @return EnviLogWriter
     */
    public static function getExtension()
    {
        return EnviExtension::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Validatorを取得(後方互換用)
     *
     * @static
     * @return EnviLogWriter
     */
    public static function getValidator()
    {
        return EnviValidator::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Loggerを取得(後方互換用)
     *
     * @static
     * @return EnviLogWriter
     */
    public static function getLogger()
    {
        return EnviLogWriter::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- YAMLファイルをパースする
     *
     * @access public
     * @param string $file YAMLファイルのファイル名
     * @param string $dir YAMLファイルがあるdirectory OPTIONAL:ENVI_MVC_APPKEY_PATH
     * @return array
     */
    public function parseYml($file, $dir = ENVI_MVC_APPKEY_PATH)
    {
        if (!is_file(ENVI_MVC_CACHE_PATH.$file.ENVI_ENV.'.envicc') || (
                self::$debug &&
                @filemtime($dir.$file) > @filemtime(ENVI_MVC_CACHE_PATH.$file.ENVI_ENV.'.envicc')
            )
            ) {
            if (!is_file($dir.$file)) {
                throw new EnviException('not such file '.$dir.$file);
            }
            include_once ENVI_BASE_DIR.'spyc.php';
            ob_start();
            include $dir.$file;
            $buff      = ob_get_contents();
            ob_end_clean();

            $buff = spyc_load($buff);
            $res = isset($buff[ENVI_ENV]) ? array_merge($buff['all'], $buff[ENVI_ENV]) : $buff['all'];
            file_put_contents(ENVI_MVC_CACHE_PATH.$file.ENVI_ENV.'.envicc', $this->serialize($res));
        } else {
            $res      = $this->unserialize(file_get_contents(ENVI_MVC_CACHE_PATH.$file.ENVI_ENV.'.envicc'));
        }
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- ベースのURLを返す
     *
     * @access public
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_system_conf['SYSTEM']['use_i18n'] ?
        $this->_system_conf['SYSTEM']['dispatch_url'].$this->_i18n.'/' : $this->_system_conf['SYSTEM']['dispatch_url'];
    }
    /* ----------------------------------------- */

    /**
     * +-- 処理を中断する
     *
     * Controller::kill()と機能は一緒です。
     *
     * @access public
     * @param string $kill OPTIONAL:''
     * @param boolean $is_shutDown OPTIONAL:true
     * @return void
     */
    public function kill($kill = '', $is_shutDown = true)
    {
        if ($is_shutDown) {
            $this->is_shutDown = $is_shutDown;
        }
        throw new killException($kill);
    }
    /* ----------------------------------------- */


    /**
     * +-- 処理を振り分ける
     *
     * main.phpなどからコールされる。
     * アプリキーとは、メインFW設定YMLの.yml拡張子の前の部分です。
     * {アプリキー}.yml
     * こんな感じです。
     * defaultでは、main.ymlが用意されています。
     *
     * @access public
     * @static
     * @param string $app アプリキー
     * @param boolean $debug デバッグモードのon/off OPTIONAL:false
     * @return void
     */
    public static function dispatch($app, $debug = false)
    {
        try {
            ob_start();
            // オブジェクトの生成
            $className = __CLASS__;
            self::$instance = new Envi($app, $debug);
            $envi = self::$instance;

            // オートロードレジスト
            spl_autoload_register(array('Envi', 'autoload'));

            // リクエストモジュールの初期化
            Request::initialize();
            include_once ENVI_MVC_CACHE_PATH.self::$app_key.ENVI_ENV.'.autoload_constant.envicc';
            $envi->loadExtension();
            $filters = $envi->getConfiguration('FILTER');
            if (isset($filters['input_filter'])) {
                foreach ($filters['input_filter'] as $input_filters) {
                    include_once $input_filters['resource'];
                    $class_name = $input_filters['class_name'];
                    $input_filter = new $class_name;
                    $input_filter->execute();
                    unset($input_filter);
                }
            }
            $envi->_run(true);
            $contents = ob_get_contents();
            ob_end_clean();
            if (isset($filters['output_filter'])) {
                foreach ($filters['output_filter'] as $output_filters) {
                    include_once $output_filters['resource'];
                    $class_name = $output_filters['class_name'];
                    $output_filter = new $class_name;
                    $output_filter->execute($contents);
                    unset($output_filter);
                }
            }
            echo $contents;
        } catch (redirectException $e) {
            throw $e;
        } catch (killException $e) {
            throw $e;
        } catch (PDOException $e) {
            Envi::getLogger()->fatal($e->__toString());
            Envi::getLogger()->fatal($e->getFile().' line on '.$e->getLine());
            if (!$debug) {
                header('HTTP/1.0 500 Internal Server Error');
            }
            throw $e;
        } catch (Exception $e) {
            Envi::getLogger()->fatal($e->__toString());
            Envi::getLogger()->fatal($e->getFile().' line on '.$e->getLine());
            if (!$debug) {
                header('HTTP/1.0 500 Internal Server Error');
            }
            throw $e;
        }
    }
    /* ----------------------------------------- */


    /**
     * +-- Performerから呼ばれる、実処理メソッド
     *
     * FWの外からコールしないでください。
     *
     * @final
     * @access public
     * @param boolean $is_first OPTIONAL:false
     * @return mixed
     */
    final public function _run($is_first = false)
    {
        // 規定のconfig.phpファイルを読み込む
        if ($is_first && is_file($this->_system_conf['DIRECTORY']['modules'].'config.php')) {
            include_once($this->_system_conf['DIRECTORY']['modules'].'config.php');
        }
        $base_module_dir = Controller::isActionChain() ?
            $this->_system_conf['DIRECTORY']['chain_modules'] : $this->_system_conf['DIRECTORY']['modules'];

        // Module
        $module_dir = $base_module_dir.Request::getThisModule().DIRECTORY_SEPARATOR;
        // Action
        $action_dir = $module_dir.$this->_system_conf['DIRECTORY']['actions'];
        // View
        $view_dir   = $module_dir.$this->_system_conf['DIRECTORY']['views'];

        if (realpath($module_dir).DIRECTORY_SEPARATOR !== $module_dir) {
            throw new EnviException($module_dir.'設定ファイルは絶対パスで指定する必要があります。', 10001);
        }

        // モジュール規定のconfig.phpファイルを読み込む
        if (is_file($module_dir.'config.php')) {
            include_once($module_dir.'config.php');
        }

        // アクションの存在確認
        $action_class_path = $action_dir.Request::getThisAction().'Action.class.php';
        $action_sf         = ucwords(Request::getThisAction());

        if (is_file($action_class_path)) {
            // 1ファイル1アクションのパターン
            if (dirname($action_class_path) !== realpath($action_dir)) {
                throw new EnviException('Actionのパスが変です。', 10002);
            }
            include_once($action_class_path);
            $action = Request::getThisAction().'Action';
            $action = new $action;
            if (method_exists($action, "execute{$action_sf}")) {
                $execute        = "execute{$action_sf}";
                $validate  = method_exists($action, "validate{$action_sf}") ? "validate{$action_sf}" : "validate";
                $defaultAccess  = method_exists($action, "defaultAccess{$action_sf}") ? "defaultAccess{$action_sf}" : "defaultAccess";
                $handleError  = method_exists($action, "handleError{$action_sf}") ? "handleError{$action_sf}" : "handleError";
                $isPrivate  = method_exists($action, "isPrivate{$action_sf}") ? "isPrivate{$action_sf}" : "isPrivate";
                $isSSL      = method_exists($action, "isSSL{$action_sf}") ? "isSSL{$action_sf}" : "isSSL";
                $isSecure   = method_exists($action, "isSecure{$action_sf}") ? "isSecure{$action_sf}" : "isSecure";
                $initialize = method_exists($action, "initialize{$action_sf}") ? "initialize{$action_sf}" : "initialize";
                $shutdown   = method_exists($action, "shutdown{$action_sf}") ? "shutdown{$action_sf}" : "shutdown";
            } else {
                $isPrivate      = 'isPrivate';
                $isSSL          = 'isSSL';
                $isSecure       = 'isSecure';
                $initialize     = 'initialize';
                $validate       = 'validate';
                $execute        = 'execute';
                $defaultAccess  = 'defaultAccess';
                $handleError    = 'handleError';
                $shutdown       = 'shutdown';
            }
        } else {
            $sub_action            = mb_ereg_replace('^([a-z0-9]+).*$', '\1', Request::getThisAction());
            $action_sub_class_path = $action_dir.$sub_action.'Actions.class.php';

            if (is_file($action_sub_class_path)) {
                // 1ファイルに複数アクションがあるパターン
                if (dirname($action_sub_class_path) !== realpath($action_dir)) {
                    throw new EnviException('Actionのパスが変です。', 10002);
                }
                include_once($action_sub_class_path);
                $action = $sub_action.'Actions';
                $action = new $action;
                $action_sub_sf = ucwords(mb_ereg_replace("^{$sub_action}", '', Request::getThisAction()));
                if (method_exists($action, "execute{$action_sub_sf}")) {
                    $execute        = "execute{$action_sub_sf}";
                    $isPrivate  = method_exists($action, "isPrivate{$action_sub_sf}") ? "isPrivate{$action_sub_sf}" : "isPrivate";
                    $isSSL      = method_exists($action, "isSSL{$action_sub_sf}") ? "isSSL{$action_sub_sf}" : "isSSL";
                    $isSecure   = method_exists($action, "isSecure{$action_sub_sf}") ? "isSecure{$action_sub_sf}" : "isSecure";
                    $initialize = method_exists($action, "initialize{$action_sub_sf}") ? "initialize{$action_sub_sf}" : "initialize";
                    $shutdown   = method_exists($action, "shutdown{$action_sub_sf}") ? "shutdown{$action_sub_sf}" : "shutdown";
                    $validate  = method_exists($action, "validate{$action_sub_sf}") ? "validate{$action_sub_sf}" : "validate";
                    $defaultAccess  = method_exists($action, "defaultAccess{$action_sub_sf}") ? "defaultAccess{$action_sub_sf}" : "defaultAccess";
                    $handleError  = method_exists($action, "handleError{$action_sub_sf}") ? "handleError{$action_sub_sf}" : "handleError";
                } else {
                    throw new Envi404Exception("execute{$action_sub_sf}がないです", 10003);
                }

            } elseif (is_file($action_dir.'actions.class.php')) {
                // actions.class.phpにまとめて書くパターン
                $action_class_path = $action_dir.'actions.class.php';
                include_once($action_class_path);
                $action = Request::getThisModule().'Actions';
                $action         = new $action;
                if (method_exists($action, "execute{$action_sf}")) {
                    $execute        = "execute{$action_sf}";
                    $validate  = method_exists($action, "validate{$action_sf}") ? "validate{$action_sf}" : "validate";
                    $defaultAccess  = method_exists($action, "defaultAccess{$action_sf}") ? "defaultAccess{$action_sf}" : "defaultAccess";
                    $handleError  = method_exists($action, "handleError{$action_sf}") ? "handleError{$action_sf}" : "handleError";
                    $isPrivate  = method_exists($action, "isPrivate{$action_sf}") ? "isPrivate{$action_sf}" : "isPrivate";
                    $isSSL      = method_exists($action, "isSSL{$action_sf}") ? "isSSL{$action_sf}" : "isSSL";
                    $isSecure   = method_exists($action, "isSecure{$action_sf}") ? "isSecure{$action_sf}" : "isSecure";
                    $initialize = method_exists($action, "initialize{$action_sf}") ? "initialize{$action_sf}" : "initialize";
                    $shutdown   = method_exists($action, "shutdown{$action_sf}") ? "shutdown{$action_sf}" : "shutdown";
                } else {
                    throw new Envi404Exception("execute{$action_sf}がないです", 10003);
                }
            } else {
                throw new Envi404Exception('Actionがないです。', 10004);
            }
        }
        try {
            // アクション開始
            if ($is_first ? $action->$isPrivate() : false) {
                // privateなアクションかどうか
                throw new Envi404Exception('this is private action', 20000);
            } elseif ($is_first ? $action->$isSSL() && !isset($_SERVER['HTTPS']) : false) {
                // sslなアクションかどうか
                throw new Envi404Exception('is not ssl', 20001);
            }
            if ($action->$isSecure() && User::isLogin() === false) {
                // セキュアなページかどうか
                throw new Envi403Exception('please login if you show this action.', 20000);
            }

            // イニシャライズ
            if ($action->$initialize() === false) {
                return false;
            }

            // バリデートする
            $res = true;
            if (method_exists($action, $validate)) {
                $res = $action->$validate();
            }

            // メイン処理の実行
            if ($res === self::DEFAULT_ACCESS) {
                $res = $action->$defaultAccess();
            } elseif ($res === self::ERROR || $res === false) {
                $res = $action->$handleError();
            } else {
                $res = $action->$execute();
            }

            // 終了処理
            if ($action->$shutdown() === false) {
                return true;
            }

            if ($is_first) {
                extension()->executeLastShutdownMethod();
            }

            if ($res === self::NONE || !$res) {
                return true;
            }
            $view_suffix = $res === true ? 'success' : strtolower($res);
        } catch (redirectException $e) {
            $action->$shutdown();
            throw $e;
        } catch (killException $e) {
            if ($this->is_shutDown) {
                $action->$shutdown();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
        $view_class_path = $view_dir.Request::getThisAction()."View_{$view_suffix}.class.php";

        if (is_file($view_class_path)) {
            if (dirname($view_class_path) !== realpath($view_dir)) {
                throw new EnviException('Viewのパスが変です。', 11002);
            }
            include_once $view_class_path;
            $view = Request::getThisAction().'View';
            $view = new $view;
            if (method_exists($view, "execute{$action_sf}")) {
                $execute        = "execute{$action_sf}";
                $setRenderer    = method_exists($view, "setRenderer{$action_sf}") ? "setRenderer{$action_sf}" : "setRenderer";
                $initialize     = method_exists($view, "initialize{$action_sf}") ? "initialize{$action_sf}" : "initialize";
                $shutdown       = method_exists($view, "shutdown{$action_sf}") ? "shutdown{$action_sf}" : "shutdown";
            } else {
                $execute        = 'execute';
                $setRenderer    = 'setRenderer';
                $initialize     = 'initialize';
                $shutdown       = 'shutdown';
            }
        } else {
            $sub_action            = isset($sub_action) ? $sub_action : mb_ereg_replace('^([a-z0-9]+).*$', '\1', Request::getThisAction());
            $view_sub_class_path = $view_dir.$sub_action."Views_{$view_suffix}.class.php";

            if (is_file($view_sub_class_path)) {
                // 1ファイルに複数ビューがあるパターン
                if (dirname($action_sub_class_path) !== realpath($action_dir)) {
                    throw new EnviException('Viewのパスが変です。', 10002);
                }
                include_once($view_sub_class_path);
                $view = $sub_action.'Views';
                $view = new $view;
                $action_sub_sf = ucwords(mb_ereg_replace("^{$sub_action}", '', Request::getThisAction()));
                if (method_exists($action, "execute{$action_sub_sf}")) {
                    $execute        = "execute{$action_sub_sf}";
                    $setRenderer    = method_exists($view, "setRenderer{$action_sub_sf}") ? "setRenderer{$action_sub_sf}" : "setRenderer";
                    $initialize     = method_exists($view, "initialize{$action_sub_sf}") ? "initialize{$action_sub_sf}" : "initialize";
                    $shutdown       = method_exists($view, "shutdown{$action_sub_sf}") ? "shutdown{$action_sub_sf}" : "shutdown";
                } else {
                    throw new Envi404Exception("execute{$action_sub_sf}がないです", 10003);
                }

            }  elseif (is_file($view_dir.'views.class.php')) {
                $view_class_path = $view_dir.'views.class.php';
                include_once($view_class_path);
                $view = Request::getThisModule().'Views';
                $view = new $view;
                if (method_exists($view, "execute{$action_sf}")) {
                    $execute        = "execute{$action_sf}";
                    $setRenderer    = method_exists($view, "setRenderer{$action_sf}") ? "setRenderer{$action_sf}" : "setRenderer";
                    $initialize     = method_exists($view, "initialize{$action_sf}") ? "initialize{$action_sf}" : "initialize";
                    $shutdown       = method_exists($view, "shutdown{$action_sf}") ? "shutdown{$action_sf}" : "shutdown";
                } else {
                    throw new EnviException("execute{$action_sf}がないです", 11003);
                }
            } else {
                throw new EnviException('Viewがないです。', 11004);
            }
        }

        try {
            // View
            // レンダラーセット
            $view->$setRenderer();

            // イニシャライズ
            $res = $view->$initialize();
            if ($res === false) {
                return false;
            }
            $res = $view->$execute();

            // 修了処理
            $res = $view->$shutdown();
            if ($res === false) {
                return false;
            }
        } catch (redirectException $e) {
            $view->$shutdown();
            throw $e;
        } catch (killException $e) {
            if ($this->is_shutDown) {
                $view->$shutdown();
            }
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /* ----------------------------------------- */


    /**
     * +-- DIコンテナ用のエクステンションを読み込む
     *
     * @access private
     * @return void
     */
    private function loadExtension()
    {
        $load_extension_constant = ENVI_MVC_CACHE_PATH.self::$app_key.ENVI_ENV.'.load_extension_constant.envicc';
        $load_extension = ENVI_MVC_CACHE_PATH.self::$app_key.ENVI_ENV.'.load_extension.envicc';
        if (self::$debug || !is_file($load_extension_constant) || !is_file($load_extension)) {
            $extension = isset($this->_system_conf['EXTENSION']['extensions']) && count((array)$this->_system_conf['EXTENSION']['extensions']) > 0 ?
                $this->_system_conf['EXTENSION']['extensions'] : array();
            if ($this->_system_conf['EXTENSION']['load_yml']) {
                $extension = array_merge(
                    $extension,
                    $this->parseYml(basename($this->_system_conf['EXTENSION']['load_yml_resource']), dirname($this->_system_conf['EXTENSION']['load_yml_resource']).DIRECTORY_SEPARATOR)
                );
            }
            if (!is_array($extension)) {
                $extension = array();
            }
            $cache = "<?php\n";
            foreach ($extension as $v) {
                if (isset($v['constant']) && $v['constant'] === true) {
                    $v = $v['class']['resource'];
                    $cache .= "include_once '{$v}';\n";
                }
            }
            file_put_contents($load_extension_constant, $cache);
            file_put_contents($load_extension, $this->serialize($extension));
        } else {
            $extension = $this->unserialize(file_get_contents($load_extension));
        }

        include $load_extension_constant;
        EnviExtension::_singleton($extension);
    }
    /* ----------------------------------------- */

    /**
     * +-- オートロードする
     *
     * @static
     * @access public
     * @param string $class_name
     * @return void
     */
    public static function autoload($class_name)
    {
        $auto_load_classes = self::singleton()->auto_load_classes;
        if (isset($auto_load_classes[$class_name])) {
            include $auto_load_classes[$class_name];
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access public
     * @param
     * @return void
     */
    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
    /* ----------------------------------------- */

    /**
     * +-- 汎用serialize
     *
     * @access public
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        static $is_message_pack;
        if ($is_message_pack === NULL) {
            $is_message_pack = is_callable("msgpack_pack");
        }
        return $is_message_pack ? msgpack_pack($data) : serialize($data);
    }
    /* ----------------------------------------- */

    /**
     * +-- 汎用unserialize
     *
     * @access public
     * @param string $data
     * @return array
     */
    public function unserialize($data)
    {
        static $is_message_pack;
        if ($is_message_pack === NULL) {
            $is_message_pack = is_callable("msgpack_pack");
        }
        return $is_message_pack ? msgpack_unpack($data) : unserialize($data);
    }
    /* ----------------------------------------- */
}

