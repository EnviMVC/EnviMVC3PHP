<?php
/**
 * セッションの基底クラス
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

/**
 * セッションの基底クラス
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage EnviUserSession
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 * @abstract
 */
abstract class EnviSessionBase
{

    protected static  $_is_login = '_is_login';
    protected static  $_is_gzip = true;
    protected static  $_session_id = null;

    /**
     * +-- 新しいセッションIDを発行する
     *
     * 新しいセッションIDを発行します。
     * IDの発行のみを行いますが、発行されたIDが一意なモノであるかどうかは、別途確認する必要があります。
     *
     * @access      public
     * @return      string
     */
    public function newSession()
    {
        $session_id = hash('sha512', mt_rand().microtime());
        $str = '';
        $rand = mt_rand(15, 30);
        while ($rand--) {
            $str .= chr(mt_rand(1,126));
        }
        $session_id .= hash('sha512', $str);
        $session_id = substr($session_id, 0, 1).substr(base64_encode(pack('h*', $session_id)), 0, 20).substr($session_id, -1, 1);
        $session_id = str_replace(array('+', '=', '/'), '', $session_id);
        return $session_id;
    }
    /* ----------------------------------------- */


}


interface EnviSessionBaseInterface
{

    /**
     * +-- セッションを開始します
     *
     * @return      void
     */
    public function sessionStart();
    /* ----------------------------------------- */

    /**
     * +-- session_set_save_handler用open
     *
     * @param       string $save_path
     * @param       string $session_name
     * @return      void
     */
    public function open($save_path, $session_name);
    /* ----------------------------------------- */

    /**
     * +-- session_set_save_handler用close
     *
     * @return      void
     */
    public function close();

    /**
     * +-- session_set_save_handler用read
     *
     * @param       string $key
     * @return      mixed
     */
    public function read($key);

    /**
     * +-- session_set_save_handler用write
     *
     * @param       string $key
     * @param       mixed $value
     * @return      void
     */
    public function write($key, $value);

    /**
     * +-- session_set_save_handler用destroy
     *
     * @param       string $key
     * @return      void
     */
    public function destroy($key);

    /**
     * +-- session_set_save_handler用gc
     *
     * @param       integer $maxlifetime
     * @return      void
     */
    public function gc($maxlifetime);

    /**
     * +-- EnviUser::setAttributeの実装を記述します
     *
     * @param       string $key
     * @param       mixed $value
     * @param       integer $expire
     * @return      void
     */
    public function setAttribute($key, $value, $expire = 3600);
    /* ----------------------------------------- */

    /**
     * +-- EnviUser::getAttributeの実装を記述します
     *
     * @param       string $key
     * @return      mixed
     */
    public function getAttribute($key);
    /* ----------------------------------------- */


    /**
     * +-- EnviUser::hasAttributeの実装を記述します
     *
     * @param       string $key
     * @return      boolean
     */
    public function hasAttribute($key);
    /* ----------------------------------------- */

    /**
     * +-- EnviUser::loginの実装を記述します
     *
     * @return      void
     */
    public function login();
    /* ----------------------------------------- */

    /**
     * +-- EnviUser::logoutの実装を記述します
     *
     * @return      void
     */
    public function logout();
    /* ----------------------------------------- */


    /**
     * +-- EnviUser::isLoginの実装を記述します
     *
     * @return      boolean
     */
    public function isLogin();
    /* ----------------------------------------- */

    /**
     * +-- EnviUser::removeAttributeの実装を記述します
     *
     * @param       string $key
     * @return      boolean
     */
    public function removeAttribute($key);
    /* ----------------------------------------- */

    /**
     * +-- EnviUser::cleanAttributesの実装を記述します
     *
     * @return      boolean
     */
    public function cleanAttributes();
    /* ----------------------------------------- */
}