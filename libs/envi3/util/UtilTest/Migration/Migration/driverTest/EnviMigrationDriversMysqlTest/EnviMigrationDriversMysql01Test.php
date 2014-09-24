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
class EnviMigrationDriversMysql01Test extends EnviMigrationDriversMysqlTestBase
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

        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `test_table_1`');
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `foo`');
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `bar`');
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `fooo`');
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
        return parent::dataProvider();
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::createTable
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function createTableTestTable1Test($driver)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('CREATE TABLE `test_table_1` (
`id` integer NOT NULL AUTO_INCREMENT ,
`subject` varchar(128) NOT NULL ,
`email` varchar(128) NOT NULL ,
`url` varchar(128) ,
`nickname` varchar(128) ,
`scale` decimal(6, 2) NOT NULL ,
`delete_flag` tinyint NOT NULL DEFAULT "0" ,
PRIMARY KEY (id),
UNIQUE KEY `uq_email` (email),
KEY `idx_subject` (subject)) ENGINE=MyISAM;
')
            ->once()
            ->andNoBypass();
        $driver->createTable('test_table_1', array(
            'engine' => 'MyISAM',
            'schema' => array(
                'id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'auto_increment' => true,
                    'not_null' => true,
                    '' => '',
                ),
                'subject' => array(
                    'type' => 'varchar',
                    'not_null' => true,
                    'limit' => 128,
                    'index' => true,

                ),
                'email' => array(
                    'type' => 'varchar(128)',
                    'unique' => true,
                    'not_null' => true,
                ),
                'url' => array(
                    'type' => 'varchar(128)',
                ),
                'nickname' => array(
                    'type' => 'varchar(128)',
                    'null' => true,
                ),
                'scale' => array(
                    'type' => 'decimal',
                    'null' => false,
                    'precision' => '6',
                    'scale' => '2',
                ),
                'delete_flag' => array(
                    'type' => 'tinyint',
                    'not_null' => true,
                    'default' => 0,
                ),
            )
        ));
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @depend      createTableTestTable1Test
     * @test
     * @access      public
     * @return      void
     */
    public function createTableTestTable1Check()
    {
        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_1');
        $check = array (
            0 =>
            array (
                'Field' => 'id',
                'Type' => 'int(11)',
                'Null' => 'NO',
                'Key' => 'PRI',
                'Default' => NULL,
                'Extra' => 'auto_increment',
            ),
            1 =>
            array (
                'Field' => 'subject',
                'Type' => 'varchar(128)',
                'Null' => 'NO',
                'Key' => 'MUL',
                'Default' => NULL,
                'Extra' => '',
            ),
            2 =>
            array (
                'Field' => 'email',
                'Type' => 'varchar(128)',
                'Null' => 'NO',
                'Key' => 'UNI',
                'Default' => NULL,
                'Extra' => '',
            ),
            3 =>
            array (
                'Field' => 'url',
                'Type' => 'varchar(128)',
                'Null' => 'YES',
                'Key' => '',
                'Default' => NULL,
                'Extra' => '',
            ),
            4 =>
            array (
                'Field' => 'nickname',
                'Type' => 'varchar(128)',
                'Null' => 'YES',
                'Key' => '',
                'Default' => NULL,
                'Extra' => '',
            ),
            5 =>
            array (
                'Field' => 'scale',
                'Type' => 'decimal(6,2)',
                'Null' => 'YES',
                'Key' => '',
                'Default' => NULL,
                'Extra' => '',
            ),
            6 =>
            array (
                'Field' => 'delete_flag',
                'Type' => 'tinyint(4)',
                'Null' => 'NO',
                'Key' => '',
                'Default' => '0',
                'Extra' => '',
            ),
        );
        $this->assertCount(7, $res);
        $this->assertSame($check, $res);
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::createTable
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function createTableFooForceTest($driver)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->withByTimes(1, 'DROP TABLE IF EXISTS `foo`')
            ->withByTimes(2, 'CREATE TABLE `foo` (
`id` integer NOT NULL AUTO_INCREMENT ,
`subject` varchar(128) NOT NULL ,
`email` varchar(128) NOT NULL ,
`url` varchar(128) DEFAULT NULL ,
`nickname` varchar(128) ,
PRIMARY KEY (id),
UNIQUE KEY `uq_email` (email),
KEY `idx_subject` (subject)) ENGINE=InnoDB;
')
            ->twice()
            ->andNoBypass();
        $driver->createTable('foo', array(
            'force'  => true,
            'schema' => array(
                'id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'auto_increment' => true,
                    'not_null' => true,
                    '' => '',
                ),
                'subject' => array(
                    'type' => 'varchar',
                    'not_null' => true,
                    'limit' => 128,
                    'index' => true,
                    'precision' => '1',
                ),
                'email' => array(
                    'type' => 'varchar(128)',
                    'unique' => true,
                    'not_null' => true,
                ),
                'url' => array(
                    'type' => 'varchar(128)',
                    'scale' => '1',
                    'default' => 'NULL',
                ),
                'nickname' => array(
                    'type' => 'varchar(128)',
                    'null' => true,
                ),
            )
        ));


        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc foo');
        $this->assertCount(5, $res);
    }
    /* ----------------------------------------- */



    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::createTable
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function createTableBarForceTest($driver)
    {
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->withByTimes(1, 'DROP TABLE IF EXISTS `bar`')
            ->withByTimes(2, 'CREATE TABLE `bar` (
`id` integer NOT NULL AUTO_INCREMENT ,
`subject` varchar(128) NOT NULL ,
`email` varchar(128) NOT NULL ,
`url` varchar(128) DEFAULT NULL ,
`nickname` varchar(128) ,
PRIMARY KEY (id),
UNIQUE KEY `uq_email` (email),
KEY `idx_subject` (subject)) ENGINE=InnoDB;
')
            ->twice()
            ->andNoBypass();
        $driver->createTable('bar', array(
            'force'  => true,
            'schema' => array(
                'id' => array(
                    'type' => 'integer',
                    'primary' => true,
                    'auto_increment' => true,
                    'not_null' => true,
                    '' => '',
                ),
                'subject' => array(
                    'type' => 'varchar',
                    'not_null' => true,
                    'limit' => 128,
                    'index' => true,
                    'precision' => '1',
                ),
                'email' => array(
                    'type' => 'varchar(128)',
                    'unique' => true,
                    'not_null' => true,
                ),
                'url' => array(
                    'type' => 'varchar(128)',
                    'scale' => '1',
                    'default' => 'NULL',
                ),
                'nickname' => array(
                    'type' => 'varchar(128)',
                    'null' => true,
                ),
            )
        ));


        $res = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc bar');
        $this->assertCount(5, $res);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::renameTable
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function renameTableTest($driver)
    {
        // 元々無いことを確認する
        $e = null;
        try {
            $foo = self::$EnviDBInstance->getInstance('default_master')->
                getAll('desc fooo');
        } catch (exception $e) {

        }
        $this->assertInstanceOf('PDOException', $e);


        $foo = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc foo');
        $this->assertCount(5, $foo);
        $mock = EnviMock::mock('EnviMigrationDriversBase');

        $mock->shouldReceive('query')
            ->with('RENAME TABLE `foo` TO `fooo`')
            ->once()
            ->andNoBypass();
        $driver->renameTable('foo', 'fooo');
        $fooo = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc fooo');
        $this->assertCount(5, $fooo);
        $this->assertSame($foo, $fooo);

        // 無いことを確認
        $e = null;
        try {
            $foo = self::$EnviDBInstance->getInstance('default_master')->
                getAll('desc foo');
        } catch (exception $e) {
        }
        $this->assertInstanceOf('PDOException', $e);
    }
    /* ----------------------------------------- */


    public function dropTableDataProvider()
    {
        self::$EnviDBInstance->getInstance('default_master')->query('DROP TABLE IF EXISTS `drop_table_test`');
        self::$EnviDBInstance->getInstance('default_master')->
            query('CREATE TABLE drop_table_test LIKE fooo');
        return self::dataProvider();
    }

    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::dropTable
     * @dataProvider    dropTableDataProvider
     * @access      public
     * @return      void
     */
    public function dropTableTest($driver)
    {
        $fooo = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc drop_table_test');
        $this->assertArray($fooo);
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('DROP TABLE `drop_table_test`')
            ->once()
            ->andNoBypass();
        $driver->dropTable('drop_table_test', array());

        // 無いことを確認する
        $e = null;
        try {
            $foo = self::$EnviDBInstance->getInstance('default_master')->
                getAll('desc drop_table_test');
        } catch (exception $e) {

        }
        $this->assertInstanceOf('PDOException', $e);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::dropTable
     * @dataProvider    dropTableDataProvider
     * @access      public
     * @return      void
     */
    public function dropTableForceTrueTest($driver)
    {

        $fooo = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc drop_table_test');
        $this->assertArray($fooo);
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('DROP TABLE IF EXISTS `drop_table_test`')
            ->once()
            ->andNoBypass();
        $driver->dropTable('drop_table_test', array('force' => true));

        // 無いことを確認する
        $e = null;
        try {
            $foo = self::$EnviDBInstance->getInstance('default_master')->
                getAll('desc drop_table_test');
        } catch (exception $e) {

        }
        $this->assertInstanceOf('PDOException', $e);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @test
     * @cover EnviMigrationDriversMysql::dropTable
     * @dataProvider    dropTableDataProvider
     * @access      public
     * @return      void
     */
    public function dropTableForceFalseTest($driver)
    {
        $fooo = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc drop_table_test');
        $this->assertArray($fooo);
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('DROP TABLE `drop_table_test`')
            ->once()
            ->andNoBypass();
        $driver->dropTable('drop_table_test', array('force' => false));

        // 無いことを確認する
        $e = null;
        try {
            $foo = self::$EnviDBInstance->getInstance('default_master')->
                getAll('desc drop_table_test');
        } catch (exception $e) {

        }
        $this->assertInstanceOf('PDOException', $e);
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
