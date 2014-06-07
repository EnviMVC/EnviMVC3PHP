<?php
/**
 * EnviInputFilter基底クラス
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage Filter
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * EnviInputFilter基底クラス
 *
 * @abstract
 * @category   MVC
 * @package    Envi3
 * @subpackage Filter
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
abstract class EnviInputFilterBase
{

    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param
     * @return void
     */
    public function __construct()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- 一番初めに呼ばれる、メソッド
     *
     * falseを返すと、そこで処理が止まります。
     *
     * @access public
     * @return boolean
     */
    public function initialize()
    {
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- フィルタの実行
     *
     * @access public
     * @return void
     */
    public function execute()
    {
    }
    /* ----------------------------------------- */

}