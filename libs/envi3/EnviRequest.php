<?php
/**
 * リクエストを管理するクラス
 *
 * EnviRequestはユーザーからのパラメータの受け渡しと、ActionControllerからViewControllerへのパラメータ、データの受け渡しを行うためのクラスです。
 * setAttributeされた値は、同一リクエスト内であれば、どのオブジェクトでも、どのメソッドでもその値を、getAttribute出来るようになります。
 * staticで定義されているため、Envi PHP上のどこからでもアクセスすることが出来ますが、ActionControllerおよび、ViewController以外からの参照はしない方が、エレガントなコードになります。
 * EnviMVCの設計思想では、EnviRequest::setAttribute()される値は、すべてvalidatorを通した、安全な値であることを保証する必要があります。
 *
 *
 * 提供する機能は主に下記の四種類です。
 *
 *  XXXParameter
 *   * ユーザーからGETやPOSTで渡ってきた値を取得します。
 *   * 通常は、valodatorを通すため、直接参照する機会は少ないかもしれません。
 * * XXXAttribute
 *   * ActionControllerから、ViewControllerへの値の受け渡しをおこないます。
 * *  XXXError
 *   * ActionControllerから、ViewControllerへの値の受け渡しを行う点では、XXXAttributeと同じですが、XXXErrorでは、エラーの情報をViewControllerへと受け渡します。
 *   * validatorを使用した場合は、自動的に値が入ります。
 * * リクエストされたor現在実行中のアクション名やモジュール名を取得する。
 *
 * PHP versions 5
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Request
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 * @subpackage_main
 */


/**
 * リクエストを管理するクラス
 *
 * ユーザーからのパラメータの受け渡しと、ActionControllerからViewControllerへのパラメータの受け渡しを、表します。
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Request
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

    /**
     * EnviRequest::getParameter()、EnviRequest::hasParameter()で使用。POSTデータの取得
     *
     * @var         var_type
     */
    const POST = 1;
    /**
     * EnviRequest::getParameter()、EnviRequest::hasParameter()で使用。GETデータの取得
     *
     * @var         var_type
     */
    const GET  = 2;

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
     * +-- リクエストされたモジュール名を返す
     *
     * リクエストされたモジュール名（つまり最初に実行されたモジュール名）を返します
     *
     * @access public
     * @static
     * @return string リクエストされたモジュール名
     * @see EnviRequest::getRequestAction()
     * @see EnviRequest::getThisAction()
     * @see EnviRequest::getThisModule()
     */
    public static function getRequestModule()
    {
        return self::$_request_module_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- リクエストされたアクション名を返す
     *
     * リクエストされたアクション名（つまり最初に実行されたActionController名）を返します
     *
     * @access public
     * @static
     * @return string リクエストされたアクション名
     * @see EnviRequest::getRequestModule()
     * @see EnviRequest::getThisAction()
     * @see EnviRequest::getThisModule()
     */
    public static function getRequestAction()
    {
        return self::$_request_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 実行中のモジュール名を返す
     *
     * 実行中のモジュール名を返します。
     * 通常は、EnviRequest::getRequestModule()と等価となりますが、
     * ActionChainやforwardでActionを異動した場合は、現在実行中のモジュール名となります。
     *
     * @access public
     * @static
     * @return string 現在実行中のモジュール名
     * @see EnviRequest::getRequestModule()
     * @see EnviRequest::getRequestAction()
     * @see EnviRequest::getThisAction()
     */
    public static function getThisModule()
    {
        return self::$_module_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 現在実行中のアクションコントローラー名を返します
     *
     * 通常は、EnviRequest::getRequestAction()と等価となりますが、
     * ActionChainやforwardでActionを異動した場合は、現在実行中のAction名となります。
     *
     * @access public
     * @static
     * @return string 現在実行中のアクションコントローラー名
     * @see EnviRequest::getRequestModule()
     * @see EnviRequest::getRequestAction()
     * @see EnviRequest::getThisModule()
     */
    public static function getThisAction()
    {
        return self::$_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 現在の言語を返す
     *
     * 国際化機能を使用している場合、現在指定されている言語を返します。
     * 国際化機能を使用していない場合は、デフォルトの言語を返します。
     *
     * @access public
     * @static
     * @return string 現在の言語
     * @see EnviRequest::getRequestModule()
     * @see EnviRequest::getRequestAction()
     * @see EnviRequest::getThisModule()
     * @see EnviRequest::getThisAction()
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
     * @doc_ignore
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
        if (count($exp_pathinfo) && $exp_pathinfo[0] !== '') {
            self::$_request_action_name = preg_replace("/\\.".$_system_conf['SYSTEM']['ext'].'$/', '', array_shift($exp_pathinfo));
        }

        self::$_ext_path_info = $exp_pathinfo;

        self::$_module_name = self::$_request_module_name;
        self::$_action_name = self::$_request_action_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- パースしたPATH_INFOのうち、フレームワークで使用されなかった分を取得します
     *
     * EnviMVCではコントロールにPATH_INFOを使用していますが、フレームワークで使用しなかった分のPATH_INFOを展開済みの配列として取得できます。
     * 単にPATH_INFOを取得したい場合は、スーパーグローバル変数を使用して下さい。
     *
     * @static
     * @access public
     * @return array 展開済みのPATH_INFO
     * @see EnviRequest::getRequestModule()
     * @see EnviRequest::getRequestAction()
     * @see EnviRequest::getI18n()
     */
    public static function getPathInfo()
    {
        return self::$_ext_path_info;
    }
    /* ----------------------------------------- */


    /**
     * +-- データコンテナにデータを保存します
     *
     * データコンテナにデータを保存します。
     * 保存されたデータは、どこからでも取り出すことが出来るようになります。
     *
     * @param string $key Attribute名
     * @param mixd $data 値
     * @return void
     * @see EnviRequest::getAttribute()
     * @see EnviRequest::hasAttribute()
     * @see EnviRequest::removeAttribute()
     * @see EnviUser::setAttribute()
     * @see EnviUser::getAttribute()
     * @see EnviUser::hasAttribute()
     * @see EnviUser::removeAttribute()
     */
    public static function setAttribute($key, $data)
    {
        self::$_attribute[$key] = $data;
    }
    /* ----------------------------------------- */


    /**
     * +-- アトリビューターの値をすべて入れ替えます
     *
     * Fw内部では使用しますが、通常の処理では使用しません。
     *
     * @param mixd $data 値
     * @return void
     * @doc_ignore
     */
    public static function setAttributeAll($data)
    {
        self::$_attribute = $data;
    }
    /* ----------------------------------------- */


    /**
     * +-- データコンテナに保存したデータを取り出します
     *
     * Attribute名を指定して、データコンテナに保存したしたデータを取り出します
     *
     * @param string $key Attribute名
     * @return mixd 保存したデータ
     * @see EnviRequest::setAttribute()
     * @see EnviRequest::hasAttribute()
     * @see EnviRequest::removeAttribute()
     * @see EnviUser::setAttribute()
     * @see EnviUser::getAttribute()
     * @see EnviUser::hasAttribute()
     * @see EnviUser::removeAttribute()
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
     * +-- データコンテナに保存した情報をすべて取り出します
     *
     * setAttributeされた値のすべてを配列で取り出します。
     *
     * @access public
     * @static
     * @return array データコンテナのすべての情報
     * @see EnviRequest::getAttribute()
     */
    public static function getAttributeAll()
    {
        return self::$_attribute;
    }
    /* ----------------------------------------- */

    /**
     * +-- データコンテナに保存されているか確認します
     *
     * データコンテナに保存されているか確認します。
     * データコンテナに保存されているか確認し、
     * 保存されていればtrue,そうで無いならfalseを返します。
     *
     * @access public
     * @static
     * @param string $key 確認するAttribute名
     * @return boolean 保存されていればtrue
     * @see EnviRequest::getAttribute()
     * @see EnviRequest::setAttribute()
     * @see EnviRequest::removeAttribute()
     * @see EnviUser::setAttribute()
     * @see EnviUser::getAttribute()
     * @see EnviUser::hasAttribute()
     * @see EnviUser::removeAttribute()
     */
    public static function hasAttribute($key)
    {
        return isset(self::$_attribute[$key]);
    }
    /* ----------------------------------------- */

    /**
     * +-- データコンテナに保存されているデータを削除します
     *
     * Attribute名を指定して、データコンテナに保存されている、データを削除します。
     *
     * @access public
     * @static
     * @param string $key 削除するAttribute名
     * @return void
     * @see EnviRequest::getAttribute()
     * @see EnviRequest::setAttribute()
     * @see EnviRequest::hasAttribute()
     * @see EnviUser::setAttribute()
     * @see EnviUser::getAttribute()
     * @see EnviUser::hasAttribute()
     * @see EnviUser::removeAttribute()
     */
    public static function removeAttribute($key)
    {
        if (isset(self::$_attribute[$key])) {
            unset(self::$_attribute[$key]);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- データコンテナに保存されているデータを全削除します
     *
     * setAttributeされたすべてのデータを空にします。
     *
     * {@example}
     * <?php
     * EnviRequest::setAttribute('key', 10);
     * var_dump(EnviRequest::getAttribute('key'));
     * EnviRequest::cleanAttributes();
     * var_dump(EnviRequest::getAttribute('key', false));
     * ?>
     * {/@example}
     *
     * {@example_result}
     * int(10)
     * bool(false)
     * {/@example_result}
     * @access public
     * @static
     * @return void
     * @see EnviRequest::getAttribute()
     * @see EnviRequest::setAttribute()
     * @see EnviRequest::hasAttribute()
     */
    public static function cleanAttributes()
    {
        self::$_attribute = array();
    }
    /* ----------------------------------------- */

    /**
     * +-- $_GETもしくは、$_POSTから値を取得します。
     *
     * ユーザーからのリクエストデータを直接取得します。
     * セキュリティの観点から、validatorを通した値を参照する方が望ましいです。
     *
     * @access public
     * @static
     * @param string|int $name 取り出すParameterのkey
     * @param mixed $default_parameter 値が取得できなかった場合のデフォルトの値(OPTIONAL:false)
     * @param int $post_only 取得するhttp methodを指定する。 EnviRequest::POST,EnviRequest::GETが指定できます。bit演算で、両方指定することも出来ます。
     * @return mixed ユーザーからのリクエストデータ
     * @see EnviRequest::hasParameter()
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
     * +-- 存在するパラメーターかどうかを確認します
     *
     * ユーザーからのリクエストデータを直接参照し、ユーザーから該当データが送られてきているかを確認します。
     * 送られてきているなら、true。そうで無いなら、falseを返します。
     *
     * @access public
     * @static
     * @param string|int $name 存在確認を行うParameterのkey
     * @param int $post_only 存在確認を行うhttp methodを指定する。 EnviRequest::POST,EnviRequest::GETが指定できます。bit演算で、両方指定することも出来ます。
     * @return boolean パラメータが存在するならtrue
     * @see EnviRequest::getParameter()
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
     * +-- エラーをセットします。
     *
     * エラー情報をセットします。
     * 主にEnviValidatorクラスからコールされますが、アクションコントローラー内で、EnviValidatorを介さずバリデーションを行った場合は、
     * このメソッドを使用して、エラーコンテナにエラー情報をセットできます。
     * EnviValidatorを使用した場合、エラー名は、フォーム名となるため、特に理由が無い場合はエラー名は、フォーム名と合わせて下さい。
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @param string $validator 引っかかったValidator
     * @param integer $code エラーコード
     * @param string $message エラーメッセージ
     * @return void
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasErrors()
     * @see EnviRequest::hasError()
     */
    public static function setError($name, $validator, $code, $message)
    {
        self::$_error_code[$name][$validator]    = $code;
        self::$_error_message[$name][$validator] = $message;
    }
    /* ----------------------------------------- */


    /**
     * +-- セットされたエラーを全て取得します
     *
     * エラーコンテナに保存されたエラー情報を展開し、配列として返します。
     *
     * @access public
     * @static
     * @return array セットされたすべてのエラー
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasErrors()
     * @see EnviRequest::hasError()
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
     * +-- エラーを全て参照受け渡しで取得します
     *
     * エラメッセージ配列を、参照渡しで取得します。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * __注意__ 参照形式で取得するため、値を書き換えると、エラーメッセージ全体も書き換わります。
     *
     * また、EnviRequest::getErrors ( )とは取得形式が違います。
     *
     * 単純に画面表示用にデータを取得するなら、[EnviRequest::getErrors](/c/man/v3/reference/EnviRequest/class/getErrors)を使用して下さい。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * @access public
     * @static
     * @return array セットされたエラーメッセージ
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasErrors()
     * @see EnviRequest::hasError()
     */
    public static function &getErrorsByRef()
    {
        return self::$_error_message;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーコードを全て参照受け渡しで取得
     *
     * エラーコード配列を、参照渡しで取得します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * __注意__ 参照形式で取得するため、値を書き換えると、エラーコード全体も書き換わります。
     *
     * また、EnviRequest::getErrors ( )とは取得形式が違います。
     *
     * 単純に画面表示用にデータを取得するなら、[EnviRequest::getErrors](/c/man/v3/reference/EnviRequest/class/getErrors)を使用して下さい。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * @access public
     * @static
     * @return array セットされたエラーコード
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasErrors()
     * @see EnviRequest::hasError()
     */
    public static function &getErrorCodesByRef()
    {
        return self::$_error_code;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーメッセージをすべて配列で書き換える
     *
     * エラーメッセージをすべて配列で書き換える
     *
     * @access public
     * @static
     * @params array $data
     * @return void
     * @doc_ignore
     */
    public static function setErrorsAll(array $data)
    {
        self::$_error_message = $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーコードをすべて配列で書き換える
     *
     * エラーコードをすべて配列で書き換える
     *
     * @access public
     * @static
     * @param array $data
     * @return void
     * @doc_ignore
     */
    public static function setErrorCodesAll(array $data)
    {
        self::$_error_code = $data;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラー名を指定してエラー配列を取得
     *
     * エラー名を指定して、エラー配列を取得します。
     * EnviValidatorを使用した場合、エラー名は、フォーム名となります。
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @return array 対応するエラー配列
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::hasErrors()
     * @see EnviRequest::hasError()
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
     * エラーがあるかどうか確認し、あるならtrue、ないならfalseを返します。
     *
     * @access public
     * @static
     * @return boolean エラーがあるかどうか
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasError()
     */
    public static function hasErrors()
    {
        return count(self::$_error_message) > 0;
    }
    /* ----------------------------------------- */

    /**
     * +-- 指定したエラーがあるかどうか確認します
     *
     * 指定したエラーがあるかどうか確認し、あるならtrue、ないならfalseを返します。
     *
     *
     * @access public
     * @static
     * @param string $name エラー名
     * @return boolean 指定したエラーがあるかどうか
     * @see EnviRequest::setError()
     * @see EnviRequest::getErrors()
     * @see EnviRequest::getErrorsByRef()
     * @see EnviRequest::getErrorCodesByRef()
     * @see EnviRequest::getError()
     * @see EnviRequest::hasErrors()
     */
    public static function hasError($name)
    {
        return isset(self::$_error_message[$name]);
    }
    /* ----------------------------------------- */
}
