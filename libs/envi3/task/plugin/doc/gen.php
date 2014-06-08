<?php
/**
 *  キャッシュのクリアタスク
 *
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
umask(0);


include_once ENVI_BASE_DIR.'util'.DIRECTORY_SEPARATOR.'EnviDocumenter.php';

$EnviDocumenter = new EnviDocumenter();
$EnviDocumenter->parse(ENVI_BASE_DIR);
