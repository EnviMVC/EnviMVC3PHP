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
class EnviMigrationDriversMysql10Test extends EnviMigrationDriversMysqlTestBase
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
     * @cover EnviMigrationDriversMysql::changeColumnDefault
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnDefaultTest($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE foo foo varchar(200) DEFAULT :default_val ', array('default_val' => 'abcd'))
            ->once()
            ->andNoBypass();
        $driver->changeColumnDefault('test_table_3', 'foo', 'abcd');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumnDefault
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnDefaultTest2($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE test_table_3 CHANGE hoo hoo int(11)  NOT NULL  DEFAULT :default_val ', array('default_val' => '1'))
            ->once()
            ->andNoBypass();
        $driver->changeColumnDefault('test_table_3', 'hoo', '1');
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::changeColumnDefault
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function changeColumnDefaultTest3($driver, $mock)
    {
        $ck = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
        $mock->shouldReceive('query')
            ->never()
            ->andNoBypass();
        try{
            $driver->changeColumnDefault('test_table_3', 'id', '1');
        } catch (exception $e) {

        }
        $this->assertInstanceOf('EnviException', $e);
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_3');
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
