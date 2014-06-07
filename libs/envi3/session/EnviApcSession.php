<?php
/**
 * APCを使用したSESSION
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
 * APCを使用したSESSION
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
class EnviApcSession extends EnviSessionBase implements EnviSessionBaseInterface
{

    protected static  $_envi_system_value = "__ENVI_USER__";
    protected static  $_attribute = array();
    protected static  $_is_login = '_is_login';

    public $_system_conf;
    public $sess_base_save_path;

    public function open($save_path, $session_name)
    {
        return true;
    }

    public function close()
    {
        return(true);
    }

    public function read($id)
    {
        $session_key = 'sess_'.$this->_system_conf['SESSION']['cookie_name'].$id;
        if (apc_exists($session_key)) {
            return apc_fetch($session_key);
        }
        return '';
    }

    public function write($id, $session_data)
    {
        $session_key = 'sess_'.$this->_system_conf['SESSION']['cookie_name'].$id;
        return apc_store($session_key, $session_data, $this->_system_conf['SESSION']['cookie_lifetime']);
    }

    public function destroy($id)
    {
        $session_key = 'sess_'.$this->_system_conf['SESSION']['cookie_name'].$id;
        setcookie (session_name(), $id, $_SERVER['REQUEST_TIME'] - 3600);
        return @apc_delete($session_key);
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
        ini_set('session.gc_maxlifetime', $this->_system_conf['SESSION']['gc_lifetime']);
        ini_set('session.cookie_lifetime', $this->_system_conf['SESSION']['cookie_lifetime']);
        session_set_save_handler (
            array($this, 'open'),
            array($this,'close'),
            array($this,'read'),
            array($this,'write'),
            array($this,'destroy'),
            array($this,'gc')
        );
        $is_new_session = true;
        //セッションIDの正誤性をチェックする。
        if (isset($_COOKIE[$session_name])) {
            $id  = $_COOKIE[$session_name];
            $session_key = 'sess_'.$this->_system_conf['SESSION']['cookie_name'].$id;
            if (apc_exists($session_key)) {
                $is_new_session = false;
            }
        }

        if ($is_new_session) {
            while(true) {
                $id = $this->newSession();
                if (!apc_exists($id)) {
                    session_id($id);
                    break;
                }
            }
        }
        //セッション開始
        session_start();
    }

    public function getAttribute($key)
    {
        return isset($_SESSION[self::$_envi_system_value][$key]) ? $_SESSION[self::$_envi_system_value][$key] : NULL;
    }
    public function hasAttribute($key)
    {
        return isset($_SESSION[self::$_envi_system_value][$key]);
    }
    public function setAttribute($key, $value)
    {
        $_SESSION[self::$_envi_system_value][$key] = $value;
    }

    public function removeAttribute($key)
    {
        unset($_SESSION[self::$_envi_system_value][$key]);
    }

    public function cleanAttributes()
    {
        $_SESSION[self::$_envi_system_value] = array();
    }

    public function login()
    {
        $_SESSION[self::$_is_login] = true;
    }
    public function logout()
    {
        $_SESSION[self::$_is_login] = false;
    }
    public function isLogin()
    {
        return isset($_SESSION[self::$_is_login]) ? $_SESSION[self::$_is_login] : false;
    }

}
