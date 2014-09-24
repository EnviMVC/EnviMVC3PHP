<?php
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
 * PHP versions 5
 *
 *
 * @category   ユーティリティ
 * @package    ドキュメンテーション
 * @subpackage Documenter
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2014 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.3.3.5
 */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'../EnviUnitTest/CodeParser/EnviCodeParser.php';
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
 * @category   ユーティリティ
 * @package    ドキュメンテーション
 * @subpackage Documenter
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
    public $method_list    = array();
    public $class_list     = array();
    public $file_list      = array();
    public $package_list   = array();
    public $category_list  = array(
    'フレームワーク基礎処理' => array(),
    'EnviMVC拡張' => array(),
    '自動テスト' => array(),
    'ユーティリティ' => array(),

    );


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
        $this->driver = new EnviDocumenterDriver($this, $docs_dir, $template_dir);
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
        $class_list = array_merge($cpr->getClassList(), $cpr->getInterfaceList(), $cpr->getTraitList());
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
            $class_item['doc_block_array'] = $doc_block_array;
            $package_name = isset($doc_block_array['package'][0]) ? join(' ', $doc_block_array['package'][0]) : 'DefaultPackage';
            $sub_package_name = isset($doc_block_array['subpackage'][0]) ? join(' ', $doc_block_array['subpackage'][0]) : 'DefaultSubPackage';
            $category_name = isset($doc_block_array['category'][0]) ? join(' ', $doc_block_array['category'][0]) : 'DefaultCategory';
            if (!trim($package_name)) {
                var_dump($class_name);
                die;
            }


            $this->class_list[$class_name]['methods'] = array();
            $class_item['file_path']  = $file_name;
            $class_item['man_path']   = $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'/'.$class_name.'.md';
            $class_item['cpr']        = $cpr;
            $class_item['file_name']  = mb_ereg_replace('\.php$', '', basename($cpr->getFileName()));

            $this->class_list[$class_name]['class_item'] = $class_item;
            if (!isset($this->package_list[$package_name][$sub_package_name])) {
                $this->package_list[$package_name][$sub_package_name] = array(
                    'file_doc'               => $file_doc,
                    'package_name'           => $package_name,
                    'package_man_path'       => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'.md',
                    'sub_package_man_path'   => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'.md',
                    'constant_man_path'      => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'/constant.md',
                    'intro_man_path'         => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'/intro.md',
                    'setup_man_path'         => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'/setup.md',
                    'class_list' => array(),
                );
            }
            $this->category_list[$category_name][$package_name][$sub_package_name] =& $this->package_list[$package_name][$sub_package_name];
            if ($class_item['file_name'] == $sub_package_name) {
                $this->package_list[$package_name][$sub_package_name]['file_doc'] = $file_doc;
            } elseif (strpos('@subpackage_main', (string)$file_doc) !== false) {
                $this->package_list[$package_name][$sub_package_name]['file_doc'] = $file_doc;
            }

            $this->package_list[$package_name][$sub_package_name]['class_list'][$class_name] = &$this->class_list[$class_name];
            $this->file_list[$file_name][$class_name] = &$this->class_list[$class_name];

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
                $method_full_name = $class_name.'::'.$method_name;
                $this->method_list[$method_full_name] = array(
                    'method_name'     => $method_name,
                    'doc_block_array' => $doc_block_array,
                    'class_array'    => $class_list[$class_name],
                    'file_doc_token' => $file_doc,
                    'class_name'     => $class_name,
                    'class_token'    => $class_item['token'],
                    'file_path'      => $file_name,
                    'token'          => $method,
                    'class_token'    => $class_list[$class_name]['token'],
                    'cpr'            => $cpr,
                    'man_path'       => $this->driver->makeWritePath($file_name).'/'.urlencode($package_name).'/'.$sub_package_name.'/'.$class_name.'/'.$method->getName().'.md',
                    'file_name'      => mb_ereg_replace('\.php$', '', basename($cpr->getFileName())),
                );
                $this->class_list[$class_name]['methods'][$method_full_name] =& $this->method_list[$method_full_name];
            }
        }

/*
        $this->driver->fileWriter($class_list, $token_list, $cpr);

        $this->driver->constantWriter($class_list, $cpr);
        foreach ($class_list as $class_name => $class_item) {
            $this->driver->classWriter($class_name, $class_item['token'], $class_item['methods'], $cpr);
        }
        $this->cliWrite($file_name.' parse end');
*/
    }
    /* ----------------------------------------- */

    public function parse($file_name, $suffix = '.php', $prefix = '')
    {
        $start_time = microtime(true);
        $file_name_list = explode(',', $file_name);
        foreach ($file_name_list as $file_name) {
            if (is_file($file_name)) {
                $this->parseFile($file_name);
            } elseif (is_dir($file_name)) {
                $file_path_list = $this->getFileListByDirectory($file_name, $suffix, $prefix);
                foreach ($file_path_list as $file_path) {
                    $this->parseFile($file_path);
                }
            }
        }

        $this->cliWrite('method list writing');
        foreach ($this->method_list as $method_name => $item) {
            $this->driver->methodWriter($method_name);
        }
        $this->cliWrite('class list writing');
        foreach ($this->class_list as $class_name => $class_item) {
            $this->driver->classWriter($class_name);
        }

        $this->cliWrite('package_list list writing');
        foreach ($this->package_list as $package_name => $sub_packages) {
            $this->driver->packageWriter($package_name);
            foreach ($sub_packages as $sub_package_name => $sub_packages) {
                $this->driver->subPackageWriter($package_name, $sub_package_name);
                $this->driver->constantWriter($package_name, $sub_package_name);
            }
        }
        $this->driver->categoryWriter();
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


