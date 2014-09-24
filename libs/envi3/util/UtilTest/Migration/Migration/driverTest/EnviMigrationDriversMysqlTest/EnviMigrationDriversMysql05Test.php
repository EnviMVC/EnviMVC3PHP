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
class EnviMigrationDriversMysql05Test extends EnviMigrationDriversMysqlTestBase
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
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_3`');
        self::$EnviDBInstance->getInstance('default_master')->
            query('CREATE TABLE test_table_3 LIKE test_table_2');

        $res = self::$EnviDBInstance->getInstance('default_master')->
            query('ALTER TABLE  `test_table_3` CHANGE  `id`  `id` INT( 11 ) NOT NULL');
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
     * @cover EnviMigrationDriversMysql::addIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addIndexTest1($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` ADD INDEX idx_column_index (`foo`, `goo`)')
            ->once()
            ->andNoBypass();
        $driver->addIndex('test_table_3', array('foo', 'goo'), array('name' => 'idx_column_index'));

    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addIndexTest2($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` ADD INDEX  (`goo`)')
            ->once()
            ->andNoBypass();
        $driver->addIndex('test_table_3', 'goo', array());

    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addIndexTest3($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` ADD UNIQUE uq_column_index (`foo`, `goo`)')
            ->once()
            ->andNoBypass();
        $driver->addIndex('test_table_3', array('foo', 'goo'), array('name' => 'uq_column_index', 'unique' => true));


    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addIndex
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addIndexTest4($driver, $mock)
    {
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_3` ADD UNIQUE uq_hoo_index (`hoo`)')
            ->once()
            ->andNoBypass();
        $driver->addIndex('test_table_3', 'hoo', array('name' => 'uq_hoo_index', 'index_type' => 'unique'));
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
