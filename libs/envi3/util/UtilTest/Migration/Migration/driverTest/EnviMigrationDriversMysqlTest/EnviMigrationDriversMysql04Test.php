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
class EnviMigrationDriversMysql04Test extends EnviMigrationDriversMysqlTestBase
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

        // テストデータ
        $res = self::$EnviDBInstance->getInstance('default_master')->
            query('CREATE TABLE test_table_3 LIKE test_table_2');

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
        $ck_data = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');

        $mock = EnviMock::mock('EnviMigrationDriversBase');
        list($driver) = parent::dataProvider();
        return array($driver, $ck_data, $mock);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest1($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[0]['Field'],  $ck_data[0]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[0]['Field'] = $ck_data[0]['Field'].'2';
        $this->assertSame($ck_data[0], $res[0]);



    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest2($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[1]['Field'],  $ck_data[1]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[1]['Field'] = $ck_data[1]['Field'].'2';
        $this->assertSame($ck_data[1], $res[1]);



    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest3($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[2]['Field'],  $ck_data[2]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[2]['Field'] = $ck_data[2]['Field'].'2';
        $this->assertSame($ck_data[2], $res[2]);



    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest4($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[3]['Field'],  $ck_data[3]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[3]['Field'] = $ck_data[3]['Field'].'2';
        $this->assertSame($ck_data[3], $res[3]);


    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest5($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[4]['Field'],  $ck_data[4]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[4]['Field'] = $ck_data[4]['Field'].'2';
        $this->assertSame($ck_data[4], $res[4]);


    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest6($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[5]['Field'],  $ck_data[5]['Field'].'2');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[5]['Field'] = $ck_data[5]['Field'].'2';
        $this->assertSame($ck_data[5], $res[5]);

    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::renameColumn
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameColumnTest7($driver, $ck_data, $mock)
    {
        // テスト開始
        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $res = self::$EnviDBInstance->getInstance('default_master')->
            query('ALTER TABLE test_table_3 DROP id2');

        $driver->addColumn('test_table_3', 'id', 'int', array(
            'not_null'       => true,
            'primary'       => true,
            'first'        => true,
            )
        );
        $ck_data = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');

        $mock->shouldReceive('query')
            ->once()
            ->andNoBypass();
        $driver->renameColumn('test_table_3', $ck_data[0]['Field'],  $ck_data[0]['Field'].'2');

        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $ck_data[0]['Field'] = $ck_data[0]['Field'].'2';
        $this->assertSame($ck_data[0], $res[0]);

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
