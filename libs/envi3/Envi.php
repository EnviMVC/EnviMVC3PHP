<?php
/**
 * EnviMVCのメイン処理
 *
 * EnviMVCのコアクラス群です。
 *
 * Envi.phpにまとめて入っていますので、
 *
 * 各種定数の定義後、フロントのPHP内で、 require してください。
 *
 * オートローダーの設定を含む、必要なコードのロードなどのFW動作に必要なことを適宜行います。
 *
 * PHP versions 5
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 * @subpackage_main
 */


if (!defined('ENVI_BASE_DIR')) {
    define('ENVI_BASE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
}

if (!defined('ENVI_ROOT_DIR')) {
    define('ENVI_ROOT_DIR', ENVI_BASE_DIR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
}

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

require ENVI_BASE_DIR.'EnviController.php';
require ENVI_BASE_DIR.'EnviRequest.php';
require ENVI_BASE_DIR.'EnviUser.php';


require ENVI_BASE_DIR.'EnviRouting.php';

require ENVI_BASE_DIR.'EnviValidator.php';
require ENVI_BASE_DIR.'EnviLogWriter.php';
require ENVI_BASE_DIR.'EnviExtension.php';


if (!defined('ENVI_ENV')) {
    define('ENVI_ENV', EnviServerStatus()->getServerStatus());
}

/**
 * +-- Redirect用の例外
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
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
        parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */

}
/* ----------------------------------------- */

/**
 * +-- 処理中断用の例外
 *
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
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
        parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * +-- 404エラー
 *
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
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
            echo $message, 'Envi404[',$code,']';
        } else {
            header('HTTP/1.0 404 Not Found');
        }
        parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */
/**
 * +-- 403エラー
 *
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
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
            echo $message, 'Envi403[',$code,']';
        } else {
            header('HTTP/1.0 403 Forbidden');
        }
        parent::__construct($message, $code, $previous);
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * +-- 汎用的な例外
 *
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
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
            echo $message, 'EnviException[',$code,']';
        } else {
            header('HTTP/1.0 403 Forbidden');
        }
        parent::__construct($message, $code, $previous);
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
 * Enviクラスは、EnviMVCのメイン処理を司ります。
 * Envi()関数を使用して、どこからでもアクセスできますが、基本的にフロントコントローラー以外では使用しません。
 *
 * ロガーや、バリデータ、エクステンションなどへのアクセスもサポートしますが、
 * それぞれに、別なラッパーが用意されているため、通常ではそれを使用します。
 *
 * EnviMVCではルーティングルールの書き換えをサポートしていませんが、Enviクラス内ではprivateメソッドは使用していないため、
 * 継承することで、dispatchや、_runを書き換えることで、ルーティングルールを変更することは出来ます。
 * しかしながら、コレにはあまり意味がありません。
 * 基本的なルールに関しては、yamlで設定可能となっていますので、特別な理由が無い限りは、その範囲内でのルール設定を推奨します。
 *
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class Envi
{
    /**
     * validate()で返すと、defaultAccess()メソッドに遷移し、execute(),handleError(),defaultAccess()で返すと、defaultビューを呼ぶ。
     *
     * @var         string
     */
    const DEFAULT_ACCESS = 'DEFAULT';

    /**
     * validate()で返すと、execute()メソッドに遷移し、execute(),handleError(),defaultAccess()で返すと、successビューを呼ぶ。
     *
     * @var         string
     */
    const SUCCESS        = 'SUCCESS';

    /**
     * validate()で返すと、handleError()メソッドに遷移し、execute(),handleError(),defaultAccess()で返すと、errorビューを呼ぶ。
     *
     * @var         string
     */
    const ERROR          = 'ERROR';
    /**
     * execute(),handleError(),defaultAccess()で返すと、confirmビューを呼ぶ。
     *
     * @var         string
     */
    const CONFORM        = 'CONFORM';

    /**
     * execute(),handleError(),defaultAccess()で返すと、commitビューを呼ぶ。
     *
     * @var         string
     */
    const COMMIT         = 'COMMIT';

    /**
     * execute(),handleError(),defaultAccess()で返すと、ビューをスキップする。
     *
     * @var         string
     */
    const NONE           = 'NONE';

    // +-- バージョン番号定義
    /**
     * バージョン番号を返す
     *
     * @var         string
     */
    const VERSION        = '3.4.15.0';

    /**
     * メジャーバージョン番号を返す
     *
     * @var         int
     */
    const MAJOR_VERSION    = 3;

    /**
     * マイナーバージョン番号を返す
     *
     * @var         int
     */
    const MINOR_VERSION    = 4;

    /**
     * リリースバージョン番号を返す
     *
     * @var         int
     */
    const RELEASE_VERSION  = 15;

    /**
     * テストバージョン番号を返す
     *
     * @var         int
     */
    const ALPHA_VERSION    = 0;
    // ---------------------------


    protected static $app_key;
    protected $_system_conf;
    protected $_i18n = array();
    public $module_dir;
    public $autoload_dirs;
    public $autoload_base_dirs;
    protected static $instance;
    public static $debug;

    protected $is_shutDown;

    public $auto_load_classes = array();

    public static $is_rested = false;

    /**
     * +-- コンストラクタ
     *
     * @access protected
     * @param string $app
     * @param boolean $debug OPTIONAL:false
     * @return void
     */
    protected function __construct($app, $debug = false)
    {
        self::$debug     = $debug;
        self::$app_key   = $app;
        $this->_system_conf      = $this->parseYml($app.'.yml');
        $autoload_constant_cache = ENVI_MVC_CACHE_PATH.$app.'.'.ENVI_ENV.'.autoload_constant.envicc';
        if ($debug || !is_file($autoload_constant_cache)) {
            $this->makeAutoLoadConstantCache($autoload_constant_cache);
        }

        // 国際化
        if ($this->_system_conf['SYSTEM']['use_i18n']) {
            $this->_i18n = $this->parseYml(
                $this->_system_conf['I18N'][EnviRequest::getIi8n()],
                ENVI_BASE_DIR.'i18n'.DIRECTORY_SEPARATOR
            );
        }

        $auto_load_classes_cache = ENVI_MVC_CACHE_PATH.self::$app_key.'.'.ENVI_ENV.'.auto_load_files.envicc';
        if (!$debug && is_file($auto_load_classes_cache)) {
            $this->auto_load_classes = $this->configUnSerialize($auto_load_classes_cache);
        } else {
            $this->autoload_dirs = array_merge(
                array(
                    array('path' => ENVI_BASE_DIR, 'is_psr' => false),
                ), $this->_system_conf['AUTOLOAD']
            );
            $this->makeAutoLoadClassesCache($auto_load_classes_cache);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- Singletonインスタンスを破棄します。
     *
     * テスト用のメソッドです。
     *
     * @access public
     * @static
     * @return void
     * @doc_ignore
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
     * @deprecated `Envi()`関数を使用して下さい。
     * @doc_ignore
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
     * +-- デバッグモードかどうかを取得する
     *
     * デバッグモードで実行されているかを確認し、デバッグモードならtrueそうでないならfalseを返します。
     *
     * @access public
     * @return boolean デバッグモードならtrueそうでないならfalseを返します。
     */
    public function isDebug()
    {
        return self::$debug;
    }
    /* ----------------------------------------- */

    /**
     * +-- コンフィグデータをすべて返します
     *
     * yamlで設定したコンフィグデータをすべて返します。
     *
     * @access public
     * @return array 定義された全てのコンフィグデータ
     */
    public function &getConfigurationAll()
    {
        return $this->_system_conf;
    }
    /* ----------------------------------------- */


    /**
     * +-- コンフィグデータを、キー指定で取得します
     *
     * 取得したいコンフィグデータを指定して、返します。
     *
     * @access public
     * @param string $key 取得したいコンフィグのキー
     * @param string $key2 可変長引数です。OPTIONAL:....
     * @return mixed 定義されたコンフィグデータ
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
     * Dispatchで指定した、アプリキーを返します。
     *
     * @access public
     * @return string アプリキー
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
     * @param string $key
     * @param string $string_key OPTIONAL:NULL
     * @return array 国際化設定情報
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
     * @return string 国際化テキスト
     */
    public function getText($string_key, array $replace)
    {
        $rep_arr = array();
        foreach ($replace as $k => $v) {
            $rep_arr['{%'.$k.'%}'] = $v;
        }
        return strtr($this->getI18n('gettext', $string_key), $rep_arr);
    }
    /* ----------------------------------------- */

    /**
     * +-- Extensionを取得
     *
     * このメソッドは、extension()関数と同義です。
     * 使用方法は、エクステンションの項目を参考にしてください。
     *
     * @static
     * @return EnviExtension エクステンションオブジェクト
     * @see extension()
     */
    public static function extension()
    {
        return EnviExtension::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Validatorを取得
     *
     * このメソッドは、validator()関数と同義です。
     * 使用方法は、バリデーションの項目を参考にしてください。
     *
     * @static
     * @return EnviValidator バリデーションオブジェクト
     * @see validator()
     */
    public static function validator()
    {
        return EnviValidator::singleton();
    }
    /* ----------------------------------------- */

    /**
     * +-- Loggerを取得
     *
     * このメソッドは、logger()関数と同義です。
     * 使用方法は、ロギングの項目を参考にしてください。
     *
     * @static
     * @return EnviLogWriter ログ記録オブジェクト
     * @see logger()
     */
    public static function logger()
    {
        return EnviLogWriter::singleton();
    }
    /* ----------------------------------------- */

    /**
     * +-- Extensionを取得(後方互換用)
     *
     * このメソッドは、extension()関数と同義です。
     * 使用方法は、エクステンションの項目を参考にしてください。
     *
     * @static
     * @return EnviLogWriter
     * @deprecated 古い方法
     * @see extension()
     * @see Envi::extension()
     */
    public static function getExtension()
    {
        return EnviExtension::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Validatorを取得(後方互換用)
     *
     * このメソッドは、validator()関数と同義です。
     * 使用方法は、バリデーションの項目を参考にしてください。
     *
     * @static
     * @return EnviLogWriter
     * @deprecated 古い方法
     * @see validator()
     * @see Envi::validator()
     */
    public static function getValidator()
    {
        return EnviValidator::singleton();
    }
    /* ----------------------------------------- */


    /**
     * +-- Loggerを取得(後方互換用)
     *
     * このメソッドは、logger()関数と同義です。
     * 使用方法は、ロギングの項目を参考にしてください。
     *
     * @static
     * @return EnviLogWriter
     * @deprecated 古い方法
     * @see logger()
     * @see Envi::logger()
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
     * @return array パース後の値
     */
    public function parseYml($file, $dir = ENVI_MVC_APPKEY_PATH)
    {
        if (!is_file(ENVI_MVC_CACHE_PATH.$file.'.'.ENVI_ENV.'.envicc') || (
                self::$debug &&
                @filemtime($dir.$file) > @filemtime(ENVI_MVC_CACHE_PATH.$file.'.'.ENVI_ENV.'.envicc')
            )
           ) {
            if (!is_file($dir.$file)) {
                throw new EnviException('not such file '.$dir.$file);
            }
            ob_start();
            include $dir.$file;
            $buff      = ob_get_contents();
            ob_end_clean();
            if (PHP_MINOR_VERSION <= 2 || PHP_MAJOR_VERSION > 5) {
                if (!function_exists('spyc_load')) {
                    include ENVI_BASE_DIR.'spyc.php';
                }
                $buff = spyc_load($buff);
            } else {
                if (!function_exists('\spyc_load')) {
                    include ENVI_BASE_DIR.'spyc.php';
                }
                $buff = \spyc_load($buff);
            }
            $res = isset($buff[ENVI_ENV]) ? $this->mergeConfiguration($buff['all'], $buff[ENVI_ENV]) : $buff['all'];
            $this->configSerialize(ENVI_MVC_CACHE_PATH.$file.'.'.ENVI_ENV.'.envicc', $res);
        } else {
            $res      = $this->configUnSerialize(ENVI_MVC_CACHE_PATH.$file.'.'.ENVI_ENV.'.envicc');
        }
        return $res;
    }
    /* ----------------------------------------- */


    /**
     * +-- ベースのURLを返します。
     *
     * フレームワーク基底のURLを返します。
     *
     * @access public
     * @return string ベースURL
     */
    public function getBaseUrl()
    {
        return $this->_system_conf['SYSTEM']['use_i18n'] ?
        $this->_system_conf['SYSTEM']['dispatch_url'].$this->_i18n.'/' : $this->_system_conf['SYSTEM']['dispatch_url'];
    }
    /* ----------------------------------------- */

    /**
     * +-- Enviで実行されているすべての処理を中断して、終了します。
     *
     * Enviで実行されているすべての処理を中断して、終了します。
     *
     * EnviController::kill()と機能は一緒です。
     * そちらに詳細を記述しているので、参照して下さい。
     *
     * exit()は処理を完全に終了してしまうため、使用しないほうが無難です。このメソッドは、安全に終了します。
     * 例外を使用しているため、try句内では、正常に動作しない可能性があります。
     *
     * @access public
     * @param string $kill OPTIONAL:''
     * @param boolean $is_shutDown OPTIONAL:true
     * @return void
     * @see EnviController::Kill()
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
            if (!self::$is_rested) {
                // オブジェクトの生成
                $className = __CLASS__;
                self::$instance = new Envi($app, $debug);
                $envi = self::$instance;

                // オートロードレジスト
                spl_autoload_register(array('Envi', 'autoload'));

                // リクエストモジュールの初期化
                EnviRequest::initialize();
                include ENVI_MVC_CACHE_PATH.self::$app_key.'.'.ENVI_ENV.'.autoload_constant.envicc';
                $envi->loadExtension();
                self::$is_rested = true;
                logger();
            } else {
                $className = __CLASS__;
                $envi = self::$instance;
            }

            $filters = $envi->getConfiguration('FILTER');
            if (isset($filters['input_filter']) && is_array($filters['input_filter'])) {
                foreach ($filters['input_filter'] as $input_filters) {
                    $class_name = $input_filters['class_name'];
                    if (!class_exists($class_name, false)) {
                        include $input_filters['resource'];
                    }
                    $input_filter = new $class_name;
                    $input_filter->execute();
                    unset($input_filter);
                }
            }
            $envi->_run(true);
            $contents = ob_get_contents();
            ob_end_clean();
            if (isset($filters['output_filter']) && is_array($filters['output_filter'])) {
                foreach ($filters['output_filter'] as $output_filters) {
                    $class_name = $output_filters['class_name'];
                    if (!class_exists($class_name, false)) {
                        include $output_filters['resource'];
                    }
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
     * @doc_ignore
     */
    final public function _run($is_first = false)
    {
        static $exists_modules = array();
        // 規定のconfig.phpファイルを読み込む
        if ($is_first && is_file($this->_system_conf['DIRECTORY']['modules'].'config.php')) {
            include $this->_system_conf['DIRECTORY']['modules'].'config.php';
        }
        $base_module_dir = EnviController::isActionChain() ?
            $this->_system_conf['DIRECTORY']['chain_modules'] : $this->_system_conf['DIRECTORY']['modules'];

        $this_module = EnviRequest::getThisModule();
        $this_action = EnviRequest::getThisAction();

        // Module
        $module_dir = $base_module_dir.$this_module.DIRECTORY_SEPARATOR;
        // Action
        $action_dir = $module_dir.$this->_system_conf['DIRECTORY']['actions'];
        // View
        $view_dir   = $module_dir.$this->_system_conf['DIRECTORY']['views'];

        if (realpath($module_dir).DIRECTORY_SEPARATOR !== $module_dir) {
            throw new EnviException($module_dir.'設定ファイルは絶対パスで指定する必要があります。', 10001);
        }

        // モジュール規定のconfig.phpファイルを読み込む
        if (!isset($exists_modules[$this_module]) && is_file($module_dir.'config.php')) {
            include($module_dir.'config.php');
        }
        $exists_modules[$this_module] = true;

        // アクションの実行計画を作成する
        // アクションの存在確認
        $action_class_path = $action_dir.$this_action.'Action.class.php';
        $action_sf         = ucwords($this_action);

        // デフォルトのメソッド名をセットする
        $isPrivate      = 'isPrivate';
        $isSSL          = 'isSSL';
        $isSecure       = 'isSecure';
        $initialize     = 'initialize';
        $validate       = 'validate';
        $execute        = 'execute';
        $defaultAccess  = 'defaultAccess';
        $handleError    = 'handleError';
        $shutdown       = 'shutdown';

        // オブジェクトの生成
        if (is_file($action_class_path)) {
            // 1ファイル1アクションのパターン
            if (dirname($action_class_path) !== realpath($action_dir)) {
                throw new EnviException('directory path error', 10002);
            }
            $action = $this_action.'Action';
            if (!class_exists($action, false)) {
                include $action_class_path;
            }
            $action = new $action;
        } else {
            $sub_action            = preg_replace('/^([a-z0-9]+).*$/', '\1', $this_action);
            $action_sub_class_path = $action_dir.$sub_action.'Actions.class.php';

            if (is_file($action_sub_class_path)) {
                // 1ファイルに複数アクションがあるパターン
                if (dirname($action_sub_class_path) !== realpath($action_dir)) {
                    throw new EnviException('directory path error', 10002);
                }

                $action = $sub_action.'Actions';
                if (!class_exists($action, false)) {
                    include $action_sub_class_path;
                }
                $action = new $action;
                $action_sub_sf = ucwords(preg_replace('/^'.$sub_action.'/', '', $this_action));
                if (!method_exists($action, 'execute'.$action_sub_sf)) {
                    throw new Envi404Exception('execute'.$action_sub_sf.' is not exists', 10003);
                }
            } elseif (is_file($action_dir.'actions.class.php')) {
                // actions.class.phpにまとめて書くパターン
                $action_class_path = $action_dir.'actions.class.php';
                $action = $this_module.'Actions';
                if (!class_exists($action, false)) {
                    include $action_class_path;
                }
                $action         = new $action;
                if (!method_exists($action, 'execute'.$action_sf)) {
                    throw new Envi404Exception('execute'.$action_sf.' is not exists', 10003);
                }
            } else {
                throw new Envi404Exception('Action is not exists', 10004);
            }
        }

        // アクションメソッドの実体
        if (isset($action_sub_sf)) {
            $execute        = $execute.$action_sub_sf;
            $isPrivate      = method_exists($action, $isPrivate.$action_sub_sf) ? $isPrivate.$action_sub_sf : $isPrivate;
            $isSSL          = method_exists($action, $isSSL.$action_sub_sf) ? $isSSL.$action_sub_sf : $isSSL;
            $isSecure       = method_exists($action, $isSecure.$action_sub_sf) ? $isSecure.$action_sub_sf : $isSecure;
            $initialize     = method_exists($action, $initialize.$action_sub_sf) ? $initialize.$action_sub_sf : $initialize;
            $shutdown       = method_exists($action, $shutdown.$action_sub_sf) ? $shutdown.$action_sub_sf : $shutdown;
            $validate       = method_exists($action, $validate.$action_sub_sf) ? $validate.$action_sub_sf : $validate;
            $defaultAccess  = method_exists($action, $defaultAccess.$action_sub_sf) ? $defaultAccess.$action_sub_sf : $defaultAccess;
            $handleError    = method_exists($action, $handleError.$action_sub_sf) ? $handleError.$action_sub_sf : $handleError;
        } elseif (method_exists($action, $execute.$action_sf)) {
            $execute        = $execute.$action_sf;
            $validate       = method_exists($action, $validate.$action_sf) ? $validate.$action_sf : $validate;
            $defaultAccess  = method_exists($action, $defaultAccess.$action_sf) ? $defaultAccess.$action_sf : $defaultAccess;
            $handleError    = method_exists($action, $handleError.$action_sf) ? $handleError.$action_sf : $handleError;
            $isPrivate      = method_exists($action, $isPrivate.$action_sf) ? $isPrivate.$action_sf : $isPrivate;
            $isSSL          = method_exists($action, $isSSL.$action_sf) ? $isSSL.$action_sf : $isSSL;
            $isSecure       = method_exists($action, $isSecure.$action_sf) ? $isSecure.$action_sf : $isSecure;
            $initialize     = method_exists($action, $initialize.$action_sf) ? $initialize.$action_sf : $initialize;
            $shutdown       = method_exists($action, $shutdown.$action_sf) ? $shutdown.$action_sf : $shutdown;
        }

        try {
            // アクション開始
            if ($is_first && $action->$isPrivate()) {
                // privateなアクションかどうか
                throw new Envi404Exception('this is protected action', 20000);
            } elseif ($is_first && $action->$isSSL() && !isset($_SERVER['HTTPS'])) {
                // sslなアクションかどうか
                throw new Envi404Exception('is not ssl', 20001);
            }
            if ($action->$isSecure() && EnviUser::isLogin() === false) {
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

        // ビューの実行計画を作成する
        $view_class_path = $view_dir.$this_action.'View_'.$view_suffix.'.class.php';
        // デフォルトのメソッド名
        $execute        = 'execute';
        $setRenderer    = 'setRenderer';
        $initialize     = 'initialize';
        $shutdown       = 'shutdown';
        if (is_file($view_class_path)) {
            // 1ファイル１ビューのパターン
            if (dirname($view_class_path) !== realpath($view_dir)) {
                throw new EnviException('directory path error', 11002);
            }

            $view = $this_action.'View';
            if (!class_exists($view, false)) {
                include $view_class_path;
            }
            $view = new $view;
        } else {
            $sub_action            = isset($sub_action) ? $sub_action : preg_replace('/^([a-z0-9]+).*$/', '\1', $this_action);
            $view_sub_class_path = $view_dir.$sub_action.'Views_'.$view_suffix.'.class.php';

            if (is_file($view_sub_class_path)) {
                // 1ファイルに複数ビューがあるパターン
                if (dirname($action_sub_class_path) !== realpath($action_dir)) {
                    throw new EnviException('directory path error', 10002);
                }
                $view = $sub_action.'Views';
                if (!class_exists($view, false)) {
                    include $view_sub_class_path;
                }
                $view = new $view;
                $view_sub_sf = ucwords(preg_replace('/^'.$sub_action.'/', '', $this_action));
                if (!method_exists($action, 'execute'.$view_sub_sf)) {
                    throw new Envi404Exception('execute'.$view_sub_sf.' is not exists', 10003);
                }

            }  elseif (is_file($view_dir.'views.class.php')) {
                // views.class.phpから実行する
                $view_class_path = $view_dir.'views.class.php';
                $view = $this_module.'Views';
                if (!class_exists($view, false)) {
                    include $view_class_path;
                }
                $view = new $view;
                if (!method_exists($view, 'execute'.$action_sf)) {
                    throw new Envi404Exception('execute'.$action_sf.' is not exists', 10003);
                }
            } else {
                throw new EnviException('View is not exists', 11004);
            }
        }

        // ビューの実メソッド名を取得
        if (isset($view_sub_sf)) {
            $execute        = $execute.$view_sub_sf;
            $setRenderer    = method_exists($view, $setRenderer.$view_sub_sf) ? $setRenderer.$view_sub_sf : $setRenderer;
            $initialize     = method_exists($view, $initialize.$view_sub_sf) ? $initialize.$view_sub_sf : $initialize;
            $shutdown       = method_exists($view, $shutdown.$view_sub_sf) ? $shutdown.$view_sub_sf : $shutdown;
        } elseif (method_exists($view, $execute.$action_sf)) {
            $execute        = $execute.$action_sf;
            $setRenderer    = method_exists($view, $setRenderer.$action_sf) ? $setRenderer.$action_sf : $setRenderer;
            $initialize     = method_exists($view, $initialize.$action_sf) ? $initialize.$action_sf : $initialize;
            $shutdown       = method_exists($view, $shutdown.$action_sf) ? $shutdown.$action_sf : $shutdown;
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
     * +-- レジストのみを行う(コマンドライン用)
     *
     * Envi::dispatch()の代わりに使用します。Enviの機能をコマンドラインで使用したい場合などに使用します。
     * Enviの初期設定のみを行い、Actionなどの解決は行いません。
     *
     * @access      public
     * @static
     * @param       strint $app アプリキー
     * @param       boolean $debug デバッグモードで動作させるかどうか OPTIONAL:false
     * @return      void
     * @see Envi::dispatch()
     */
    public static function registerOnly($app, $debug = false)
    {
        if (self::$is_rested) {
            return true;
        }
        try {
            // オブジェクトの生成
            self::$instance = new Envi($app, $debug);
            $envi = self::$instance;

            // オートロードレジスト
            spl_autoload_register(array('Envi', 'autoload'));

            // リクエストモジュールの初期化
            EnviRequest::initialize();
            include ENVI_MVC_CACHE_PATH.self::$app_key.'.'.ENVI_ENV.'.autoload_constant.envicc';
            $envi->loadExtension();
            self::$is_rested = true;

        } catch (redirectException $e) {
            throw $e;
        } catch (killException $e) {
            throw $e;
        } catch (PDOException $e) {
            Envi::getLogger()->fatal($e->__toString());
            Envi::getLogger()->fatal($e->getFile().' line on '.$e->getLine());
            throw $e;
        } catch (Exception $e) {
            Envi::getLogger()->fatal($e->__toString());
            Envi::getLogger()->fatal($e->getFile().' line on '.$e->getLine());
            throw $e;
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- オートロードする
     *
     * @static
     * @access public
     * @param string $class_name
     * @return void
     * @doc_ignore
     */
    public static function autoload($class_name)
    {
        static $autoload_psr_dir;
        $auto_load_classes = self::singleton()->auto_load_classes;
        if (isset($auto_load_classes[$class_name])) {
            include $auto_load_classes[$class_name];
            return;
        } elseif (isset($auto_load_classes["\\".$class_name])) {
            include $auto_load_classes["\\".$class_name];
            return;
        }

        // psr-0用のDIRECTORY
        if (!$autoload_psr_dir) {
            $autoload_psr_dir = self::singleton()->getConfiguration('AUTOLOAD_PSR');
        }
        if (!is_array($autoload_psr_dir) || count($autoload_psr_dir) === 0) {
            return;
        }

        // psr-0
        $class_name = ltrim($class_name, '\\');
        $file_name  = '';
        $namespace = '';
        if ($last_ns_pos = strripos($class_name, '\\')) {
            $namespace  = substr($class_name, 0, $last_ns_pos);
            $class_name = substr($class_name, $last_ns_pos + 1);
            $file_name  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name);
        foreach ($autoload_psr_dir as $dir_name) {
            if (is_file($dir_name.DIRECTORY_SEPARATOR.$file_name.'.php')) {
                include $dir_name.DIRECTORY_SEPARATOR.$file_name.'.php';
                return;
            }
        }

        foreach ($autoload_psr_dir as $dir_name) {
            if (is_file($dir_name.DIRECTORY_SEPARATOR.$file_name.'.class.php')) {
                include $dir_name.DIRECTORY_SEPARATOR.$file_name.'.class.php';
                return;
            }
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access public
     * @return void
     * @doc_ignore
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
     * @doc_ignore
     */
    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
    /* ----------------------------------------- */

    /**
     * +-- コンフィグファイルのシリアライズを行う
     *
     * @access      public
     * @param       string $file_path
     * @param       mixed $data
     * @return      void
     * @doc_ignore
     */
    public function configSerialize($file_path, $data)
    {
        file_put_contents($file_path, "<?php\n    return ".var_export($data, true).';');
    }
    /* ----------------------------------------- */

    /**
     * +-- コンフィグファイルのアンシリアライズを行う
     *
     * @access      public
     * @param       string $file_path
     * @return      mixed
     * @doc_ignore
     */
    public function configUnSerialize($file_path)
    {
        return include $file_path;
    }
    /* ----------------------------------------- */

    /**
     * +-- 汎用serialize
     *
     * 配列をシリアライズします。
     *
     * @access public
     * @param mixed $data
     * @return string
     * @see Envi::unserialize()
     */
    public function serialize($data)
    {
        static $is_message_pack;
        if ($is_message_pack === NULL) {
            $is_message_pack = is_callable('msgpack_pack');
        }
        return $is_message_pack ? msgpack_pack($data) : serialize($data);
    }
    /* ----------------------------------------- */

    /**
     * +-- 汎用unserialize
     *
     * Envi::serialize()された、配列をシリアライズします。
     *
     * @access public
     * @param string $data
     * @return array
     * @see Envi::serialize()
     */
    public function unserialize($data)
    {
        static $is_message_pack;
        if ($is_message_pack === NULL) {
            $is_message_pack = is_callable('msgpack_pack');
        }
        return $is_message_pack ? msgpack_unpack($data) : unserialize($data);
    }
    /* ----------------------------------------- */

    // +-- protected method

    /**
     * +-- auto_load_classes_cacheの作成
     *
     * @access      protected
     * @param       string $auto_load_classes_cache
     * @return      void
     * @doc_ignore
     */
    protected function makeAutoLoadClassesCache($auto_load_classes_cache)
    {
        $this->autoload_base_dirs = array();
        foreach ($this->autoload_dirs as $key => $dir) {
            if (!is_array($dir)) {
                $dir = array(
                    'is_psr' => false,
                    'path' => $dir,
                );
            }
            $dir['path'] = realpath($dir['path']);
            if (strlen($dir['path']) === 0) {
                throw new EnviException($this->autoload_dirs[$key]['path'].' is non exists aut load dir.');
                continue;
            }
            $dir['path'] .= DIRECTORY_SEPARATOR;
            $this->autoload_dirs[$key] = $dir;
            $this->autoload_base_dirs[$key] = $dir['path'];
        }
        $use_namespace = version_compare(PHP_VERSION, '5.3.0') >= 0;
        foreach ($this->autoload_dirs as $dir) {
            $this->auto_load_classes = array_merge($this->auto_load_classes, $this->mkAutoLoadSubmodules($dir['path'], '', $dir['is_psr'], $use_namespace));
        }
        $this->configSerialize($auto_load_classes_cache, $this->auto_load_classes);
    }
    /* ----------------------------------------- */


    /**
     * +-- サブモジュールを読み込む
     *
     * @access      protected
     * @param       string $dir_name
     * @param       string $name_space
     * @param       boolean $is_psr OPTIONAL:true
     * @param       boolean $use_namespace OPTIONAL:false
     * @return      array
     * @doc_ignore
     */
    protected function mkAutoLoadSubmodules($dir_name, $name_space, $is_psr = true, $use_namespace = false)
    {
        $dir_name = realpath($dir_name).DIRECTORY_SEPARATOR;
        if (array_search($dir_name, $this->autoload_base_dirs) !== false && $name_space !== '') {
            return array();
        }
        if (!is_dir($dir_name)) {
            return array();
        }
        if (!($dh = opendir($dir_name))) {
            return array();
        }
        $res = array();
        while (($file = readdir($dh)) !== false) {
            if (strpos($file, '.') === 0) {
                continue;
            }
            if (is_dir($dir_name.$file) && $is_psr) {
                $res = array_merge($res, $this->mkAutoLoadSubmodules($dir_name.$file, $name_space."\\".$file, $is_psr, $use_namespace));
            } elseif (preg_match('/\.php/', $file)) {
                $class_name = preg_replace("/^(.*)\\.php$/", "\\1", $file);
                $class_name = preg_replace("/^(.*)\\.class$/", "\\1", $class_name);
                if ($use_namespace) {
                    $res[$name_space."\\".$class_name] = $dir_name.$file;
                } else {
                    $res[$class_name] = $dir_name.$file;
                }
            }
        }
        closedir($dh);
        return $res;
    }
    /* ----------------------------------------- */


    /**
     * +-- autoload_constant_cacheの作成
     *
     * @access      protected
     * @param       string $autoload_constant_cache
     * @return      void
     * @doc_ignore
     */
    protected function makeAutoLoadConstantCache($autoload_constant_cache)
    {
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
            $cache .= "include '".$v."';\n";
        }
        file_put_contents($autoload_constant_cache, $cache);
    }
    /* ----------------------------------------- */


    /**
     * +-- DIコンテナ用のエクステンションを読み込む
     *
     * @access protected
     * @return void
     * @doc_ignore
     */
    protected function loadExtension()
    {
        $load_extension_constant = ENVI_MVC_CACHE_PATH.self::$app_key.'.'.ENVI_ENV.'.load_extension_constant.envicc';
        $load_extension = ENVI_MVC_CACHE_PATH.self::$app_key.'.'.ENVI_ENV.'.load_extension.envicc';
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
                    if (!class_exists($v['class']['class_name'], false)) {
                        $v = $v['class']['resource'];
                        $cache .= "include '".$v."';\n";
                    }
                }
            }
            file_put_contents($load_extension_constant, $cache);
            $this->configSerialize($load_extension, $extension);
        } else {
            $extension = $this->configUnSerialize($load_extension);
        }

        include $load_extension_constant;
        EnviExtension::_singleton($extension);
    }
    /* ----------------------------------------- */

    /**
     * +-- 再帰的にコンフィグ情報をマージする
     *
     * @access      protected
     * @param       mixed $all_conf
     * @param       mixed $env_conf
     * @return      array
     * @doc_ignore
     */
    protected function mergeConfiguration($all_conf, $env_conf)
    {
        foreach ($all_conf as $key => $values) {
            if (!isset($env_conf[$key])) {
                // 環境別の設定がない場合は何もしない
                continue;
            }
            if (is_array($env_conf[$key]) && !isset($env_conf[$key][0])) {
                // 入れ子の処理
                $env_conf[$key] = $this->mergeConfiguration($values, $env_conf[$key]);
            }
            $all_conf[$key] = $env_conf[$key];
        }
        return $all_conf;
    }
    /* ----------------------------------------- */

    /* ----------------------------------------- */

}
