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
class EnviMigrationDriversMysql08Test extends EnviMigrationDriversMysqlTestBase
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
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_3`');
        self::$EnviDBInstance->getInstance('default_master')->
            query('CREATE TABLE test_table_3 LIKE test_table_2');
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
     * @cover EnviMigrationDriversMysql::removeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removeColumnTest($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` DROP `goo`')
            ->once()
            ->andNoBypass();
        $driver->removeColumn('test_table_3', 'goo');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $this->assertCount(count($ck) - 1, $res);
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::removeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removeColumnsTest($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3`  DROP `foo`, DROP `hoo`')
            ->once()
            ->andNoBypass();
        $driver->removeColumn('test_table_3', array('foo', 'hoo'));
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $this->assertCount(count($ck) - 2, $res);
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::removeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removeColumnsTest2($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3`  DROP `id`, DROP `foo`, DROP `hoo`, DROP `goo`')
            ->once()
            ->andNoBypass();
        $driver->removeColumn('test_table_3', array('id', 'foo', 'hoo', 'goo'));
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $this->assertCount(count($ck) - 4, $res);
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
