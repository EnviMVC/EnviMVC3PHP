<?php
/**
 * Mathクラス
 *
 * ActionControllerから、全て静的にコールされます。
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
 * Mathクラス
 *
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
class EnviMath
{
    /**
     * +-- 端数の切り上げ
     *
     * 端数の切り上げ
     *
     * @access public
     * @static
     * @param float $value
     * @return float
     */
    public static function ceil($value)
    {
        $value = ((string)$value);
        return ceil($value);
    }
    /* ----------------------------------------- */

    /**
     * +-- 端数の切り捨て
     *
     * @access public
     * @static
     * @param float $value
     * @return float
     */
    public static function floor($value)
    {
        $value = ((string)$value);
        return floor($value);
    }
    /* ----------------------------------------- */


    /**
     * +-- 浮動小数点数を丸める
     *
     * @access public
     * @static
     * @param float $value
     * @param integer $precision OPTIONAL:0
     * @param integer $mode OPTIONAL:PHP_ROUND_HALF_UP
     * @return float
     */
    public static function round($value, $precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        $value = ((string)$value);
        return round($value, $precision, $mode);
    }
    /* ----------------------------------------- */
}
