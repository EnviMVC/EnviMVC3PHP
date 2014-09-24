<?php
/**
 * 静的コード解析
 *
 * 静的コード解析。
 *
 * @abstract
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/c/man/v3/reference
 * @since      Class available since Release 1.0.0
 * @doc_ignore
 */
require dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'spyc.php';
class ParsePHP
{
    protected $yml_file = 'php_static.yml';
    protected $highlight_comment;
    protected $highlight_default;
    protected $highlight_keyword;
    protected $highlight_html;
    protected $highlight_string;
    private $configuration;
    private $error_count;
    private $echo_cache = array();
    /**
     * +-- コンストラクタ
     *
     * @access      public
     * @return      void
     */
    public function __construct()
    {
        $this->highlight_comment = ini_get('highlight.comment');
        $this->highlight_default = ini_get('highlight.default');
        $this->highlight_keyword = ini_get('highlight.keyword');
        $this->highlight_html    = ini_get('highlight.html');
        $this->highlight_string  = ini_get('highlight.string');
        $this->yml_file  = dirname(__FILE__).DIRECTORY_SEPARATOR.'php_static.yml';
    }
    /* ----------------------------------------- */
    /**
     * +-- デストラクタ
     *
     * @access      public
     * @return      void
     */
    public function __destruct()
    {
        foreach ($this->echo_cache as $v) {
            echo $v."\r\n";
        }
    }
    /* ----------------------------------------- */
    /**
     * +-- spycでymlをパースする
     *
     * @final
     * @access      public
     * @return      void
     */
    final public function spyc_load_file()
    {
        $this->configuration = spyc_load(strtolower(file_get_contents($this->yml_file)));
    }
    /* ----------------------------------------- */
    /**
     * +-- OSに応じた文字コードでechoする
     *
     * @final
     * @access      private
     * @param       string $text
     * @return      void
     */
    final public function echoText($text)
    {
        $this->echo_cache[] = DIRECTORY_SEPARATOR === '/' ? $text : mb_convert_encoding($text, 'SJIS-win', 'UTF-8');
    }
    /* ----------------------------------------- */
    /**
     * +-- 構文解析
     *
     * @access      public
     * @param       string $contents
     * @return      string
     */
    final public function parse($contents)
    {
        $contents = strtolower($contents);
        $this->spyc_load_file();
        $highlight_contents = highlight_string($contents, true);
        // html部分の削除
        $this->_deleteHtml($highlight_contents);
        // Commentの削除
        $comment = $this->_deleteComment($highlight_contents);
        // 使用禁止関数の確認
        $this->doVulnerabilityFunction($highlight_contents);
        if (!count($this->error_count)) {
            // $this->echoText('no error');
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイルベースのパース
     *
     * @final
     * @access      public
     * @param       var_text $contents
     * @return      void
     */
    final public function parseFile($contents)
    {
        $cmd = 'php -l "'.$contents.'"';
        $tmp = `$cmd`;
        if (strpos($tmp, 'No syntax errors detected in ') !== 0) {
            echo $tmp;
        }
        $this->parse(file_get_contents($contents));
        if (count($this->echo_cache)) {
            array_unshift($this->echo_cache, $contents);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 使用禁止関数の確認
     *
     * @access      protected
     * @param       string $highlight_contents
     * @return      void
     */
    protected function doVulnerabilityFunction($highlight_contents)
    {
        $functions = $this->getUseFunction($highlight_contents);
        $error_count = $this->error_count;
        foreach ($functions as $func) {
            foreach ($this->configuration['vulnerabilityfunction'] as $conf) {
                if ($conf['name'] === $func) {
                    $this->echoText(isset($conf['description']) ? $conf['description'] : 'vulnerability function error : '.$conf['name']);
                    if (!isset($error_count[$func])) {
                        $error_count[$func] = 0;
                    }
                    $error_count[$func]++;
                }
            }
        }
        $this->error_count = $error_count;
    }
    /* ----------------------------------------- */

    /**
     * +-- コールされている関数の一覧を返す
     *
     * @access      protected
     * @param       string $contents
     * @return      array
     */
    protected function getUseFunction($contents)
    {
        // 定義を削除
        $preg = '(<span style="color: '.$this->highlight_keyword.'">([^<]*<br \/>|&nbsp;)*?((var|public|static|private|abstract|protected|final)(<br \/>|&nbsp;))*?(class|(<br \/>|&nbsp;)*?function|trait|extends|implements)(<br \/>|&nbsp;)*?<\/span>)'
                .'<span style="color: '.$this->highlight_default.'">([^<]*?)<\/span>';

        preg_match_all('/'.$preg.'/', $contents, $match);
        $contents = str_replace($match[0], '', $contents);

        // メソッドの実行を削除
        $preg = '<span style="color: '.$this->highlight_keyword.'">(-&gt;|::)<\/span>'
            .'<span style="color: '.$this->highlight_default.'">([^$][^<]+?)<\/span>';

        preg_match_all('/'.$preg.'/', $contents, $match);
        $contents = str_replace($match[0], '', $contents);

        // 取得
        $preg = '<span style="color: '.$this->highlight_default.'">([^$][^<]+?)<\/span>'
            .'<span style="color: '.$this->highlight_keyword.'">\(';

        preg_match_all('/'.$preg.'/', $contents, $match);
        foreach ($match[1] as &$v) {
            $v = str_replace(array('<br />', '&nbsp;'), '', $v);
        }
        preg_match_all('/(include_once|include|require|require_once)(&nbsp;|\(|<)/', $contents, $match2);

        return array_merge(($match[1]), ($match2[1]));
    }
    /* ----------------------------------------- */

    /**
     * +-- Comment部分を削除して、削除したCommentを返す
     *
     * @access      private
     * @param       string &$contents
     * @return      array
     */
    private function _deleteComment(&$contents)
    {
        $preg = '<span style="color: '.$this->highlight_comment.'">(.*?)<\/span>';
        preg_match_all('/'.$preg.'/', $contents, $match);
        $contents = str_replace($match[0], '', $contents);
        foreach ($match[1] as &$v) {
            $v = str_replace(array('<br />', '&nbsp;'), array("\n", ' '), $v);
        }
        unset($v);
        return $match[1];
    }
    /* ----------------------------------------- */

    /**
     * +-- HTML部分を削除する
     *
     * @access      private
     * @param       string &$contents
     * @return      void
     */
    private function _deleteHtml(&$contents)
    {
        $contents = mb_ereg_replace('(</span>)([^<]|<[^s]).*?(</span>|<span)', "\\1\\3", $contents);
    }
    /* ----------------------------------------- */
}


class ParsePHP_Executer
{
    private $argv;

    /**
     * +-- コンストラクタ
     *
     * @access      public
     * @return      void
     */
    public function __construct()
    {
        global $argv;
        $this->argv = $argv;
    }
    /* ----------------------------------------- */

    /**
     * +-- メイン処理
     *
     * @access      public
     * @return      void
     */
    public function main()
    {
        $ParsePHP = new ParsePHP;

        switch (true) {
            case (isset($this->argv[1]) ? is_file($this->argv[1]) || is_dir($this->argv[1]) : false):
                $this->refDo($this->argv[1], clone $ParsePHP);
                break;
            case $buffer = fgets(STDIN):
                $contents = $buffer;
                while (($buffer = fgets(STDIN)) !== false) {
                    $contents .= $buffer;
                }
                $ParsePHP->parse($contents);
                break;
             default:
                break;
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- 再帰的にディレクトリを開いてPHPをチェックする
     *
     * @access      private
     * @param       string $path
     * @param       ParsePHP $ParsePHP
     * @return      void
     */
    private function refDo($path, ParsePHP $ParsePHP)
    {
        if (is_file($path) && strpos($path, '.php')) {
            $ParsePHP->parseFile(realpath($path));
            return;
        }
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_file($path.DIRECTORY_SEPARATOR.$file) && preg_match('/\.php$/', $path.DIRECTORY_SEPARATOR.$file)) {
                        $ParsePHP2 = clone $ParsePHP;
                        $ParsePHP2->parseFile(realpath($path.DIRECTORY_SEPARATOR.$file));
                        unset($ParsePHP2);
                    } elseif ($file !== '.' && $file !== '..' && is_dir($path.DIRECTORY_SEPARATOR.$file)) {
                        $this->refDo($path.DIRECTORY_SEPARATOR.$file, clone $ParsePHP);
                    }
                }
                closedir($dh);
            }
        }
    }
    /* ----------------------------------------- */
}

$ParsePHP_Executer = new ParsePHP_Executer;
$ParsePHP_Executer->main();
