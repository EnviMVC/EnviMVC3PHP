<?php
/**
 * ビュー基底クラス
 *
 * ビューを書くときは必ずこのクラスを継承すること。
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage ViewController
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */


/**
 * ビュー基底クラス
 *
 * ビューを書くときは必ずこのクラスを継承すること。
 *
 * @abstract
 * @category   MVC
 * @package    Envi3
 * @subpackage ViewController
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
abstract class EnviViewBase
{
    public $renderer;


    public function __construct()
    {
    }

    /**
     * @abstract
     */
    public function initialize()
    {
        return true;
    }

    /**
     * @abstract
     */
    public function execute()
    {
        return true;
    }


    /**
     * @abstract
     */
    public function setRenderer()
    {
        $renderer_path = Envi()->getConfiguration('SYSTEM', 'renderer');
        if (!class_exists($renderer_class, false)) {
            include $renderer_path;
        }
        $renderer_class = substr(basename($renderer_path), 0, -4);
        $this->renderer = new $renderer_class();
    }

    /**
     * @abstract
     */
    public function shutdown()
    {
        return true;
    }
}
