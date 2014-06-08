<?php
/**
 * PHPファイルをパースするクラスです
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviCodeParser
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release v3.3.3.5
 */

require dirname(__FILE__).'/CodeParser/EnviParserToken.php';
require dirname(__FILE__).'/CodeParser/EnviParserResult.php';

/**
 * PHPファイルをパースするクラスです
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviCodeParser
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release v3.3.3.5
 */
class EnviCodeParser
{
    protected $is_token_cache = true;
    protected $code_route_coverage = array();

    protected static $token_cache_list = array();

    /**
     * +-- ファイルを指定してパースする
     *
     * @access      public
     * @param       var_text $file_name
     * @return      void
     */
    public function parseFile($file_name)
    {
        if ($this->is_token_cache) {
            $file_name = realpath($file_name);
            if (!isset(self::$token_cache_list[$file_name])) {
                self::$token_cache_list[$file_name] = EnviParserResult::parseFile($file_name);
            }
            return self::$token_cache_list[$file_name];
        }
        return EnviParserResult::parseFile($file_name);
    }
    /* ----------------------------------------- */

    /**
     * +-- ソースコードを指定してパースする
     *
     * @access      public
     * @param       string $source_code
     * @return      EnviParserResult
     */
    public function parseSourceCode($source_code)
    {
        return EnviParserResult::parseSourceCode($source_code);
    }
    /* ----------------------------------------- */

    /**
     * +-- FunctionとそのDocBlockをそのまま取得します
     *
     * @access      public
     * @param       string $file_name
     * @return      array
     */
    public function getMethodDocsTagSimple($file_name)
    {
        $pattern = $this->getDocTagList($file_name);
        $res = array();
        foreach ($pattern['FUNCTION'] as $function_name => $arr) {
            $res[$function_name] = $arr['doc_block'];
        }
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイル名を指定して、展開済みのDocBlockのリストを取得する
     *
     * @access      public
     * @param       var_text $file_name
     * @return      array
     */
    public function getDocTagList($file_name)
    {
        return $this->parse($file_name)->getDocTagList();
    }
    /* ----------------------------------------- */

}
