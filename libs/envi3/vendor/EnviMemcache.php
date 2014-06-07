<?php
/**
 * Memcache
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Memcache
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */

/**
 * Memcache
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage Memcache
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviMemcache
{
    private static $connection;
    private static $prefix;
    private static $cache;

    /**
     * Connection取得
     *
     */
    public static function getConnection($name = 'default')
    {
        if (isset(self::$connection[$name])) {
            return self::$connection[$name];
        }
        $system_conf = Envi::singleton()->getConfiguration('MEMCACHE');
        self::$connection[$name] = new Memcache;
        self::$connection[$name]->connect($system_conf["{$name}_host"], $system_conf["{$name}_port"]);
        self::$prefix[$name] = $system_conf["{$name}_prefix"];
        return self::$connection[$name];
    }

    /**
     *
     *
     * Memcache::set() は、キー key に var という値を 関連付け、memcached サーバに格納します。
     * パラメータ expire は、データの有効期限を秒単位で指定します。
     * もし 0 を指定した場合は その項目が期限切れになることはありません
     * (これは、その項目のデータが memcached サーバ上にずっと残り続けることを保証するものではありません。
     * 他の項目をキャッシュするための場所を確保するためにサーバから 削除されてしまうこともあります)。
     * (zlib を使用して) その場でのデータの圧縮を行いたい場合は、 flag の値として、定数 MEMCACHE_COMPRESSED を指定します。
     */
    public static function set($key, $var, $expire = 3600, $name= 'default', $flag = false)
    {
        $con = self::getConnection($name);
        $key = self::$prefix[$name].$key;
        self::$cache[$name][$key] = $var;
        if ($flag) {
            $flag = MEMCACHE_COMPRESSED;
        }
        return $con->set($key, $var, $flag, $expire);
    }

    public static function get($key,  $name= 'default')
    {
        $con = self::getConnection($name);
        $key = self::$prefix[$name].$key;

        if (!isset(self::$cache[$name][$key])) {
            $flags = NULL;
            self::$cache[$name][$key] = $con->get($key, $flags);
        }
        return self::$cache[$name][$key];
    }

    public static function has($key, $name= 'default')
    {
        $con = self::getConnection($name);
        $key = self::$prefix[$name].$key;
        if (isset(self::$cache[$name][$key]) && self::$cache[$name][$key] !== FALSE) {
            return true;
        }
        $flags = NULL;
        self::$cache[$name][$key] = $con->get($key, $flags);
        return self::$cache[$name][$key] !== FALSE;
    }

    public static function delete($key, $name= 'default')
    {
        $con = self::getConnection($name);
        $key = self::$prefix[$name].$key;
        return $con->delete($key) !== FALSE;
    }
}
