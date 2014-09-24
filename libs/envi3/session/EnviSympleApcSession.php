<?php
/**
 * APCを使用したSESSION
 *
 * PHP versions 5
 *
 *
 * @category   EnviMVC拡張
 * @package    ユーザーセッション
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
 * APCを使用したSESSION
 *
 * @category   EnviMVC拡張
 * @package    ユーザーセッション
 * @subpackage EnviUserSession
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.3.2
 */
class EnviSympleApcSession extends EnviSessionBase implements EnviSessionInterface
{

    protected static  $_envi_system_value = "__ENVI_USER__";
    protected static  $_attribute = array();
    protected static  $_is_login = '_is_login';

    protected static  $_session_id = null;

    public $_system_conf;
    public $sess_base_save_path;

    public function open($save_path, $session_name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return true;
    }

    public function write($id, $session_data)
    {
        return true;
    }

    public function destroy($id)
    {
        return true;
    }


    public function gc($maxlifetime)
    {
        // apc側のGCに任せる
        return true;
    }


    public function sessionStart()
    {
        $this->sess_base_save_path = $this->_system_conf['SESSION']['sess_base_save_path'];
        $session_name = $this->_system_conf['SESSION']['cookie_name'];
        session_name($session_name);

        $is_new_session = true;
        //セッションIDの正誤性をチェックする。
        if (isset($_COOKIE[$session_name])) {
            $key  = $_COOKIE[$session_name];
            if (apc_fetch($key) === $key) {
                $is_new_session = false;
            }
        }
        if ($is_new_session) {
            $i = 0;
            while ($i++ < 30) {
                $key = self::newSession();
                if (!apc_fetch($key)) {
                    break;
                }
            }
            if ($i >= 30) {
                throw new EnviException('do not start session');
            }
        }
        //セッション開始
        self::$_session_id = $key;

        apc_store($key, $key, $this->_system_conf['SESSION']['cookie_lifetime']);
        setcookie(session_name(), $key, $_SERVER['REQUEST_TIME']+$this->_system_conf['SESSION']['cookie_lifetime'], '/');
    }

    public function getAttribute($key)
    {
        $session_key = $this->generateKey($key);
        if (apc_exists($session_key)) {
            return apc_fetch($session_key);
        }
        return NULL;
    }
    public function hasAttribute($key)
    {
        $session_key = $this->generateKey($key);
        return apc_exists($session_key);
    }
    public function setAttribute($key, $value, $ttl = false)
    {
        $session_key = $this->generateKey($key);
        apc_store($session_key, $value, is_numeric($ttl) ? $ttl : $this->_system_conf['SESSION']['cookie_lifetime']);
    }

    public function removeAttribute($key)
    {
        $session_key = $this->generateKey($key);
        return @apc_delete($session_key);
    }

    public function cleanAttributes()
    {
        $session_name = $this->_system_conf['SESSION']['cookie_name'];
        setcookie ($session_name, '___', $_SERVER['REQUEST_TIME'] - 3600);
        return @apc_delete(self::$_session_id);
    }

    public function login()
    {
        $session_key = $this->generateKey(self::$_is_login);
        apc_store($session_key, true, $this->_system_conf['SESSION']['cookie_lifetime']);
    }
    public function logout()
    {
        $session_key = $this->generateKey(self::$_is_login);
        return @apc_delete($session_key);
    }
    public function isLogin()
    {
        $session_key = $this->generateKey(self::$_is_login);
        return apc_exists($session_key);
    }

    public static function generateKey($key)
    {
        static $key_gen;
        if (!isset($key_gen)) {
            $key_gen = self::$_session_id;
        }
        return $key_gen.'-'.$key;
    }
}
