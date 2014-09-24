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
class EnviMigrationDriversMysql09Test extends EnviMigrationDriversMysqlTestBase
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

        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_3`');
        self::$EnviDBInstance->getInstance('default_master')->
            query('CREATE TABLE test_table_3 LIKE test_table_2');

        return array($driver, $mock);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE foo foo varchar(100) DEFAULT NULL')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'foo', 'varchar', array(
            'limit' => 100,
            'null' => true,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest2($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE id id int  NOT NULL  AUTO_INCREMENT')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'id', 'int', array(
            'auto_increment' => true,
            'not_null' => true
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest21($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE id test_table_3 int  NOT NULL  AUTO_INCREMENT')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'id', 'int', array(
            'not_null' => true,
            'default' => 'null',
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest3($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE id test_table_3 int  NOT NULL  DEFAULT "1"')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'id', 'int', array(
            'auto_increment' => false,
            'null' => false,
            'default' => '1',
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest4($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE foo test_table_3 varchar(100) DEFAULT NULL')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'foo', 'varchar', array(
            'limit' => 100,
            'not_null' => false,
            'default' => NULL,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest42($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE foo test_table_3 varchar(150)  NOT NULL ')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'foo', 'varchar', array(
            'limit' => 150,
            'not_null' => true,
            'default' => NULL,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest43($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE foo test_table_3 varchar(150)  NOT NULL ')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'foo', 'varchar', array(
            'limit' => 150,
            'not_null' => true,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest5($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE ioo test_table_3 decimal(11, 2) DEFAULT NULL')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'ioo', 'decimal', array(
            'precision' => 11,
            'scale' => 2,
            'default' => 'NULL',
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');


    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnTest6($driver, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE hoo test_table_3 tinyint  NOT NULL  DEFAULT "1"')
            ->once()
            ->andNoBypass();
        $driver->changeColumn('test_table_3', 'hoo', 'tinyint', array(
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');


    }


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
