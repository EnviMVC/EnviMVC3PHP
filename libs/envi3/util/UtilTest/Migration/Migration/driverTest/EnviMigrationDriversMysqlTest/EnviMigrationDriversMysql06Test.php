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
class EnviMigrationDriversMysql06Test extends EnviMigrationDriversMysqlTestBase
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
     * @cover EnviMigrationDriversMysql::removeIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removeIndexTest($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` DROP INDEX `uq_hoo_index`')
            ->once()
            ->andNoBypass();
        $driver->removeIndex('test_table_3', array('name' => 'uq_hoo_index'));
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::removeIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removePrimaryKeyTest($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` DROP PRIMARY KEY')
            ->once()
            ->andNoBypass();
        $driver->removeIndex('test_table_3', array('primary' => true));
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
