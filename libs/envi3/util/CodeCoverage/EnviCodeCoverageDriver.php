<?php
/**
 *
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviCodeCoverage
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release v3.3.3.5
 */

/**
 *
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviCodeCoverage
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release v3.3.3.5
 */
class EnviCodeCoverageDriver
{
    private $filter;
    private $code_coverage;
    private static $driver_start = false;

    /**
     * +-- クラスのビルダ
     *
     * @access      public
     * @static
     * @param       EnviCodeCoverage $code_coverage
     * @return      EnviCodeCoverageDriver
     */
    public static function factory(EnviCodeCoverage $code_coverage)
    {
        $obj = new EnviCodeCoverageDriver;
        $obj->code_coverage = $code_coverage;
        return $obj;
    }
    /* ----------------------------------------- */

    /**
     * +-- code_coverage開始
     *
     * @access      public
     * @return      void
     */
    public function start()
    {
        if (self::$driver_start) {
            return;
        }
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
        self::$driver_start = true;
    }
    /* ----------------------------------------- */

    /**
     * +-- code_coverage終了
     *
     * @access      public
     * @return      array
     */
    public function finish()
    {
        if (!self::$driver_start) {
            return array();
        }
        $data = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        self::$driver_start = false;
        $this->filter($data);
        return $data;
    }
    /* ----------------------------------------- */

    private function filter(array &$data)
    {
        foreach (array_keys($data) as $file_name) {
            if (!file_exists($file_name) ||
                $this->code_coverage->filter()->isFiltered($file_name)) {
                unset($data[$file_name]);
                continue;
            }

            foreach ($this->code_coverage->parser()->getSkipLine($file_name) as $line) {
                unset($data[$file_name][$line]);
            }
            foreach ($data[$file_name] as $line => $v) {
                if ($v === self::UN_USE_FLAG) {
                    unset($data[$file_name][$line]);
                }
            }
            if (empty($data[$file_name])) {
                unset($data[$file_name]);
            }
        }
        return $data;
    }
}

