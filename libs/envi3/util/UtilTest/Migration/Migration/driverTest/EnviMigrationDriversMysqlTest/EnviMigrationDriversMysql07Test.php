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

require_once 'EnviMigrationDriversMysqlTestBase.php';

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
class EnviMigrationDriversMysql07Test extends EnviMigrationDriversMysqlTestBase
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
        parent::beforeClass();
        self::$EnviDBInstance = parent::$EnviDBInstance;

    }
    /* ----------------------------------------- */

    /**
     * +-- 初期化
     *
     * @access public
     * @return void
     */
    public function initialize()
    {
        $this->free();
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
        list($driver) = parent::dataProvider();
        $mock = EnviMock::mock('EnviMigrationDriversBase');

        return array($driver, $mock);
    }
    /* ----------------------------------------- */




    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameIndexTest($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE  `test_table_3` DROP INDEX  `uq_column_index` ,ADD INDEX  `uq_columns_index` ( `foo`, `goo` )')
            ->once()
            ->andNoBypass();
        $driver->renameIndex('test_table_3', 'uq_column_index', 'uq_columns_index');
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameIndexTest2($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE  `test_table_3` DROP INDEX  `goo` ,ADD INDEX  `idx_goo` ( `goo` )')
            ->once()
            ->andNoBypass();
        $driver->renameIndex('test_table_3', 'goo', 'idx_goo');
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
