<?php
/**
 * ユーザークラス
 *
 * EnviUserはユーザー固有のデータを、[SESSION](http://www.php.net/manual/ja/intro.session.php)の機構を利用して、保存する機能です。
 *
 * [EnviUser::setAttribute](/c/man/v3/reference/EnviUser/class/setAttribute)で格納されたデータは、リクエスト間でデータを参照することが出来ます。
 *
 * staticで定義されているため、Envi PHP上のどこからでもアクセスすることが出来ますが、ActionControllerおよび、ViewController以外からの参照はしない方が、エレガントなコードになります。
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviUserSession
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */

/**
 * ユーザークラス
 *
 * Sessionを利用した、ユーザー固有のデータ管理を行います。
 *
 * ユーザークラス機能は、複数回のアクセスを通じて特定のデー タを保持する手段を実現するものです。
 * Envi PHPでは、いくつかの手段で、この方法を実現していますが、基本的な挙動はすべて同じです。
 * Web サイトの訪問者にはセッションIDというセッションIDと呼ばれるユニークなIDが割りつけられます。
 * このIDは、ユーザー側にクッキーとして保存します。
 *
 * 使用できる、セッションの仕組みは現在、
 *
 * * EnviSecureSession
 * * EnviMemcacheSession
 * * EnviApcSession
 * * EnviApcSympleSession
 *
 * の３つとなります。
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviUserSession
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviUser
{

    private static $_system_conf;
    public static $session;


    private static $_is_session_start = false;

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
     * +-- セッションを開始する
     *
     * 通常は明示的に実行する必要はありません。
     *
     * @return void
     */
    public static function sessionStart()
    {
        $session_manager = Envi::singleton()->getConfiguration('SESSION', 'session_manager');
        if (!class_exists($session_manager, false)) {
            $session_manager_path = Envi::singleton()->getConfiguration('SESSION', 'session_manager_path');
            include $session_manager_path;
        }
        self::$session = new $session_manager;
        self::$session->_system_conf = Envi::singleton()->getConfigurationAll();
        self::$session->sessionStart();
        self::$_is_session_start = true;
    }
    /* ----------------------------------------- */

    /**
     * +-- ログイン状態にする
     *
     * ユーザーをログイン状態にします。
     * ここでのログイン状態の有無は、ActionControllerにおける、isSecure()の挙動にひも付いています。
     * isSecure()の返り値が true にした時、もしログイン状態でないのであれば、Envi403Exceptionを発行し、すべての処理を中断します。
     *
     * @return void
     */
    public static function login()
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        self::$session->login();
    }
    /* ----------------------------------------- */

    /**
     * +-- ログアウト状態にする
     *
     * EnviUser::login() でログインした状態にある場合に、ログアウト状態にします。
     * すでにログイン状態にない場合は、特に何もせず、エラーも返しません。
     *
     *
     * @return void
     */
    public static function logout()
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        self::$session->logout();
    }
    /* ----------------------------------------- */

    /**
     * +-- ログイン状態かどうかを確認する
     *
     * EnviUser::login() でログインした状態にあるかどうかを確認します。
     *
     * @return boolean
     */
    public static function isLogin()
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }

        return self::$session->isLogin();
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザーAttributeにデータを格納する
     *
     * 変数をデータ領域にキャッシュします。
     *
     * PHP の他の多くの仕組みと異なり、 EnviUser::setAttribute() を用いて格納された変数は、
     * ユーザー単位でリクエストを超えて（その値がキャッシュから取り除かれるまで）持続します。
     *
     * @param string $name この名前を用いて変数を格納します。$name は ユーザーAttribute内で一意です。そのため、同一の $name で新しい値を格納すると、元の値は上書きされます。
     * @param mixed $value 格納する変数
     * @param int $ttl     EnviMemcacheSessionを選択したときだけ有効です。有効期間。$value は、キャッシュに ttl 秒間だけ格納されます。 ttl が経過すると、格納されている変数は （次のリクエスト時に）キャッシュから削除されます。 ttl が指定されていない（あるいは ttl が 0 の場合）は、 キャッシュから手動で削除される・あるいはキャッシュに存在できなくなる （clear, delete, sesion time out など）まで値が持続します。
     * @return void
     */
    public static function setAttribute($name, $value)
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        $arr = func_get_args();
        return call_user_func_array(array(self::$session, 'setAttribute'), $arr);
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザーAttributeにデータがあるか確認する
     *
     * ユーザーAttributeにデータがセットされており、それが NULL でないことを調べます。
     *
     * @param string $name 存在を確認する$name
     * @return boolean
     */
    public static function hasAttribute($name)
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        $arr = func_get_args();
        return call_user_func_array(array(self::$session, 'hasAttribute'), $arr);
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザーAttributeのデータを削除する
     *
     * EnviUser::setAttribute()したデータを削除します。
     *
     * @param string $name EnviUser::setAttribute()を用いて値を格納する際に 使用された$name
     * @return void
     */
    public static function removeAttribute($name)
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        $arr = func_get_args();
        return call_user_func_array(array(self::$session, 'removeAttribute'), $arr);
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザーAttributeのデータを全て削除する
     *
     * EnviUser::setAttribute()を用いて格納したデータを全て破棄します。
     *
     * @return void
     */
    public static function cleanAttributes()
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        self::$session->cleanAttributes();
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザーAttributeに格納されている値を取得する
     *
     * このメソッドはEnviUser::setAttribute()したデータを取得します。
     *
     * @param string $name 取得するAttribute名
     * @return mixd
     */
    public static function getAttribute($name)
    {
        if (!self::$_is_session_start) {
            self::sessionStart();
        }
        $arr = func_get_args();
        return call_user_func_array(array(self::$session, 'getAttribute'), $arr);
    }
    /* ----------------------------------------- */
}
