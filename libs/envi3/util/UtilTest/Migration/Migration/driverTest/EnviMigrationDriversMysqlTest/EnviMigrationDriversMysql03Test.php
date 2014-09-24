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
class EnviMigrationDriversMysql03Test extends EnviMigrationDriversMysqlTestBase
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
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_2`');
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_3`');
        self::$EnviDBInstance->getInstance('default_master')->query('CREATE TABLE `test_table_2` (`aaa` varchar(128)) ENGINE=InnoDB');
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

        $aaa = array (
            'Field' => 'aaa',
            'Type' => 'varchar(128)',
            'Null' => 'YES',
            'Key' => '',
            'Default' => NULL,
            'Extra' => '',
        );

        $foo = array (
            'Field' => 'foo',
            'Type' => 'varchar(200)',
            'Null' => 'YES',
            'Key' => '',
            'Default' => NULL,
            'Extra' => '',
        );

        $hoo = array (
            'Field' => 'hoo',
            'Type' => 'int(11)',
            'Null' => 'NO',
            'Key' => '',
            'Default' => '1',
            'Extra' => '',
        );
        $goo = array (
            'Field' => 'goo',
            'Type' => 'decimal(4,1)',
            'Null' => 'YES',
            'Key' => '',
            'Default' => NULL,
            'Extra' => '',
        );
        $ioo = array (
            'Field' => 'ioo',
            'Type' => 'decimal(10,0)',
            'Null' => 'YES',
            'Key' => '',
            'Default' => NULL,
            'Extra' => '',
        );
        $id = array (
            'Field' => 'id',
            'Type' => 'int(11)',
            'Null' => 'NO',
            'Key' => 'PRI',
            'Default' => NULL,
            'Extra' => 'auto_increment',
        );
        $id2 = array (
            'Field' => 'id',
            'Type' => 'int(11)',
            'Null' => 'NO',
            'Key' => 'PRI',
            'Default' => NULL,
            'Extra' => '',
        );

        $mock = EnviMock::mock('EnviMigrationDriversBase');
        return array($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2);

    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest1($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        // 下準備がOKかどうか
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');
        $this->assertCount(1, $res);
        $this->assertSame($aaa, $res[0]);
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest2($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->addColumn('test_table_2', 'foo', 'varchar', array(
            'limit' => 200,
            'null' => true)
            );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(2, $res);
        $this->assertSame($aaa, $res[0]);
        $this->assertSame($foo, $res[1]);
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest3($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();

        $driver->addColumn('test_table_2', 'hoo', 'int', array(
            'after' => 'aaa',
            'default' => '1',
            'null' => false)
            );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(3, $res);
        $this->assertSame($aaa, $res[0]);
        $this->assertSame($hoo, $res[1]);
        $this->assertSame($foo, $res[2]);

    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest4($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();

        $driver->addColumn('test_table_2', 'goo', 'decimal', array(
            'first' => false,
            'default' => 'NULL',
            'precision' => '4',
            'scale' => '1',
            'not_null' => false,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(4, $res);
        $this->assertSame($aaa, $res[0]);
        $this->assertSame($hoo, $res[1]);
        $this->assertSame($foo, $res[2]);
        $this->assertSame($goo, $res[3]);

    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest5($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();

        $driver->addColumn('test_table_2', 'ioo', 'decimal', array(
            'first' => false,
            'default' => NULL,
            'precision' => '4',
            'not_null'    => false,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(5, $res);
        $this->assertSame($aaa, $res[0]);
        $this->assertSame($hoo, $res[1]);
        $this->assertSame($foo, $res[2]);
        $this->assertSame($goo, $res[3]);
        $this->assertSame($ioo, $res[4]);

    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest6($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();

        $driver->addColumn('test_table_2', 'id', 'int', array(
            'first'          => true,
            'not_null'       => true,
            'primary' => true,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(6, $res);
        $this->assertSame($id2, $res[0]);
        $this->assertSame($aaa, $res[1]);
        $this->assertSame($hoo, $res[2]);
        $this->assertSame($foo, $res[3]);
        $this->assertSame($goo, $res[4]);
        $this->assertSame($ioo, $res[5]);
    }


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::addColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addColumnTest7($driver, $mock, $aaa, $foo, $hoo, $goo, $ioo, $id, $id2)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        // idを作り直すため削除
        $res = self::$EnviDBInstance->getInstance('default_master')->
            query('ALTER TABLE test_table_2 DROP id');


        $driver->addColumn('test_table_2', 'id', 'int', array(
            'first'          => true,
            'not_null'       => true,
            'auto_increment' => true,
            )
        );
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_2');

        $this->assertCount(6, $res);
        $this->assertSame($id,  $res[0]);
        $this->assertSame($aaa, $res[1]);
        $this->assertSame($hoo, $res[2]);
        $this->assertSame($foo, $res[3]);
        $this->assertSame($goo, $res[4]);
        $this->assertSame($ioo, $res[5]);
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
