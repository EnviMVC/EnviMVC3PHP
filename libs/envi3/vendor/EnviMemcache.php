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
     * +-- Connection取得
     *
     * @access      public
     * @static
     * @param       string $name コネクションキ－ OPTIONAL:'default'
     * @return      EnviMemcache
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
     * +-- Memcacheにセットする
     *
     * Memcache::set() は、キー key に var という値を 関連付け、memcached サーバに格納します。
     * パラメータ expire は、データの有効期限を秒単位で指定します。
     * もし 0 を指定した場合は その項目が期限切れになることはありません
     * (これは、その項目のデータが memcached サーバ上にずっと残り続けることを保証するものではありません。
     * 他の項目をキャッシュするための場所を確保するためにサーバから 削除されてしまうこともあります)。
     * (zlib を使用して) その場でのデータの圧縮を行いたい場合は、 flag の値として、定数 MEMCACHE_COMPRESSED を指定します。
     *
     * @access      public
     * @static
     * @param       string $key 保存するキー
     * @param       string $var 保存する値
     * @param       integer $expire タイムアウト値 OPTIONAL:3600
     * @param       string $name ネームスペース OPTIONAL:'default'
     * @param       boolean $flag zlib圧縮の有効/無効 OPTIONAL:false
     * @return      boolean
     * @see EnviMemcache::get()
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
    /* ----------------------------------------- */

    /**
     * +-- Memcacheから値を取得する
     *
     * @access      public
     * @static
     * @param       string $key 取得するキー
     * @param       string $name ネームスペース OPTIONAL:'default'
     * @return      mixed
     * @see EnviMemcache::set()
     */
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
    /* ----------------------------------------- */

    /**
     * +-- Memcacheに値があるか確認する
     *
     * @access      public
     * @static
     * @param       string $key 確認するキー
     * @param       string $name ネームスペース OPTIONAL:'default'
     * @return      boolean
     * @see EnviMemcache::set()
     */
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
    /* ----------------------------------------- */

    /**
     * +-- Memcacheから値を削除するキー
     *
     * @access      public
     * @static
     * @param       string $key 削除するキー
     * @param       string $name ネームスペース OPTIONAL:'default'
     * @return      boolean
     * @see EnviMemcache::set()
     */
    public static function delete($key, $name= 'default')
    {
        $con = self::getConnection($name);
        $key = self::$prefix[$name].$key;
        return $con->delete($key) !== FALSE;
    }
    /* ----------------------------------------- */
}
