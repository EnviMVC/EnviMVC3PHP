<?php
/**
 * ActionControllerから、Framework自体の振る舞いを変更する
 *
 * ActionControllerから、全て静的にコールされます。
 * ActionChainの仕組みを利用するなど、FW自体に振る舞いの変更を通知します。
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
 * ActionControllerから、Framework自体の振る舞いを変更する
 *
 * ActionControllerから、全て静的にコールされます。
 * ActionChainの仕組みを利用するなど、FW自体に振る舞いの変更を通知します。
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
class EnviController
{
    private static $_action_chain      = array();
    private static $_action_chain_data = array();
    private static $_system_conf;
    private static $_is_action_chain   = false;
    private static $_action_chain_name = NULL;

    /**
     * +-- オブジェクト化させない
     *
     * @access      private
     * @return      void
     */
    private function __construct()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションチェインの中かどうかを返す
     *
     * 実行中のアクションコントローラーが、アクションチェインで実行されているかどうかを返します。
     *
     * @final
     * @access public
     * @static
     * @return boolean アクションチェインで実行されている場合は、true。そうでない場合は、false;
     */
    final public static function isActionChain()
    {
        return self::$_is_action_chain;
    }
    /* ----------------------------------------- */

    /**
     * +-- 他のアクションに処理を明け渡す
     *
     * @final
     * @param string $action アクション名
     * @param string $module モジュール名 OPTIONAL:NULL
     * @return boolean
     */
    final public static function forward($action, $module = NULL)
    {
        $cpm = EnviRequest::$_module_name;
        $cpa = EnviRequest::$_action_name;
        if ($module !== NULL) {
            EnviRequest::$_module_name = $module;
        }

        //Action
        EnviRequest::$_action_name = $action;
        //実行
        Envi::singleton()->_run();
        //戻す
        EnviRequest::$_module_name = $cpm;
        EnviRequest::$_action_name = $cpa;
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- 他のURLにリダイレクトする
     *
     * 第二引数の、$dieは、trueで中断、falseで続行します。
     * defaultはtrueとなります。
     *
     * @final
     * @access public
     * @static
     * @param string $url リダイレクトするURL
     * @param boolean $die リダイレクトヘッダ出力後の処理を中断するかどうか。 OPTIONAL:true
     * @return void
     */
    final public static function redirect($url, $die = true)
    {
        header('location:'.$url);
        ob_clean();
        ob_start();
        if ($die) {
            ob_clean();
            throw new redirectException($url);
        }
        ob_clean();
    }
    /* ----------------------------------------- */

    /**
     * +-- action名module名を指定してUrlを作成する。
     *
     * @final
     * @access public
     * @static
     * @param string $action アクション名
     * @param string $module モジュール名。省略された場合は、実行中のモジュールになります。OPTIONAL:NULL
     * @param string $url フロントコントローラーへのURL。省略された場合は、実行中のフロントコントローラーになります。OPTIONAL:NULL
     * @return string
     */
    final public static function generateUrl($action, $module = NULL, $url = NULL)
    {
        if ($url === NULL) {
            $url = Envi()->getConfiguration('SYSTEM', 'dispatch_url');
        }

        $i18n = EnviRequest::getI18n();

        if ($module === NULL) {
            $module = EnviRequest::getThisModule();
        }
        return Envi()->getConfiguration('SYSTEM', 'use_i18n') ? $url.'/'.$i18n.'/'.$module.'/'.$action : $url.'/'.$module.'/'.$action;
    }
    /* ----------------------------------------- */

    /**
     * +-- 処理を中断します。
     *
     * Envi内では、exitやdieなどの関数で処理を中断することは推奨されません。
     * Envi::kill()と機能は一緒です。
     *
     * @final
     * @access public
     * @static
     * @param  $kill OPTIONAL:''
     * @param  $is_shutDown OPTIONAL:true
     * @return void
     */
    final public static function kill($kill = '', $is_shutDown = true)
    {
        Envi()->kill($kill, $is_shutDown);
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションを連続して実行する準備をする
     *
     * @final
     * @access public
     * @static
     * @param string $name チェイン名
     * @param string $action アクション名
     * @param string $module モジュール名 OPTIONAL:NULL
     * @param string $data チェイン先に渡すデータ OPTIONAL:NULL
     * @return void
     */
    final public static function setActionChain($name, $action, $module = NULL, $data = NULL)
    {
        self::$_action_chain[$name]      = array($action, $module);
        self::$_action_chain_data[$name] = $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションチェインしたアクションを実行リストから削除する
     *
     * EnviController::setActionChain()したアクションを実行リストから削除します。
     *
     * @final
     * @access public
     * @static
     * @param string $name チェイン名
     * @return void
     */
    final public static function unsetActionChain($name)
    {
        unset(self::$_action_chain[$name]);
        unset(self::$_action_chain_data[$name]);
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションを連続して実行して、出力を受け取る
     *
     * EnviController::setActionChain()したアクションを連続して実行して、その出力の配列を受け取ります。
     *
     * このメソッドは、出力バッファリングを使用しています。
     *
     * @final
     * @access public
     * @static
     * @see setActionChain()
     * @return array
     */
    final public static function go()
    {
        self::$_is_action_chain = true;
        $_attribute     = EnviRequest::getAttributeAll();
        $_error_message = EnviRequest::getErrorsByRef();
        $_error_code    = EnviRequest::getErrorCodesByRef();
        $post_data = $_POST;
        foreach (self::$_action_chain as $key => $value) {
            self::$_action_chain_name = $key;
            if (self::$_action_chain_data[$key] !== NULL) {
                $_POST = array_merge($_POST, self::$_action_chain_data[$key]);
            }
            ob_start();
            self::forward($value[0], $value[1]);
            $res[$key] = ob_get_contents();
            ob_clean();
            EnviRequest::setAttributeAll($_attribute);
            EnviRequest::setErrorsAll($_error_message);
            EnviRequest::setErrorCodesAll($_error_code);
            $_POST = $post_data;
        }

        self::$_action_chain      = array();
        self::$_action_chain_data = array();
        self::$_is_action_chain   = false;
        self::$_action_chain_name = NULL;
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションチェイン中、その実行プロセス名を返す
     *
     * @final
     * @access public
     * @static
     * @return string 実行中のアクションチェイン名
     */
    final public static function getActionChainName()
    {
        return self::$_action_chain_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 404エラーを発行する
     *
     * @final
     * @access      public
     * @static
     * @param string $error_message エラーメッセージ OPTIONAL:''
     * @return      void
     */
    final public static function killBy404Error($error_message = '')
    {
        throw new Envi404Exception($error_message);
    }
    /* ----------------------------------------- */

    /**
     * +-- 403エラーを発行する
     *
     * @final
     * @access      public
     * @static
     * @param string $error_message エラーメッセージ OPTIONAL:''
     * @return      void
     */
    final public static function killBy403Error($error_message = '')
    {
        throw new Envi403Exception($error_message);
    }
    /* ----------------------------------------- */
}
