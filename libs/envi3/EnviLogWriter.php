<?php
/**
 * ログ記録クラス
 *
 * PHPの汎用ログ記録クラスです。
 * アクセスログ、レベル分けの実行(エラー)ログ、内部ログ、パフォーマンス、実行時間、登録変数
 * が記録可能です。
 * 振る舞いは、設定ファイルに依存します。
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Logger
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * +-- Loggerを取得
 *
 * @static
 * @return EnviLogWriter
 */
function logger()
{
    return EnviLogWriter::singleton();
}
/* ----------------------------------------- */

/**
 * +-- コンソールロガーを取得
 *
 * @static
 * @return EnviLogWriter
 */
function console()
{
    return EnviLogWriter::singleton()->console();
}
/* ----------------------------------------- */


/**
 * ログ記録クラス
 *
 * PHPの汎用ログ記録クラスです。
 * アクセスログ、レベル分けの実行(エラー)ログ、内部ログ、パフォーマンス、実行時間、登録変数
 * が記録可能です。
 * 振る舞いは、設定ファイルに依存します。
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Logger
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviLogWriter
{

    // エラータイプ
    const ETYPE_DEBUG =    1;
    const ETYPE_INFO =     2;
    const ETYPE_NOTICE =   4;
    const ETYPE_WARNING =  8;
    const ETYPE_FATAL =    16;
    const ETYPE_ALL =      31;

    // ロガータイプ
    const LTYPE_FILE =     1;
    const LTYPE_DB =       2;
    const LTYPE_MAIL =     4;
    const LTYPE_DISPLAY =  8;
    const LTYPE_SYSTEM =   16;

    // ログ保存モード
    const LMODE_EACH =    1;
    const LMODE_LAST =    2;

    // リクエストログ保存グローバル変数
    const REQUEST_SERVER =    1;
    const REQUEST_COOKIE =    2;
    const REQUEST_ENV =       4;
    const REQUEST_POST =      8;
    const REQUEST_GET =       16;
    const REQUEST_FILE =      32;
    const REQUEST_SESSION =   64;
    const REQUEST_GLOBALS =   128;
    const REQUEST_ALL =       255;

    // ログの記録形式
    const PURSER_XML =        1;
    const PURSER_SERIALIZE =  2;
    const PURSER_TEXT =       3;



    const MB_AUTO =  1;
    const MB_PASS =  2;


    /**#@+
    * @access private
    */
    private $_container  = array();
    private $_system_conf = array();
    private $_dbi;
    private $_performance;
    private $_log_level = array(
        self::ETYPE_DEBUG   => 'Debug',
        self::ETYPE_INFO    => 'Information',
        self::ETYPE_NOTICE  => 'Notice',
        self::ETYPE_WARNING => 'Warning',
        self::ETYPE_FATAL   => 'Fatal',
    );
    /**#@-*/

    private static $instance;
    private $console;

    public static function singleton()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EnviLogWriter();
        }
        return self::$instance;
    }

    /**
     * +-- コンストラクタ
     *
     * @access private
     * @param
     * @return void
     */
    private function __construct()
    {
        if (!defined('LW_START_MTIMESTAMP')) {
            // 実行時間計測開始
            $this->_performance = microtime(true);
        } else {
            $this->_performance = LW_START_MTIMESTAMP;
        }

        // システムファイルを記録する
        $this->_parse_system_config();

        if (envi()->isDebug()) {
            $this->console = EnviLogWriterConsoleLog::singleton();
            if (isset($this->_system_conf['console'])) {
                $this->console->_setConsoleLogDir($this->_system_conf['console']['value_log_dir']);
                $this->console->_setConsoleLogGetKey($this->_system_conf['console']['value_log_get_key']);
                $this->console->setUseDebugBackTrace($this->_system_conf['console']['flag_use_debug_back_trace']);
            }
        } else {
            $this->console = EnviLogWriterConsoleEmpty::singleton();
        }

        // リクエストログを取得する
        if ($this->_system_conf['system']['flag_use_request_log']) {
            list($debug) = debug_backtrace();
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'performance' => $this->getExecutionTime(),
            );
            if ($this->_system_conf['system']['value_request_log_type'] === self::PURSER_TEXT) {
                $replace = array(
                    '%t' => $res['time'],
                    '%T' => strftime($this->_system_conf['system']['value_response_log_date_time_format'], $res['time']),
                    '%l' => $res['line'],
                    '%f' => $res['file'],
                    '%p' => $res['performance'],
                    '%s' => '',
                    '%S' => '',
                    '%q' => '',
                    '%F' => '',
                    '%e' => '',
                    '%c' => '',
                    '%g' => '',
                    '%P' => '',
                );
            } elseif (
            $this->_system_conf['system']['value_request_log_type'] === self::PURSER_SERIALIZE ||
            $this->_system_conf['system']['value_request_log_type'] === self::PURSER_XML) {
                $replace = $res+array('format_time' => strftime($this->_system_conf['system']['value_response_log_date_time_format'], $res['time']));
            }

            // ロギング
            $this->_writeValiableLog('request', $replace);
        }
    }
    /* ----------------------------------------- */
    /**
     * +-- コンソールロガーの呼び出し
     *
     * @access      public
     * @return      void
     */
    public function console()
    {
        return $this->console;
    }
    /* ----------------------------------------- */
    /**
     * +-- デバッグメッセージ
     *
     * @access public
     * @param string $message メッセージ OPTIONAL:'debugMeaage'
     * @return void
     */
    public function debug($message = 'debug Meaage')
    {
        if ($this->_system_conf['system']['value_error_logging_level'][0] === 0) {
            return;
        }

        list($debug) = debug_backtrace();
        $res = array(
            'time'        => $_SERVER['REQUEST_TIME'],
            'message'     => $message,
            'line'        => $debug['line'],
            'file'        => $debug['file'],
            'level'       => self::ETYPE_DEBUG,
            'performance' => $this->getExecutionTime(),
        );
        $this->_write($res);
    }
    /* ----------------------------------------- */

    /**
     * +-- インフォメーションメッセージ
     *
     * @access public
     * @param string $message メッセージ OPTIONAL:'Information Meaage'
     * @return void
     */
    public function info($message = 'Information Meaage')
    {
        if ($this->_system_conf['system']['value_error_logging_level'][1] === 0) {
            return;
        }
        list($debug) = debug_backtrace();
        $res = array(
            'time'        => $_SERVER['REQUEST_TIME'],
            'message'     => $message,
            'line'        => $debug['line'],
            'file'        => $debug['file'],
            'level'       => self::ETYPE_INFO,
            'performance' => $this->getExecutionTime(),
        );
        $this->_write($res);
    }
    /* ----------------------------------------- */

    /**
     * +-- 忠告レベルのエラーメッセージ
     *
     * @access public
     * @param string $message メッセージ OPTIONAL:'notice Meaage'
     * @return void
     */
    public function notice($message = 'notice Meaage')
    {
        if ($this->_system_conf['system']['value_error_logging_level'][2] === 0) {
            return;
        }
        list($debug) = debug_backtrace();
        $res = array(
            'time'        => $_SERVER['REQUEST_TIME'],
            'message'     => $message,
            'line'        => $debug['line'],
            'file'        => $debug['file'],
            'level'       => self::ETYPE_NOTICE,
            'performance' => $this->getExecutionTime(),
        );
        $this->_write($res);
    }
    /* ----------------------------------------- */

    /**
     * +-- 警告レベルのエラーメッセージ
     *
     * @access public
     * @param string $message メッセージ OPTIONAL:'warningMeaage'
     * @return void
     */
    public function warning($message = 'warning Meaage')
    {
        if ($this->_system_conf['system']['value_error_logging_level'][3] === 0) {
            return;
        }
        list($debug) = debug_backtrace();
        $res = array(
            'time'        => $_SERVER['REQUEST_TIME'],
            'message'     => $message,
            'line'        => $debug['line'],
            'file'        => $debug['file'],
            'level'       => self::ETYPE_WARNING,
            'performance' => $this->getExecutionTime(),
        );
        $this->_write($res);
    }
    /* ----------------------------------------- */

    /**
     * +-- 深刻なエラーメッセージ
     *
     * @access public
     * @param string $message メッセージ OPTIONAL:'fatalMeaage'
     * @return void
     */
    public function fatal($message = 'fatal Meaage')
    {
        if ($this->_system_conf['system']['value_error_logging_level'][4] === 0) {
            return;
        }
        list($debug) = debug_backtrace();
        $res = array(
            'time'        => $_SERVER['REQUEST_TIME'],
            'message'     => $message,
            'line'        => $debug['line'],
            'file'        => $debug['file'],
            'level'       => self::ETYPE_FATAL,
            'performance' => $this->getExecutionTime(),
        );
        $this->_write($res);
    }
    /* ----------------------------------------- */

    /**
     * +-- パフォーマンスを取得する
     *
     * @access public
     * @return float
     */
    public function getExecutionTime()
    {
        $res = (microtime(true) - $this->_performance);
        if (strpos($res, 'E') !== false) {
            $res = 0;
        }
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーオブジェクトから、ログを記録する
     *
     * @param object $error_obj
     * @return void
     */
    public function setErrorByRef($error_obj)
    {
        if (method_exists($error_obj, 'getMessage')) {
            if ($this->_system_conf['system']['value_error_logging_level'][3] === 0) {
                return;
            }
            list($debug) = debug_backtrace();
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'message'     => $error_obj->getMessage(),
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'level'       => self::ETYPE_WARNING,
                'performance' => $this->getExecutionTime(),
            );
            $this->_write($res);
        }

        if (method_exists($error_obj, 'getUserInfo')) {
            if ($this->_system_conf['system']['value_error_logging_level'][2] === 0) {
                return;
            }
            list($debug) = debug_backtrace();
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'message'     => $error_obj->getUserInfo(),
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'level'       => self::ETYPE_INFO,
                'performance' => $this->getExecutionTime(),
            );
            $this->_write($res);
        }

        if (method_exists($error_obj, 'getDebugInfo')) {
            if ($this->_system_conf['system']['value_error_logging_level'][0] === 0) {
                return;
            }
            list($debug) = debug_backtrace();
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'message'     => $error_obj->getDebugInfo(),
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'level'       => self::ETYPE_DEBUG,
                'performance' => $this->getExecutionTime(),
            );
            $this->_write($res);
        }
    }
    /* ----------------------------------------- */
// +---------------------------------------------
    /**
     * スクリプトの最後で呼ぶ
     *
     * @param string $message (optional) メッセージ
     * @return void
     */
    public function shutdown()
    {
        // ログ記録モードがself::LMODE_LASTなら最後にログを記録する
        if ($this->_system_conf['system']['value_logging_mode'] === self::LMODE_LAST) {
            $this->_logging($this->_container, 'error');
        }
        // パフォーマンスログも、アラートログも記録しないなら、ここでリターン
        if (!$this->_system_conf['system']['flag_use_response_log'] && !$this->_system_conf['system']['flag_use_performance_alert_log']) {
            return true;
        }

        $gc = debug_backtrace();
        $debug = $gc[0];
        if (isset($debug['class']) ? $debug['class'] !== 'EnviLogWriter' : true) {
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'performance' => $this->getExecutionTime(),
            );
        } else {
            $debug = $gc[1];
            $res = array(
                'time'        => $_SERVER['REQUEST_TIME'],
                'line'        => $debug['line'],
                'file'        => $debug['file'],
                'performance' => $this->getExecutionTime(),
            );
        }

        if ($this->_system_conf['system']['flag_use_response_log']) {
            if ($this->_system_conf['system']['value_request_log_type'] === self::PURSER_TEXT) {
                $replace = array(
                    '%t' => $res['time'],
                    '%T' => strftime($this->_system_conf['system']['value_response_log_date_time_format'], $res['time']),
                    '%l' => $res['line'],
                    '%f' => $res['file'],
                    '%p' => $res['performance'],
                    '%s' => '',
                    '%S' => '',
                    '%q' => '',
                    '%F' => '',
                    '%e' => '',
                    '%c' => '',
                    '%g' => '',
                    '%P' => '',
                );
            } elseif (
            $this->_system_conf['system']['value_response_log_type'] === self::PURSER_SERIALIZE ||
            $this->_system_conf['system']['value_response_log_type'] === self::PURSER_XML) {
                $replace = $res+array('format_time' => strftime($this->_system_conf['system']['value_response_log_date_time_format'], $res['time']));
            }


            // ロギング
            $this->_writeValiableLog('response', $replace);
        }
        if ($this->_system_conf['system']['flag_use_performance_alert_log'] &&
        ((is_numeric($res['performance']) ? $res['performance'] : 0) > $this->_system_conf['system']['value_performance_alert_execute'])) {

            /**
             * --ファイルパターン
             * %t unixタイムスタンプ
             * %T フォーマットされたタイムスタンプ
             * %l 行
             * %f ファイルパス
             * %p パフォーマンス
             */
            $message = str_replace(array(
                '%t',
                '%T',
                '%m',
                '%l',
                '%f',
                '%p',
            ), array(
                $res['time'],
                strftime($this->_system_conf['system']['value_performance_alert_log_format'], $res['time']),
                'performance alert',
                $res['line'],
                $res['file'],
                is_numeric($res['performance']) ? $res['performance'] : 0,
            ), $this->_system_conf['system']['value_performance_alert_log_format']);

            // ロギング
            $this->_logging($message, 'performance_alert');
        }
    }

    /**#@+
     * @access provate
    */

    /**
     * PHPのグローバル変数ログを記録します。
     *
     * @param string $mode
     * @param array $res (OPTIONAL)
     * @return void
     */
    private function _writeValiableLog($mode = 'request', $res = array())
    {
        $track_mode_type = 'value_track_'.$mode.'_type';
        $mode_log_type = 'value_'.$mode.'_log_type';
        if ($this->_system_conf[$mode][$track_mode_type][0]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_server_key']);
            if ($this->_system_conf[$mode]['flag_limit_server_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_server_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%s'] = $this->_parseArrayList($_SERVER, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_SERVER'] = $this->_getArrayList($_SERVER, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][1]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_cookie_key']);
            if ($this->_system_conf[$mode]['flag_limit_cookie_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_cookie_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%c'] = $this->_parseArrayList($_COOKIE, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_COOKIE'] = $this->_getArrayList($_COOKIE, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][2]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_env_key']);
            if ($this->_system_conf[$mode]['flag_limit_env_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_envtrack_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%e'] = $this->_parseArrayList($_ENV, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_ENV'] = $this->_getArrayList($_ENV, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][3]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_post_key']);
            if ($this->_system_conf[$mode]['flag_limit_post_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_post_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%P'] = $this->_parseArrayList($_POST, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_POST'] = $this->_getArrayList($_POST, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][4]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_get_key']);
            if ($this->_system_conf[$mode]['flag_limit_get_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_get_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%q'] = $this->_parseArrayList($_GET, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_GET'] = $this->_getArrayList($_GET, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][5]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_file_key']);
            if ($this->_system_conf[$mode]['flag_limit_file_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_file_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%F'] = $this->_parseArrayList($_FILES, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_FILES'] = $this->_getArrayList($_FILES, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][6]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_session_key']);
            if ($this->_system_conf[$mode]['flag_limit_session_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_session_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%S'] = $this->_parseArrayList($_SESSION, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['_SESSION'] = $this->_getArrayList($_SESSION, $md5list, $limit);
            }
        }
        if ($this->_system_conf[$mode][$track_mode_type][7]) {
            $md5list = explode(',', $this->_system_conf[$mode]['value_md5_globals_key']);
            if ($this->_system_conf[$mode]['flag_limit_globals_track']) {
                $limit = explode(',', $this->_system_conf[$mode]['value_globals_track_key']);
            } else {
                $limit = false;
            }
            if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
                $res['%g'] = $this->_parseArrayList($GLOBALS, $md5list, $limit);
            } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE || $this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
                $res['GLOBALS'] = $this->_getArrayList($GLOBALS, $md5list, $limit);
            }
        }
        if ($this->_system_conf['system'][$mode_log_type] === self::PURSER_TEXT) {
            $message = str_replace(array_keys($res), array_values($res), $this->_system_conf['system']['value_'.$mode.'_log_format']);
        } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_SERIALIZE) {
            $message = urlencode(serialize($res));
        } elseif ($this->_system_conf['system'][$mode_log_type] === self::PURSER_XML) {
            $res = array($mode => $res);
            $message = $this->_xmlCreate($res);
        }

        $this->_logging($message, $mode);
    }

    /**
     * エラーメッセージを記録する
     *
     * @param array &$res
     * @return void
     */
    private function _write(&$res)
    {
        static $time_array = array();
        if (!isset($time_array[$res['time']])) {
            $time_array[$res['time']] = strftime($this->_system_conf['system']['value_error_log_date_time_format'], $res['time']);
        }
        if ($this->_system_conf['system']['value_error_log_type'] === self::PURSER_TEXT) {
                /**
                 * --ファイルパターン
                 * %t unixタイムスタンプ
                 * %T フォーマットされたタイムスタンプ
                 * %l 行
                 * %f ファイルパス
                 * %L エラーレベル
                 * %p パフォーマンス
                 */
            $message = str_replace(array(
                '%t',
                '%T',
                '%m',
                '%l',
                '%f',
                '%L',
                '%p',
            ), array(
                $res['time'],
                $time_array[$res['time']],
                $this->_system_conf['mb_encoding']['value_convert_encode'] === self::MB_AUTO ? mb_convert_encoding($res['message'], mb_internal_encoding(), 'auto') : $res['message'],
                $res['line'],
                $res['file'],
                $this->_log_level[$res['level']],
                $res['performance'],
            ), $this->_system_conf['system']['value_error_log_format']);
        } else {
            $res['format_time'] = $time_array[$res['time']];
            $res['level']       = $this->_log_level[$res['level']];
            if ($this->_system_conf['system']['value_'.$mode.'_log_type'] === self::PURSER_SERIALIZE) {
                $message = urlencode(serialize($res));
            } elseif ($this->_system_conf['system']['value_'.$mode.'_log_type'] === self::PURSER_XML) {
                $res = array('error' => $res);
                $message = $this->_xmlCreate($res);
            }
        }
        if ($this->_system_conf['system']['value_logging_mode'] === self::LMODE_EACH) {
            // ロギング
            $this->_logging($message, 'error');
        } else {
            // メモリに格納
            $this->_container[] = $message;
        }
        // コンソール
        $this->console->_systemLog($message, $this->_log_level[$res['level']]);

    }

    /**
     * ログを記録する
     *
     * @param stging|array &$message
     * @param string $mode
     * @return void
     */
    private function _logging(&$message, $mode)
    {
        static $logging_count = array();
        static $file_name;
        if (!isset($logging_count[$mode])) {
            $logging_count[$mode] = 0;
        }
        if (is_array($message)) {
            if (count($message) === 0) {
                return;
            }
            $res = join('\n', $message).'\n';
        } else {
            $res = $message.'\n';
        }

        if ($this->_system_conf['system']['value_'.$mode.'_logging_type'][0] === 1) {
            if (!$file_name) {
                $file_name = strftime($this->_system_conf['file']['value_'.$mode.'_log_file_name']);
            }
            // ファイル
            error_log(
                $res,
                3,
                $this->_system_conf['file']['value_'.$mode.'_log_file_path']
                .$file_name
            );
            if ($this->_system_conf['file']['value_'.$mode.'_log_rotate_size'] !== 0 && $logging_count[$mode] === 0) {
                // ログローテート
                $this->_logRotate($this->_system_conf['file']['value_'.$mode.'_log_file_path']
                    .$file_name,
                    $this->_system_conf['file']['value_'.$mode.'_log_rotate_size']
                );
            }
        }

        if ($this->_system_conf['system']['value_'.$mode.'_logging_type'][1] === 1) {
            // DB
            $ck = $this->_dbi->autoExecute(
                $this->_system_conf['db']['value_'.$mode.'_log_table_name'],
                array($this->_system_conf['db']['value_'.$mode.'_log_column_name'] => $res)
            );

            if (DB::isError($ck)) {
                error_log('['.date('Y-m-d').'] logWriter Error : Can\'t autoExecute Log Send.');
            }
        }

        if ($this->_system_conf['system']['value_'.$mode.'_logging_type'][2] === 1) {
            // mail
            mb_send_mail($this->_system_conf['mail']['value_'.$mode.'_log_mail_to'],
                $this->_system_conf['mail']['value_'.$mode.'_log_mail_subject'],
                $res,
                'from:'.$this->_system_conf['mail']['value_'.$mode.'_log_mail_from']
            );
        }

        if ($this->_system_conf['system']['value_'.$mode.'_logging_type'][3] === 1) {
            // 表示
            echo '<b>'.nl2br($res).'</b>';
        }

        if ($this->_system_conf['system']['value_'.$mode.'_logging_type'][4] === 1) {
            // システム規定
            error_log($res, 0);
        }
        $logging_count[$mode]++;
    }

    /**
     * ログローテート
     *
     * @param string $fpath ログファイルパス
     * @param integer $masize ログファイルの最大サイズ
     * @return void
     */
    private function _logRotate($fpath, $maxsize)
    {
        if (is_file($fpath) ? filesize($fpath) > $maxsize : false) {
            $i = 0;
            while (is_file($fpath.++$i)) {
            }
            rename($fpath, $fpath.$i);
        }
    }

    /**
     * 配列を再帰的に、パースさせる
     *
     * @param array &$array
     * @param array $md5_list
     * @param array|boolean $limit
     * @return string
     */
    private function _parseArrayList(&$array, $md5_list = array(), $limit = false)
    {
        $res = '';
        $md5_list = array_flip($md5_list);
        if (!is_array($array)) {
            $res = $array;
        } else {
            if ($limit === false) {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $res .= '['.$key.']=>'.$this->_parseArrayList($value, isset($md5_list[$key]) !== false ? array_keys($value) : array());
                    } else {
                        $res .= '['.$key.']=>'."'".(isset($md5_list[$key]) !== false ? md5($value) : $value)."'";
                    }
                }

            } else {
                foreach ($limit as $key) {
                    if (!isset($array[$key])) {
                        continue;
                    }

                    if (is_array($array[$key])) {
                        $res .= '['.$key.']=>'.$this->_parseArrayList($array[$key], isset($md5_list[$key]) !== false ? array_keys($array[$key]) : array());
                    } else {
                        $res .= '['.$key.']=>'."'".(isset($md5_list[$key]) !== false ? md5($array[$key]) : $array[$key])."'";
                    }
                }
            }
        }
        return $res;
    }

    private function _getArrayList(&$array, $md5_list = array(), $limit = false)
    {
        $res = array();
        $md5_list = array_flip($md5_list);
        if (!is_array($array)) {
            $res = $array;
        } else {
            if ($limit === false) {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        $res[$key]= $this->_getArrayList($value, isset($md5_list[$key]) !== false ? array_keys($value) : array());
                    } else {
                        $res[$key]= (isset($md5_list[$key]) !== false ? md5($value) : $value);
                    }
                }

            } else {
                foreach ($limit as $key) {
                    if (is_array($array[$key])) {
                        $res[$key] = $this->_getArrayList($value, isset($md5_list[$key]) !== false ? array_keys($value) : array());
                    } else {
                        $res[$key] = (isset($md5_list[$key]) !== false ? md5($value) : $value);
                    }
                }
            }
        }
        return $res;
    }


    /**
     * 設定ファイルを読み込みます。
     *
     * @param string $logging_type 設定のタイプ
     * @param string $ini_path 設定ファイルのパス
     * @return void
     */
    private function _parse_system_config()
    {
        /** 設定ファイル読み込み */
        $dir = ENVI_MVC_CACHE_PATH;
        $bk_file = $dir.__CLASS__.Envi::singleton()->getApp().'.'.ENVI_ENV.'.envicc';
        $autoload_constant_cache = $dir.Envi()->getApp().'.'.ENVI_ENV.'.autoload_constant.envicc';
        if (is_file($bk_file) && !Envi()->isDebug() && filemtime($bk_file) > filemtime($autoload_constant_cache)) {
            $this->_system_conf = Envi()->unserialize(file_get_contents($bk_file));
            return;
        }

        $this->_system_conf = Envi::singleton()->getConfiguration('LOGGER');

        /** /設定ファイル読み込み */

        // ログ出力レベル
        $this->_system_conf['system']['value_error_logging_level'] = array(
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_level']), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_level']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_level']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_level']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_level']>>4), -1, 1),

        );

        // ログ記録タイプ
        $this->_system_conf['system']['value_error_logging_type'] = array(
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_error_logging_type']>>4), -1, 1),
        );

        // レスポンスログ記録タイプ
        $this->_system_conf['system']['value_response_logging_type'] = array(
            (int)substr(decbin($this->_system_conf['system']['value_response_logging_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_response_logging_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_response_logging_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_response_logging_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_response_logging_type']>>4), -1, 1),
        );
        // レスポンスログ記録グローバル変数
        $this->_system_conf['response']['value_track_response_type'] = array(
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>4), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>5), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>6), -1, 1),
            (int)substr(decbin($this->_system_conf['response']['value_track_response_type']>>7), -1, 1),
        );
        // パフォーマンスアラートログ記録タイプ
        $this->_system_conf['system']['value_performance_alert_logging_type'] = array(
            (int)substr(decbin($this->_system_conf['system']['value_performance_alert_logging_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_performance_alert_logging_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_performance_alert_logging_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_performance_alert_logging_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_performance_alert_logging_type']>>4), -1, 1),
        );

        // リクエストログ記録グローバル変数
        $this->_system_conf['request']['value_track_request_type'] = array(
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>4), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>5), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>6), -1, 1),
            (int)substr(decbin($this->_system_conf['request']['value_track_request_type']>>7), -1, 1),
        );

        // リクエストログ記録タイプ
        $this->_system_conf['system']['value_request_logging_type'] = array(
            (int)substr(decbin($this->_system_conf['system']['value_request_logging_type']), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_request_logging_type']>>1), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_request_logging_type']>>2), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_request_logging_type']>>3), -1, 1),
            (int)substr(decbin($this->_system_conf['system']['value_request_logging_type']>>4), -1, 1),
        );

        $handle = @ fopen($bk_file, 'w');
        $writestring = Envi()->serialize($this->_system_conf);
        @ fwrite($handle, $writestring);
        @ fclose($handle);
    }


    /**
     * Xmlを作成する
     *
     * @param array $array 配列
     * @param boolean $is_start 再帰キー
     */
    private function _xmlCreate(&$array, $is_start = true)
    {
        $res = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($is_start) {
                    $res .= "<".$key.">\n";
                    $res .= $this->_xmlCreate($value, false);
                    $res .= "</".$key.">";
                } else {
                    $res .="<parameter key='".htmlspecialchars($key)."'>";
                    $res .= $this->_xmlCreate($value, false);
                    $res .= "</".$key.">";
                }
            } else {
                if ($is_start) {
                    $res .= "<".$key.">\n";
                    $res .= htmlspecialchars($value);
                    $res .= "</".$key.">";
                } else {
                    $res .="<parameter key='".htmlspecialchars($key)."'>";
                    $res .= htmlspecialchars($value);
                    $res .= "</parameter>\n";
                }
            }
        }
        return $res;
    }
    /**#@-*/
// -------------------------------------------------------------
}
/**
 * @package
 * @subpackage
 * @sinse 0.1
 * @author     akito<akito-artisan@five-foxes.com>
 */
class EnviLogWriterConsoleEmpty extends EnviLogWriterConsole
{
    private static $instance     = NULL;

    /**
     * +-- デバッグトレースも記録するかどうかを設定して、元の値を返す
     *
     * @access      public
     * @return      void
     */
    public function setUseDebugBackTrace()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- Consoleにのみログを排出します
     *
     * @access      public
     * @return      void
     */
    public function log()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- シングルトン
     *
     * @access      public
     * @static
     * @return      self
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EnviLogWriterConsoleEmpty();
        }
        return self::$instance;
    }
    /* ----------------------------------------- */

    /**
     * +-- コンストラクタ
     *
     * @access      private
     * @return      void
     */
    private function __construct()
    {
    }
    /* ----------------------------------------- */

    public function _systemLog()
    {
    }

    public function stopwatch()
    {
    }


    public function _queryLog()
    {
    }


    public function _setConsoleLogDir()
    {
    }
    public function _setConsoleLogGetKey()
    {
    }

}

/**
 * @package
 * @subpackage
 * @sinse 0.1
 * @author     akito<akito-artisan@five-foxes.com>
 */
class EnviLogWriterConsole
{
    protected $use_debug_back_trace = false;
    protected $use_console_log     = false;
    protected $is_console_log      = false;
    protected $console_log_dir     = NULL;
    protected $console_log_get_key = NULL;
    protected $console_log_get_hash = NULL;
    protected $console_log_write_dir     = NULL;
    protected $_performance           = NULL;
    public function getLogHash()
    {
        return $this->console_log_get_hash;
    }
}


/**
 * コンソールログ記録クラス
 *
 * ブラウザのコンソールログや、envi コマンドでのログ収集が可能な形式での、ロギングです。
 * 振る舞いは、設定ファイルに依存します。
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Logger
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviLogWriterConsoleLog extends EnviLogWriterConsole
{
    private static $instance     = NULL;
    /**
     * +-- デバッグトレースも記録するかどうかを設定して、元の値を返す
     *
     * @access      public
     * @param       boolean $setter
     * @return      boolean
     */
    public function setUseDebugBackTrace($setter)
    {
        $res = $this->use_debug_back_trace;
        $this->use_debug_back_trace = $setter;
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- Consoleにのみログを排出します
     *
     * @access      public
     * @param       var_text $log_text
     * @return      void
     */
    public function info($log_text)
    {
        if (!$this->is_console_log) {
            return;
        }
        $debug = array();

        $debug['log_text'] = $log_text;
        $debug['log_mode'] = 'info';
        $this->writeLog($debug);
    }
    /* ----------------------------------------- */



    /**
     * +-- Consoleにのみログを排出します
     *
     * @access      public
     * @param       var_text $log_text
     * @return      void
     */
    public function log($log_text)
    {
        if (!$this->is_console_log) {
            return;
        }
        $debug = array();
        $debug['log_text'] = $log_text;
        $debug['log_mode'] = 'log';
        $this->writeLog($debug);
    }
    /* ----------------------------------------- */

    /**
     * +-- Consoleにのみログを排出します
     *
     * @access      public
     * @param       var_text $log_text
     * @return      void
     */
    public function error($log_text)
    {
        if (!$this->is_console_log) {
            return;
        }
        $debug = array();

        $debug['log_text'] = $log_text;
        $debug['log_mode'] = 'error';
        $this->writeLog($debug);
    }
    /* ----------------------------------------- */

    /**
     * +-- Consoleにのみログを排出します
     *
     * @access      public
     * @param       var_text $log_text
     * @return      void
     */
    public function warn($log_text)
    {
        if (!$this->is_console_log) {
            return;
        }
        $debug = array();

        $debug['log_text'] = $log_text;
        $debug['log_mode'] = 'warn';
        $this->writeLog($debug);
    }
    /* ----------------------------------------- */

    protected function writeLog($debug)
    {
        $performance = microtime(true) - $this->_performance;
        $debug['performance'] = $performance;
        $memory_get_usage = memory_get_usage();
        if ($this->use_debug_back_trace) {
            $debug['backtrace'] = debug_backtrace();
            array_shift($debug['backtrace']);
            $tmp = array_shift($debug['backtrace']);
            $debug['file'] = $tmp['file'];
            $debug['line'] = $tmp['line'];
        } else {
            list(, $tmp) = debug_backtrace();
            $debug['file'] = $tmp['file'];
            $debug['line'] = $tmp['line'];
        }
        $debug['memory_get_usage'] = $memory_get_usage;
        $debug = json_encode($debug);
        $umask = umask(0);
        error_log($debug."\n", 3, $this->console_log_write_dir.'console.log');
        error_log($debug."\n", 3, $this->console_log_dir.DIRECTORY_SEPARATOR.Envi()->getApp().'console.log');
        umask($umask);
    }


    /**
     * +-- シングルトン
     *
     * @access      public
     * @static
     * @return      self
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EnviLogWriterConsoleLog();
        }
        return self::$instance;
    }
    /* ----------------------------------------- */

    /**
     * +-- コンストラクタ
     *
     * @access      private
     * @return      void
     */
    private function __construct()
    {
        $this->use_console_log = envi()->isDebug();
        if (!$this->use_console_log) {
            return;
        }
        if (defined('LW_START_MTIMESTAMP')) {
            $this->_performance = LW_START_MTIMESTAMP;
        } elseif (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $this->_performance = $_SERVER['REQUEST_TIME_FLOAT'];
        } else {
            // 実行時間計測開始
            $this->_performance = microtime(true);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- システムログ(直接コールできません)
     *
     * @access      public
     * @param       var_text $log_text
     * @param       var_text $log_type
     * @return      void
     */
    public function _systemLog($log_text, $log_type)
    {
        if (!$this->is_console_log) {
            return;
        }

        $performance = microtime(true) - $this->_performance;
        $memory_get_usage = memory_get_usage();
        $debug = array();
        if ($this->use_debug_back_trace) {
            $debug['backtrace'] = debug_backtrace();
            array_shift($debug['backtrace']);
            array_shift($debug['backtrace']);
            $tmp = array_shift($debug['backtrace']);
            $debug['file'] = $tmp['file'];
            $debug['line'] = $tmp['line'];
        }
        $debug['performance'] = $performance;
        $debug['log_text'] = $log_text;
        $debug['memory_get_usage'] = $memory_get_usage;
        if ($log_type === 'Information') {
            $debug['log_mode'] = 'info';
        } elseif ($log_type === 'Notice') {
            $debug['log_mode'] = 'warn';
        } elseif ($log_type === 'Warning') {
            $debug['log_mode'] = 'error';
        } elseif ($log_type === 'Fatal') {
            $debug['log_mode'] = 'error';
        } else {
            $debug['log_mode'] = 'log';
        }

        $debug = json_encode($debug);
        $umask = umask(0);
        error_log($debug."\n", 3, $this->console_log_write_dir.'system.log');
        error_log($debug."\n", 3, $this->console_log_dir.DIRECTORY_SEPARATOR.Envi()->getApp().'system.log');
        umask($umask);
    }
    /* ----------------------------------------- */

    /**
     * +-- ストップウオッチの使用
     *
     * @access      public
     * @return      差分
     */
    public function stopwatch()
    {
        static $watch;
        if (!$this->is_console_log) {
            return;
        }
        if (empty($watch)) {
            $watch = $this->_performance;
        }
        $res = $watch;
        $watch = microtime(true);
        return $watch-$res;
    }
    /* ----------------------------------------- */

    /**
     * +-- クエリログ(直接コールできません)
     *
     * @access      public
     * @param       & $dbi
     * @return      void
     */
    public function _queryLog(&$dbi)
    {
        if (!$this->is_console_log) {
            return;
        }
        $execution_time = console()->stopwatch();
        $performance = microtime(true) - $this->_performance;
        $memory_get_usage = memory_get_usage();
        $debug = array();
        if ($this->use_debug_back_trace) {
            $debug['backtrace'] = debug_backtrace();
            array_shift($debug['backtrace']);
            array_shift($debug['backtrace']);
            array_shift($debug['backtrace']);
        }
        $debug['execution_time'] = $execution_time;
        $debug['performance'] = $performance;
        $debug['log_text'] = $dbi->getLastQuery();
        $debug['memory_get_usage'] = $memory_get_usage;
        $debug = json_encode($debug);

        $umask = umask(0);
        error_log($debug."\n", 3, $this->console_log_write_dir.'query.log');
        error_log($debug."\n", 3, $this->console_log_dir.DIRECTORY_SEPARATOR.Envi()->getApp().'query.log');
        umask($umask);
    }
    /* ----------------------------------------- */

    public function __destruct()
    {
        if (!$this->is_console_log) {
            return;
        }
        $include_files = get_included_files();
        $debug = array(
            'count' => count($include_files),
            'include_files' => $include_files,
            'system_execution_time' => microtime(true) - $this->_performance,
        );
        error_log(json_encode($debug)."\n", 3, $this->console_log_write_dir.'included_files.log');
        error_log(json_encode($debug)."\n", 3, $this->console_log_dir.DIRECTORY_SEPARATOR.Envi()->getApp().'included_files.log');
    }

    public function _setConsoleLogDir($setter)
    {
        if (empty($setter) || !is_dir($setter) || !is_writable($setter)) {
            $this->is_console_log = false;
            return;
        }
        $this->console_log_dir = $setter;
        $this->initialize();
    }
    public function _setConsoleLogGetKey($setter)
    {
        if (empty($setter)) {
            $this->is_console_log = false;
            return;
        }
        $this->console_log_get_key = $setter;
        $this->initialize();
    }

    private function initialize()
    {
        if (!$this->use_console_log) {
            return;
        }
        if (!isset($this->console_log_dir, $this->console_log_get_key)) {
            $this->is_console_log = false;
            return;
        }
        if (!isset($_GET[$this->console_log_get_key])) {
            $this->is_console_log = false;
            return;
        }
        $this->is_console_log = true;
        $umask = umask(0);
        $this->console_log_get_hash = microtime(true).sha1(microtime(true));
        mkdir($this->console_log_dir.DIRECTORY_SEPARATOR.$this->console_log_get_hash, 0777, true);
        umask($umask);
        setcookie($this->console_log_get_key, $this->console_log_get_hash, $_SERVER['REQUEST_TIME']+36000, '/');

        $this->console_log_write_dir = $this->console_log_dir.DIRECTORY_SEPARATOR.$this->console_log_get_hash.DIRECTORY_SEPARATOR;
    }

}
