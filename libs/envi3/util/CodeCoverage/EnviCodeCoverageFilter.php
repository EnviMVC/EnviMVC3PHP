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
class EnviCodeCoverageFilter
{
    private $black_list_files;
    private $white_list_files;
    private $code_coverage;

    /**
     * +-- クラスのビルダ
     *
     * @access      public
     * @static
     * @param       EnviCodeCoverage $code_coverage
     * @return      EnviCodeCoverageFilter
     */
    public static function factory(EnviCodeCoverage $code_coverage)
    {
        $obj = new EnviCodeCoverageFilter;
        $obj->code_coverage = $code_coverage;
        return $obj;
    }
    /* ----------------------------------------- */

    /**
     * +-- ブラックリストにファイルを追加する
     *
     * @access      public
     * @param       string $file_name
     * @return      void
     */
    public function addBlackList($file_name)
    {
        $file_name = realpath($file_name);
        if (!$file_name) {
            return;
        }
        $this->black_list_files[$file_name] = true;
    }
    /* ----------------------------------------- */

    /**
     * +-- ホワイトリストにファイルを追加する
     *
     * @access      public
     * @param       string $file_name
     * @return      void
     */
    public function addWhiteList($file_name)
    {
        $file_name = realpath($file_name);
        if (!$file_name) {
            return;
        }
        $this->white_list_files[$file_name] = true;
    }
    /* ----------------------------------------- */

    /**
     * +-- ブラックリストから削除する
     *
     * @access      public
     * @param       string $file_name
     * @return      void
     */
    public function removeBlackList($file_name)
    {
        $file_name = realpath($file_name);
        if (!$file_name) {
            return;
        }
        if (isset($this->black_list_files[$file_name])) {
            unset($this->black_list_files[$file_name]);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- ホワイトリストから削除する
     *
     * @access      public
     * @param       string $file_name
     * @return      void
     */
    public function removeWhiteList($file_name)
    {
        $file_name = realpath($file_name);
        if (!$file_name) {
            return;
        }
        if (isset($this->white_list_files[$file_name])) {
            unset($this->white_list_files[$file_name]);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列をブラックリストに追加する
     *
     * @access      public
     * @param       array $list
     * @return      array
     */
    public function addBlackListByList(array $list)
    {
        foreach ($list as $file_name) {
            $this->addBlackList($file_name);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列をホワイトリストに追加する
     *
     * @access      public
     * @param       array $list
     * @return      array
     */
    public function addWhiteListByList(array $list)
    {
        foreach ($list as $file_name) {
            $this->addWhiteList($file_name);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列をブラックリストから削除する
     *
     * @access      public
     * @param       array $list
     * @return      array
     */
    public function removeBlackListByList(array $list)
    {
        foreach ($list as $file_name) {
            $this->removeBlackList($file_name);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列をホワイトリストから削除する
     *
     * @access      public
     * @param       array $list
     * @return      array
     */
    public function removeWhiteListByList(array $list)
    {
        foreach ($list as $file_name) {
            $this->removeWhiteList($file_name);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- ディレクトリをブラックリストに追加する
     *
     * @access      public
     * @param       string $dir_path
     * @param       string $suffix OPTIONAL:'.php'
     * @param       string $prefix OPTIONAL:''
     * @return      void
     */
    public function addBlackListByDirectory($dir_path, $suffix = '.php', $prefix = '')
    {
        $list = $this->getFileListByDirectory($dir_path, $suffix, $prefix);
        $this->addBlackListByList($list);
    }
    /* ----------------------------------------- */

    /**
     * +-- ディレクトリをホワイトリストに追加する
     *
     * @access      public
     * @param       string $dir_path
     * @param       string $suffix OPTIONAL:'.php'
     * @param       string $prefix OPTIONAL:''
     * @return      void
     */
    public function addWhiteListByDirectory($dir_path, $suffix = '.php', $prefix = '')
    {
        $list = $this->getFileListByDirectory($dir_path, $suffix, $prefix);
        $this->addWhiteListByList($list);
    }
    /* ----------------------------------------- */

    /**
     * +-- ディレクトリをブラックリストから削除する
     *
     * @access      public
     * @param       string $dir_path
     * @param       string $suffix OPTIONAL:'.php'
     * @param       string $prefix OPTIONAL:''
     * @return      void
     */
    public function removeBlackListByDirectory($dir_path, $suffix = '.php', $prefix = '')
    {
        $list = $this->getFileListByDirectory($dir_path, $suffix, $prefix);
        $this->removeBlackListByList($list);
    }
    /* ----------------------------------------- */

    /**
     * +-- ディレクトリをホワイトリストから削除する
     *
     * @access      public
     * @param       string $dir_path
     * @param       string $suffix OPTIONAL:'.php'
     * @param       string $prefix OPTIONAL:''
     * @return      void
     */
    public function removeWhiteListByDirectory($dir_path, $suffix = '.php', $prefix = '')
    {
        $list = $this->getFileListByDirectory($dir_path, $suffix, $prefix);
        $this->removeWhiteListByList($list);
    }
    /* ----------------------------------------- */

    /**
     * +-- ブラックリストを返す
     *
     * @access      public
     * @return      array
     */
    public function getBlackList()
    {
        return array_keys($this->black_list_files);
    }
    /* ----------------------------------------- */

    /**
     * +-- ホワイトリストを返す
     *
     * @access      public
     * @return      array
     */
    public function getWhiteList()
    {
        return array_keys($this->white_list_files);
    }
    /* ----------------------------------------- */

    /**
     * +-- 再帰的にディレクトリ内のファイルを取得する
     *
     * @access      private
     * @param       var_text $dir_path
     * @param       var_text $suffix OPTIONAL:'.php'
     * @param       var_text $prefix OPTIONAL:''
     * @return      void
     */
    private function getFileListByDirectory($dir_path, $suffix = '.php', $prefix = '')
    {
        if (!is_dir($dir_path)) {
            return array();
        }
        $list = $tmp = array();
        foreach(glob($dir_path . '*/', GLOB_ONLYDIR) as $child_dir) {
            if ($tmp = $this->getFileListByDirectory($child_dir, $suffix, $prefix)) {
                $list = array_merge($list, $tmp);
            }
        }

        foreach(glob($dir_path.$prefix.'*'.$suffix , GLOB_BRACE) as $file_path) {
            $list[] = $file_path;
        }

        return $list;
    }
    /* ----------------------------------------- */



    /**
     * +-- ファイルかどうか
     *
     * @access      public
     * @param       string $file_name
     * @return      boolean
     */
    public function isFile($file_name)
    {
        if ($file_name == '-' ||
            strpos($file_name, 'vfs://') === 0 ||
            strpos($file_name, 'xdebug://debug-eval') !== false ||
            strpos($file_name, 'eval()\'d code') !== false ||
            strpos($file_name, 'runtime-created function') !== false ||
            strpos($file_name, 'runkit created function') !== false ||
            strpos($file_name, 'assert code') !== false ||
            strpos($file_name, 'regexp code') !== false) {
            return false;
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- フィルタリングされるファイルかどうか
     *
     * フィルタリングされるファイルならtrueそうで無いならfalseを返す
     *
     * @access      public
     * @param       string $file_name
     * @return      boolean
     */
    public function isFiltered($file_name)
    {
        if (!$this->isFile($file_name)) {
            return true;
        }

        $file_name = realpath($file_name);
        if (!$file_name) {
            return true;
        }
        if (!is_file($file_name)) {
            return true;
        }

        if (!empty($this->white_list_files)) {
            return !isset($this->white_list_files[$file_name]);
        }

        return isset($this->black_list_files[$file_name]);
    }
    /* ----------------------------------------- */
}

