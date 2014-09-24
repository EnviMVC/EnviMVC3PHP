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
 * @group core
 */
namespace EnviCoreTest{
    class EnviServerStatus
    {
        const DEVELOPER  = 'dev';
        const STAGE      = 'stg';
        const PRODUCTION = 'prod';

        private $_stutus_conf;
        private static  $instance;
        /**
         * +-- コンストラクタ
         *
         * @access private
         * @return void
         */
        private function __construct()
        {
            $this->_stutus_conf = 'test';
        }
        /* ----------------------------------------- */

        /**
         * +-- シングルトン
         *
         * @access public
         * @static
         * @return EnviServerStatus
         */
        public static function &singleton()
        {
            if (!isset(self::$instance)) {
                self::$instance = new EnviServerStatus();
            }
            return self::$instance;
        }
        /* ----------------------------------------- */

        /**
         * +-- サーバーステータスを取得する
         *
         * @access public
         * @return string
         */
        public function getServerStatus()
        {
            return $this->_stutus_conf;
        }
        /* ----------------------------------------- */

    }

    class exception extends \exception
    {

    }



    function EnviServerStatus()
    {
        return EnviServerStatus::singleton();
    }
}
namespace {
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
class EnviCoreTest extends testCaseBase
{
    /**
     * +-- 初期化
     *
     * @access public
     * @return void
     */
    public function initialize()
    {
        $res = file(dirname(__FILE__).'/../../../envi3/Envi.php');
        $res[0] = $res[0].'namespace EnviCoreTest;'."\n";
        foreach ($res as &$row) {
            if (strpos($row, 'require') !== false) {
                $row = '// '.$row;
            }
        }
        unset($row);

        file_put_contents(dirname(__FILE__).'/test/Envi.php', join('', $res));
        require_once dirname(__FILE__).'/test/Envi.php';
        $this->free();
    }
    /* ----------------------------------------- */

    public function parseYmlTest()
    {
        EnviMock::mock('EnviCoreTest\Envi');
    }

}
}