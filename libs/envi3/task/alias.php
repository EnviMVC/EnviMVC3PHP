<?php
/**
 * コマンドのエイリアス
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
 * @doc_ignore
*/

$alias = array(
    'cc'        => array('clear', 'cache'),
    'app'        => array('init', 'app'),
    'module' => array('init', 'module'),
    'controller' => array('init', 'action'), // symfony風のコマンドエイリアス
    'action' => array('init', 'action'),
    'view' => array('init', 'view'),
    'test' => array('unittest', 'go'),
    'yml' => array('yml', 'test'),
    'scaffold' => array('init', 'scaffold'),
);
