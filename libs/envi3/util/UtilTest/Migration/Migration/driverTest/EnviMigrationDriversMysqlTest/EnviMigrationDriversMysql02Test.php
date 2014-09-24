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
class EnviMigrationDriversMysql02Test extends EnviMigrationDriversMysqlTestBase
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
        return parent::dataProvider();
    }
    /* ----------------------------------------- */

    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::addTimestamps
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function addTimestampsTest($driver)
    {
        // デフォの値を取得する
        $test_table_1 = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_1');

        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_1` ADD `time_stamp` timestamp NOT NULL')
            ->once()
            ->andNoBypass();
        $driver->addTimestamps('test_table_1');

        // 追加されたか確認
        $test_table_1_new = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_1');
        $test_table_1[] = array (
            'Field' => 'time_stamp',
            'Type' => 'timestamp',
            'Null' => 'NO',
            'Key' => '',
            'Default' => 'CURRENT_TIMESTAMP',
            'Extra' => 'on update CURRENT_TIMESTAMP',
          );
        $this->assertSame($test_table_1_new, $test_table_1);
        $this->assertCount(count($test_table_1), $test_table_1_new);
    }
    /* ----------------------------------------- */


    /**
     * +--
     *
     * @cover EnviMigrationDriversMysql::removeTimestamps
     * @dataProvider    dataProvider
     * @access      public
     * @return      void
     */
    public function removeTimestampsTest($driver)
    {
        // デフォの値を取得する
        $test_table_1 = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_1');
        $mock = EnviMock::mock('EnviMigrationDriversBase');
        $mock->shouldReceive('query')
            ->with('ALTER TABLE `test_table_1` DROP `time_stamp`')
            ->once()
            ->andNoBypass();
        $driver->removeTimestamps('test_table_1', array());
        // 削除されたか確認
        $test_table_1_new = self::$EnviDBInstance->getInstance('default_master')->
            getAll('desc test_table_1');
        array_pop($test_table_1);
        $this->assertSame($test_table_1_new, $test_table_1);
        $this->assertCount(count($test_table_1), $test_table_1_new);
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
