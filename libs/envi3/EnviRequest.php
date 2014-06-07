<?php
/**
 * リクエストを管理するクラス
 *
 * EnviRequestはユーザーからのパラメータの受け渡しと、ActionControllerからViewControllerへのパラメータの受け渡しを行うためのクラスです。
 * staticで定義されているため、Envi PHP上のどこからでもアクセスすることが出来ますが、ActionControllerおよび、ViewController以外からの参照はしない方が、エレガントなコードになります。
 *
 * 主に三種類
 *
 *  XXXParameter
 *   * ユーザーからGETやPOSTで渡ってきた値を取得します。
 *   * 通常は、valodatorを通すため、直接参照する機会は少ないかもしれません。
 * * XXXAttribute
 *   * ActionControllerから、ViewControllerへの値の受け渡しをおこないます。
 * *  XXXError
 *   * ActionControllerから、ViewControllerへの値の受け渡しを行う点では、XXXAttributeと同じですが、XXXErrorでは、エラーの情報をViewControllerへと受け渡します。
 *   * validatorを使用した場合は、自動的に値が入ります。
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
 * リクエストを管理するクラス
 *
 * ユーザーからのパラメータの受け渡しと、ActionControllerからViewControllerへのパラメータの受け渡しを、表します。
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
class EnviRequest
{
    private static $_ext_path_info = array();
    private static $_attribute     = array();

    private static $_error_code    = array();
    private static $_error_message = array();


    private static $_i18n = '';
    private static $_request_module_name = '';
    private static $_request_action_name = '';
    public static $_module_name = '';
    public static $_action_name = '';

    const POST = 1;
    const GET  = 2;

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
     * +-- リクエストされたモジュール名を返す
     *
     * @access public
     * @static
     * @return string
     */
    public static function getRequestModule()
    {
        return self::$_request_module_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- リクエストされたアクション名を返す
     *
     * @access public
     * @static
     * @return string
     */
    public static function getRequestAction()
    {
        return self::$_request_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 実行中のモジュール名を返す
     *
     * @access public
     * @static
     * @return string
     */
    public static function getThisModule()
    {
        return self::$_module_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 実行中のアクション名を返す
     *
     * @access public
     * @static
     * @param
     * @return string
     */
    public static function getThisAction()
    {
        return self::$_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 現在の言語を返す
     *
     * @access public
     * @static
     * @return string
     */
    public static function getI18n()
    {
        return self::$_i18n;
    }
    /* ----------------------------------------- */

    /**
     * +-- 初期化
     *
     * ディスパッチャーが勝手にコールするので、基本呼ばない
     *
     * @access public
     * @static
     * @return void
     */
    public static function initialize()
    {
        $_system_conf = Envi::singleton()->getConfigurationAll();
        // デフォルト指定
        self::$_request_module_name = $_system_conf['SYSTEM']['default_module'];
        self::$_request_action_name = $_system_conf['SYSTEM']['default_action'];
        self::$_i18n = $_system_conf['SYSTEM']['default_i18n'];
        if (!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] === '/') {
            self::$_module_name = self::$_request_module_name;
            self::$_action_name = self::$_request_action_name;
            return;
        }

        $exp_pathinfo = explode('/', $_SERVER['PATH_INFO']);

        // 先頭は空になるから削除
        array_shift($exp_pathinfo);

        // 国際化を使うときははじめのパスインフォがi18n
        if ($_system_conf['SYSTEM']['use_i18n']) {
            self::$_i18n = array_shift($exp_pathinfo);
            if (!isset($_system_conf['I18N'][self::$_i18n])) {
                throw Envi404Exception('404 Error', 20001);
            }
        }

        // モジュール名
        if (count($exp_pathinfo)) {
            self::$_request_module_name = array_shift($exp_pathinfo);
        }
        // アクション名
        if (count($exp_pathinfo)) {
            self::$_request_action_name = preg_replace("/\\.".$_system_conf['SYSTEM']['ext'].'$/', '', array_shift($exp_pathinfo));
        }

        self::$_ext_path_info = $exp_pathinfo;

        self::$_module_name = self::$_request_module_name;
        self::$_action_name = self::$_request_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- パースしたPATH_INFOのうち、フレームワークで使用されなかった分を取得する
     *
     * @static
     * @access public
     * @return array
     */
    public static function getPathInfo()
    {
        return self::$_ext_path_info;
    }
    /* ----------------------------------------- */


    /**
     * +-- データをAttributeします
     *
     * @param string $name Attribute名
     * @param mixd $data 値
     * @return void
     */
    public static function setAttribute($name, $data)
    {
        self::$_attribute[$name] = $data;
    }
    /* ----------------------------------------- */


    public static function setAttributeAll($data)
    {
        self::$_attribute = $data;
    }


    /**
     * Attributeしたデータを取り出します
     *
     * @param string $key Attribute名
     * @return mixd
     */
    public static function getAttribute($key)
    {
        if (is_array(self::$_attribute) ? !count(self::$_attribute) : true) {
            return null;
        }

        if (func_num_args() === 1) {
            return isset(self::$_attribute[$key]) ? self::$_attribute[$key] : null;
        }
        $fga = func_get_args();
        $data = self::$_attribute;
        foreach ($fga as $node) {
            if (is_array($data) && isset($data[$node])) {
                $data = $data[$node];
                continue;
            }
            return null;
        }
        return $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- Attributeした情報をすべて取り出します
     *
     * @access public
     * @static
     * @return array
     */
    public static function getAttributeAll()
    {
        return self::$_attribute;
    }
    /* ----------------------------------------- */

    /**
     * +-- Attributeされているか確認します
     *
     * @access public
     * @static
     * @param string $name Attribute名
     * @return boolean
     */
    public static function hasAttribute($name)
    {
        return isset(self::$_attribute[$name]);
    }
    /* ----------------------------------------- */

    /**
     * +-- Attributeされているデータを削除します
     *
     * @access public
     * @static
     * @param string $name Attribute名
     * @return void
     */
    public static function removeAttribute($name)
    {
        if (isset(self::$_attribute[$name])) {
            unset(self::$_attribute[$name]);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- Attributeされているデータを全削除します
     *
     * @access public
     * @static
     * @param string $name Attribute名
     * @return void
     */
    public static function cleanAttributes()
    {
        self::$_attribute = array();
    }
    /* ----------------------------------------- */

    /**
     * +-- $_GETもしくは、$_POSTから値を取得します。
     *
     * @access public
     * @static
     * @param string|int $name 取り出すParameterのkey
     * @param mixed $default_parameter 値が取得できなかった場合のデフォルトの値(OPTIONAL:false)
     * @param int $post_only 取得するhttp methodを指定する。 EnviRequest::POST,EnviRequest::GETが指定できます。bit演算で、両方指定することも出来ます。
     * @return mixed
     */
    public static function getParameter($name, $default_parameter = false, $post_only = 3)
    {
        if (($post_only === 3 || $post_only === 1) && isset($_POST[$name])) {
            return $_POST[$name] === '' ? $default_parameter : $_POST[$name];
        } elseif (($post_only === 3 || $post_only === 2) && isset($_GET[$name])) {
            return $_GET[$name] === '' ? $default_parameter : $_GET[$name];
        }
        return $default_parameter;
    }

    /**
     * +-- 存在するパラメーターかどうかを確認する
     *
     * @access public
     * @static
     * @param string|int $name 存在確認を行うParameterのkey
     * @param int $post_only 存在確認を行うhttp methodを指定する。 EnviRequest::POST,EnviRequest::GETが指定できます。bit演算で、両方指定することも出来ます。
     * @return boolean
     */
    public static function hasParameter($name, $post_only = 3)
    {
        if ($post_only === 3) {
            return (isset($_POST[$name]) || isset($_GET[$name]));
        } elseif ($post_only == 1) {
            return isset($_POST[$name]);
        } else {
            return isset($_GET[$name]);
        }
    }
    /* ----------------------------------------- */



    /**
     * +-- エラーをセットする
     *
     * 主にEnviValidatorクラスからコールされます。
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @param string $validator 引っかかったValidator
     * @param integer $code エラーコード
     * @param string $message エラーメッセージ
     * @return void
     */
    public static function setError($name, $validator, $code, $message)
    {
        self::$_error_code[$name][$validator]    = $code;
        self::$_error_message[$name][$validator] = $message;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーを全て取得
     *
     * @access public
     * @static
     * @return array
     */
    public static function getErrors()
    {
        $i = 0;
        $res = array();
        $res['message'] = array();
        $res['keys'] = array();
        foreach (self::$_error_message as $key => $values) {
            foreach ($values as $value) {
                $res['message'][$i] = $value;
                $res['keys'][$key][] = $i;
                ++$i;
            }
        }
        $res['count'] = $i;
        return $res;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーを全て参照受け渡しで取得
     *
     * @access public
     * @static
     * @return array
     */
    public static function &getErrorsByRef()
    {
        return self::$_error_message;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーコードを全て参照受け渡しで取得
     *
     * @access public
     * @static
     * @return array
     */
    public static function &getErrorCodesByRef()
    {
        return self::$_error_code;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーメッセージをすべて配列で書き換える
     *
     * @access public
     * @static
     * @params array $data
     * @return void
     */
    public static function setErrorsAll(array $data)
    {
        self::$_error_message = $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーコードをすべて配列で書き換える
     *
     * @access public
     * @static
     * @param array $data
     * @return void
     */
    public static function setErrorCodesAll(array $data)
    {
        self::$_error_code = $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーを指定して取得
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @return array
     */
    public static function getError($name)
    {
        $code = isset(self::$_error_code[$name]) ? self::$_error_code[$name] : 0;
        return isset(self::$_error_message[$name]) ? array('message' => self::$_error_message[$name], 'code' => $code) : null;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーがあるかどうか確認します
     *
     * @access public
     * @static
     * @return boolean
     */
    public static function hasErrors()
    {
        return count(self::$_error_message) > 0;
    }
    /* ----------------------------------------- */

    /**
     * +-- 指定したエラーがあるかどうか確認します
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @return boolean
     */
    public static function hasError($name)
    {
        return isset(self::$_error_message[$name]);
    }
    /* ----------------------------------------- */
}