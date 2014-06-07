<?php
/**
 * Memcacheを使用したセッション
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
 * Memcacheを使用したセッション
 *
 * PHP標準の$_SESSIONは使用しません。
 *
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
 */
class EnviMemcacheSession extends EnviSessionBase implements EnviSessionBaseInterface
{

    protected static  $_is_login = '_is_login';
    protected static  $_is_gzip = true;
    protected static  $_session_id = null;


    public function sessionStart()
    {
        $this->sess_base_save_path = $this->_system_conf['SESSION']['sess_base_save_path'];
        $session_name = $this->_system_conf['SESSION']['cookie_name'];
        session_name($session_name);

        $is_new_session = true;
        //セッションIDの正誤性をチェックする。
        if (isset($_COOKIE[$session_name])) {
            $key  = $_COOKIE[$session_name];
            if (EnviMemcache::has($key, 'session', self::$_is_gzip)) {
                $is_new_session = false;
            }
        }
        if ($is_new_session) {
            $i = 0;
            while ($i++ < 30) {
                $key = self::newSession();
                if (!EnviMemcache::has($key, 'session', self::$_is_gzip)) {
                    break;
                }
            }
            if ($i >= 30) {
                throw new EnviException('do not start session');
            }
        }
        //セッション開始
        self::$_session_id = $key;
        EnviMemcache::set($key, serialize(array()), $this->_system_conf['SESSION']['cookie_lifetime'], 'session', self::$_is_gzip);
        setcookie (session_name(), $key, $_SERVER['REQUEST_TIME']+$this->_system_conf['SESSION']['cookie_lifetime']);
    }

    public function open($save_path, $session_name)
    {
        return true;
    }

    public function close()
    {
        return(true);
    }

    public function read($key)
    {
        return unserialize(EnviMemcache::get($key, 'session', self::$_is_gzip));
    }

    public function write($key, $value)
    {
        return EnviMemcache::set($key, serialize($value), $this->_system_conf['SESSION']['cookie_lifetime'], 'session', self::$_is_gzip);
    }

    public function destroy($key)
    {
        return EnviMemcache::delete($key, 'session');
    }


    public function gc($maxlifetime)
    {
        return true;
    }


    public function setAttribute($key, $value, $expire = 3600)
    {
        $key = self::generateKey($key);
        return EnviMemcache::set($key, serialize($value), $expire, 'session', self::$_is_gzip);
    }


    public function getAttribute($key)
    {
        $key = self::generateKey($key);
        return unserialize(EnviMemcache::get($key, 'session', self::$_is_gzip));
    }


    public function hasAttribute($key)
    {
        $key = self::generateKey($key);
        return EnviMemcache::has($key, 'session', self::$_is_gzip);
    }

    public function login()
    {
        $key = self::generateKey(self::$_is_login);
        return EnviMemcache::set($key, serialize(true), $this->_system_conf['SESSION']['cookie_lifetime'], 'session', self::$_is_gzip);
    }
    public function logout()
    {
        $key = self::generateKey(self::$_is_login);
        EnviMemcache::delete($key, 'session');
    }
    public function isLogin()
    {
        $key = self::generateKey(self::$_is_login);
        return EnviMemcache::get($key, 'session', self::$_is_gzip);
    }

    public function removeAttribute($key){
        $key = self::generateKey($key);
        return EnviMemcache::delete($key, 'session');
    }

    public function cleanAttributes()
    {
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