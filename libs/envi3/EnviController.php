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
 * EnviControllerは、ActionController内でアプリケーション自体の振る舞いを制御します。
 *
 * * 処理の中断
 * * 処理の委譲
 * * アクションチェインの実行・制御
 *
 * が、提供する主な機能となります。
 *
 * [アクションチェイン](/c/man/v3/core/action_chain)に関しては、基本リファレンスで解説をしています。
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
     * @doc_ignore
     */
    private function __construct()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションチェインの中かどうかを返す
     *
     * 実行中のアクションコントローラーが、アクションチェインで実行されているかどうかをしらべ、
     * アクションチェインで実行されている場合は、true。そうでない場合は、falseを返します
     *
     * {@example}
     * <?php
     * // アクションチェイン中のAction
     * $res = EnviController::isActionChain();
     * var_dump($res);
     * ?>
     * {/@example}
     * {@example_result}
     * bool(true)
     * {/@example_result}
     *
     * @final
     * @access public
     * @static
     * @return boolean アクションチェイン内かどうか
     */
    final public static function isActionChain()
    {
        return self::$_is_action_chain;
    }
    /* ----------------------------------------- */

    /**
     * +-- 他のアクションに処理を明け渡します
     *
     * 指定された他のアクションに処理を明け渡します。
     * 元のアクションは処理を中断せず、他のアクションを実行後は、元のアクションに処理が戻るので注意して下さい。
     *
     * {@example}
     * <?php
     * $res = EnviController::forward('reference', 'v3');
     * var_dump($res);
     * ?>
     * {/@example}
     * {@example_result}
     * bool(true)
     * {/@example_result}
     *
     * @final
     * @param string $action アクション名
     * @param string $module モジュール名 OPTIONAL:NULL
     * @return boolean 必ずtrueを返す
     * @see EnviRequest::getThisModule()
     * @see EnviRequest::getThisAction()
     */
    final public static function forward($action, $module = NULL)
    {
        $cpm = EnviRequest::$_module_name;
        $cpa = EnviRequest::$_action_name;
        if ($module !== NULL) {
            EnviRequest::$_module_name = $module;
        }

        // Action
        EnviRequest::$_action_name = $action;
        // 実行
        Envi::singleton()->_run();
        // 戻す
        EnviRequest::$_module_name = $cpm;
        EnviRequest::$_action_name = $cpa;
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- 他のURLにリダイレクトします
     *
     *
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * __注意__: $die をtrue にした場合、[EnviController::kill](/c/man/v3/reference/EnviController/class/kill)とは違い、shutdownは実行されないことに注意して下さい。
     *
     * デストラクタは実行されます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     *
     * このメソッドは、例外を使用します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::redirect(EnviController::generateUrl('action', 'module'));
     * } catch (exception $e) {
     *
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * と言ったように、すべての例外を受ける、try句の中では、使用できません。
     * try句の中では下記のように記述することで、使用できます。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::redirect(EnviController::generateUrl('action', 'module'));
     * } catch (redirectException $e) {
     *     throw $e;
     * } catch (exception $e) {
     *     // 何か処理
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
     * __※__ redirectExceptionの最終的な挙動は、フロントコントローラーで操作できます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {@example}
     * <?php
     * EnviController::redirect('reference', 'v3');
     * ?>
     * {/@example}
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
     * action名module名を指定してUrlを作成します。
     * モジュール名が省略された場合は、実行中のモジュールが指定されます。
     *
     * {@example}
     * <?php
     * $res = EnviController::generateUrl('reference', 'v3');
     * echo $res;
     * ?>
     * {/@example}
     * {@example_result}
     * /app.php/module_name/action_name.php
     * {/@example_result}
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
     *
     * EnviPHPで実行されている現在のプロセスをすべて中断します。
     *
     * EnviPHPでは、exitやdieによるスクリプトの停止を認めていません。
     *
     * PHPの仕様上それは単なる規約に過ぎませんが、代わりに、このメソッドを使用することにより、より柔軟なスクリプト中断が可能です。
     *
     * このメソッドは、例外を使用します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::kill('reference', 3);
     * } catch (exception $e) {
     *
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * と言ったように、すべての例外を受ける、try句の中では、使用できません。
     * try句の中では下記のように記述することで、使用できます。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::kill('reference', 3);
     * } catch (killException $e) {
     *     throw $e;
     *
     * } catch (exception $e) {
     *     // 何か処理
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
     * __※__ killExceptionの最終的な挙動は、フロントコントローラーで操作できます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {@example}
     * <?php
     * EnviController::kill('reference', 3);
     * ?>
     * {/@example}
     *
     *
     * @final
     * @access public
     * @static
     * @param string $kill OPTIONAL:''
     * @param boolean $is_shutDown OPTIONAL:true
     * @return void
     * @see Envi::Kill()
     */
    final public static function kill($kill = '', $is_shutDown = true)
    {
        Envi()->kill($kill, $is_shutDown);
    }
    /* ----------------------------------------- */

    /**
     * +-- アクションを連続して実行する準備をする
     *
     * アクションを連続して実行する準備をします。
     * アクションチェイン名が、配列で受け取るときのキーとなる。
     *
     * {@example}
     * <?php
     * EnviController::setActionChain('top_header', 'header', 'common');
     * ?>
     * {/@example}
     *
     * @final
     * @access public
     * @static
     * @param string $name チェイン名
     * @param string $action アクション名
     * @param string $module モジュール名 OPTIONAL:NULL
     * @param string $data チェイン先に渡すデータ OPTIONAL:NULL
     * @return void
     * @see EnviController::go()
     * @see EnviController::getActionChainName()
     * @see EnviController::isActionChain ()
     * @see EnviController::unsetActionChain()
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
     *
     * {@example}
     * <?php
     * EnviController::setActionChain('top_header', 'header', 'common');
     * EnviController::unsetActionChain('top_header');
     * ?>
     * {/@example}
     *
     * @final
     * @access public
     * @static
     * @param string $name チェイン名
     * @return void
     * @see EnviController::go()
     * @see EnviController::getActionChainName()
     * @see EnviController::isActionChain ()
     * @see EnviController::setActionChain()
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
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
     * __※__ このメソッドは、出力バッファリングを使用しています。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {@example}
     * <?php
     * EnviController::setActionChain('top_header', 'header', 'common');
     * $res = EnviController::go();
     * echo $res['top_header'];
     * ?>
     * {/@example}
     *
     * @final
     * @access public
     * @static
     * @return array
     * @see EnviController::getActionChainName()
     * @see EnviController::isActionChain ()
     * @see EnviController::setActionChain()
     * @see EnviController::unsetActionChain()
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
     * 現在実行中のアクションコントローラーが、
     * アクションチェインで実行されている場合、そのアクションチェイン名を返します。
     * そうで無い場合は、単にNULLを返します。
     *
     * {@example}
     * <?php
     * $res = EnviController::getActionChainName();
     * var_dump($res);
     * ?>
     *
     * {/@example}
     *
     * @final
     * @access public
     * @static
     * @return string 実行中のアクションチェイン名
     * @see EnviController::go()
     * @see EnviController::isActionChain ()
     * @see EnviController::setActionChain()
     * @see EnviController::unsetActionChain()
     */
    final public static function getActionChainName()
    {
        return self::$_action_chain_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 404エラーを発行する
     *
     *  404ヘッダを出力し、EnviPHPで実行されている現在のプロセスをすべて中断します。
     *
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * __注意__:[EnviController::kill](/c/man/v3/reference/EnviController/class/kill)とは違い、shutdownは実行されないことに注意して下さい。
     *
     * デストラクタは実行されます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     *
     * EnviPHPでは、exitやdieによるスクリプトの停止を認めていません。
     *
     * PHPの仕様上それは単なる規約に過ぎませんが、代わりに、このメソッドを使用することにより、より柔軟なスクリプト中断が可能です。
     *
     * このメソッドは、例外を使用します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::killBy404Error();
     * } catch (exception $e) {
     *
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * と言ったように、すべての例外を受ける、try句の中では、使用できません。
     * try句の中では下記のように記述することで、使用できます。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::killBy404Error();
     * } catch (Envi404Exception $e) {
     *     throw $e;
     * } catch (exception $e) {
     *     // 何か処理
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
     * __※__ Envi404Exception の最終的な挙動は、フロントコントローラーで操作できます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {@example}
     * <?php
     * EnviController::killBy404Error('not found.');
     * ?>
     * {/@example}
     *
     * @final
     * @access      public
     * @static
     * @param string $error_message エラーメッセージ OPTIONAL:''
     * @return      void
     * @see EnviController::kill()
     * @see EnviController::killBy403Error()
     */
    final public static function killBy404Error($error_message = '')
    {
        throw new Envi404Exception($error_message);
    }
    /* ----------------------------------------- */

    /**
     * +-- 403エラーを発行する
     *
     * 403ヘッダを出力し、EnviPHPで実行されている現在のプロセスをすべて中断します。
     *
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * __注意__:[EnviController::kill](/c/man/v3/reference/EnviController/class/kill)とは違い、shutdownは実行されないことに注意して下さい。
     *
     * デストラクタは実行されます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     *
     * EnviPHPでは、exitやdieによるスクリプトの停止を認めていません。
     *
     * PHPの仕様上それは単なる規約に過ぎませんが、代わりに、このメソッドを使用することにより、より柔軟なスクリプト中断が可能です。
     *
     * このメソッドは、例外を使用します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::killBy403Error();
     * } catch (exception $e) {
     *
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * と言ったように、すべての例外を受ける、try句の中では、使用できません。
     * try句の中では下記のように記述することで、使用できます。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.php_code .code}
     * <?php
     * try {
     *     EnviController::killBy403Error();
     * } catch (Envi403Exception $e) {
     *     throw $e;
     * } catch (exception $e) {
     *     // 何か処理
     * }
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-info}
     * __※__ Envi403Exception の最終的な挙動は、フロントコントローラーで操作できます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     *
     * {@example}
     * <?php
     * EnviController::killBy403Error('access denied');
     * ?>
     * {/@example}
     *
     * @final
     * @access      public
     * @static
     * @param string $error_message エラーメッセージ OPTIONAL:''
     * @return      void
     * @see EnviController::kill()
     * @see EnviController::killBy404Error()
     */
    final public static function killBy403Error($error_message = '')
    {
        throw new Envi403Exception($error_message);
    }
    /* ----------------------------------------- */
}
