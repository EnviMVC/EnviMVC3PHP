<?php
/**
 * PHPdoc形式で作成したコメントを、Markdown形式のドキュメントに書き出します
 *
 * 現状は一部無視されるタグなどがあり、不完全な状態です。
 *
 * また、代わりに独自タグか実装されて居亜ｍ州。
 *
 * EnviMVC3のクラスリファレンス作成が主な用途です。
 *
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.3.3.5
 */
require 'EnviCodeParser.php';
require dirname(__FILE__).DIRECTORY_SEPARATOR.'Documenter/EnviDocumenterDriver.php';

/**
 * PHPdoc形式で作成したコメントを、Markdown形式のドキュメントに書き出します
 *
 * 現状は一部無視されるタグなどがあり、不完全な状態です。
 *
 * また、代わりに独自タグか実装されています。
 *
 * EnviMVC3のクラスリファレンス作成が主な用途です。
 *
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.3.3.5
 */
class EnviDocumenter
{
    private $code_parser;
    private $driver;
    private $method_list = array();


    /**
     * +-- コンストラクタ
     *
     * @access      public
     * @param       string $docs_dir ドキュメント化するディレクトリ OPTIONAL:NULL
     * @param       string $template_dir テンプレートファイル格納ディレクトリ OPTIONAL:NULL
     * @return      void
     */
    public function __construct($docs_dir = NULL, $template_dir = NULL)
    {
        $this->code_parser = new EnviCodeParser;
        $this->driver = new EnviDocumenterDriver($docs_dir, $template_dir);
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイルを指定してパースする
     *
     * @access      public
     * @param       var_text $file_name
     * @return      void
     */
    public function parseFile($file_name)
    {
        $this->cliWrite($file_name.' parse start');
        $cpr = $this->code_parser->parseFile($file_name);
        $token_list = $cpr->getTokenList();

        // @doc_ignoreの処理
        foreach ($token_list as $file_doc) {
            if ($file_doc->getTokenName() === 'DOC_COMMENT') {
                $arr = $file_doc->getDocBlockArray();
                if (isset($arr['doc_ignore'])) {
                    return;
                }
                break;
            }
        }



        // クラスリストの正規化
        $class_list = $cpr->getClassList();
        foreach ($class_list as $class_name => $class_item) {
            if (!($class_item['token']->getDocBlockToken() instanceof EnviParserToken)) {
                unset($class_list[$class_name]);
                continue;
            }
            $doc_block_array = $class_item['token']->getDocBlockToken()->getDocBlockArray();
            if (isset($doc_block_array['doc_ignore'])) {
                unset($class_list[$class_name]);
                continue;
            }
            foreach ($class_item['methods'] as $method_name => $method) {
                if (!($method->getDocBlockToken() instanceof EnviParserToken)) {
                    unset($class_list[$class_name]['methods'][$method_name]);
                    continue;
                } elseif (!($method->getDocBlockToken() instanceof EnviParserToken_DOC_COMMENT)) {
                        var_dump($method->getDocBlockToken()->getTokenName());
                        die;
                }
                $doc_block_array = $method->getDocBlockToken()->getDocBlockArray();
                if (isset($doc_block_array['access'][0][0]) && $doc_block_array['access'][0][0] !== 'public') {
                    unset($class_list[$class_name]['methods'][$method_name]);
                    continue;
                }
                if (isset($doc_block_array['doc_ignore'])) {
                    unset($class_list[$class_name]['methods'][$method_name]);
                    continue;
                }
                if ($method->getVisibility() !== 'public') {
                    unset($class_list[$class_name]['methods'][$method_name]);
                    continue;
                }
                if (strpos($method->getName(), '_') === 0) {
                    unset($class_list[$class_name]['methods'][$method_name]);
                    continue;
                }

                $this->method_list[$method->getMethodName()] = array(
                    'class_array' => $class_list[$class_name],
                    'file_doc_token' => $file_doc,
                    'class_name'  => $class_name,
                    'class_token' => $class_item['token'],
                    'file_path'   => $file_name,
                    'token'       => $method,
                    'class_token' => $class_list[$class_name]['token'],
                    'cpr'         => $cpr,
                    'man_path'    => $this->driver->makeWritePath($file_name).'/'.$class_name.'/'.$method->getName().'.md',
                    'file_name' => mb_ereg_replace('\.php$', '', basename($cpr->getFileName())),
                );
            }
        }

        $this->driver->fileWriter($class_list, $token_list, $cpr);

        $this->driver->constantWriter($class_list, $cpr);
        foreach ($class_list as $class_name => $class_item) {
            $this->driver->classWriter($class_name, $class_item['token'], $class_item['methods'], $cpr);
        }
        $this->cliWrite($file_name.' parse end');
    }
    /* ----------------------------------------- */

    public function parse($file_name, $suffix = '.php', $prefix = '')
    {
        $start_time = microtime(true);
        if (is_file($file_name)) {
            $this->parseFile($file_name);
        } elseif (is_dir($file_name)) {
            $file_path_list = $this->getFileListByDirectory($file_name, $suffix, $prefix);
            foreach ($file_path_list as $file_path) {
                $this->parseFile($file_path);
            }
        }
        $this->cliWrite('method list writing');
        foreach ($this->method_list as $method_name => $item) {
            $this->driver->methodWriter($this->method_list, $method_name);
        }
        $this->cliWrite('parse end:'.(microtime(true)-$start_time).'sec');
    }


    public function cliWrite($str, $separator = "\n")
    {
        if (PHP_SAPI === ('cli')) {
            echo $str, $separator;
        }
    }

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
}


