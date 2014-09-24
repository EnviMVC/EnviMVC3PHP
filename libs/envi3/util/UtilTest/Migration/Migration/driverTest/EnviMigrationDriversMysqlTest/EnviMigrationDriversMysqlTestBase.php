<?php
/**
 *
 *
 *
 * PHP versions 5
 *
 *

 *
 * @category   テスト
 * @package    テスト
 * @subpackage TestCode
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/c/man/v3/reference
 * @since      File available since Release 1.0.0
 * @doc_ignore
 */

$ds = DIRECTORY_SEPARATOR;
require_once ENVI_BASE_DIR.'vendor'.$ds.'EnviDB.php';
require_once ENVI_BASE_DIR."util{$ds}Migration{$ds}drivers{$ds}".'EnviMigrationDriversBase.php';
require_once ENVI_BASE_DIR."util{$ds}Migration{$ds}drivers{$ds}".'EnviMigrationDriversMysql.php';
require_once ENVI_BASE_DIR."util{$ds}Migration{$ds}".'EnviMigrationBase.php';

/**
 *
 *
 *
 *
 * @category   テスト
 * @package    テスト
 * @subpackage TestCode
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/c/man/v3/reference
 * @since      File available since Release 1.0.0
 */
class EnviMigrationDriversMysqlTestBase extends testCaseBase
{
    public static $EnviDBInstance;
    /**
     * +--
     *
     * @beforeClass
     * @access      public
     * @static
     * @return      void
     */
    public static function beforeClass()
    {
        ob_start();
        include FIXTURE_DIR.'database.yml';
        $buff      = ob_get_contents();
        ob_end_clean();
        $buff = spyc_load($buff);
        self::$EnviDBInstance = new EnviDBInstance($buff['all']);



        $mock = EnviMock::mock('EnviMigrationMock');
        $mock->shouldReceive('DBI')
            ->andReturn(self::$EnviDBInstance->getInstance('default_master'))
            ->shouldReceive('isDryRun')
            ->andReturn(false)
            ;


    }
    /* ----------------------------------------- */

    /**
     * +-- データプロバイダ
     *
     * @access      public
     * @return      void
     */
    public function dataProvider()
    {
        $migration = new EnviMigrationMock;
        $EnviMigrationDriversMysql = new EnviMigrationDriversMysql($migration);
        return array($EnviMigrationDriversMysql);
    }
    /* ----------------------------------------- */


    /**
     * +-- 終了処理
     *
     * @access public
     * @return void
     */
    public function shutdown()
    {
    }

}
