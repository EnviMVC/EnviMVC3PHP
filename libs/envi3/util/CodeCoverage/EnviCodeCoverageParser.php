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
class EnviCodeCoverageParser
{
    private $parser;
    private $ignored_lines;
    private $code_coverage;

    /**
     * +-- ファクトリ
     *
     * @access      public
     * @static
     * @param       EnviCodeCoverage $code_coverage
     * @return      EnviCodeCoverageParser
     */
    public static function factory(EnviCodeCoverage $code_coverage)
    {
        $obj = new EnviCodeCoverageParser;
        $obj->code_coverage = $code_coverage;
        if (!class_exists('EnviCodeParser', false)) {
            include dirname(dirname(__FILE__)).'/EnviCodeParser.php';
        }
        $obj->parser = new EnviCodeParser;
        return $obj;
    }
    /* ----------------------------------------- */

    public function parseFile($file_name)
    {
        return $this->parser->parseFile($file_name);
    }

    /**
     * +-- ファイル名を指定して、展開済みのDocBlockのリストを取得する
     *
     * @access      public
     * @param       string $file_name
     * @return      array
     */
    public function getDocTagList($file_name)
    {
        return $this->parser->getDocTagList($file_name);
    }
    /* ----------------------------------------- */

    /**
     * +-- メソッドのDogsTagを返す
     *
     * @access      public
     * @param       string $file_name
     * @return      array
     */
    public function getMethodDocsTagSimple($file_name)
    {
        return $this->parser->getMethodDocsTagSimple($file_name);
    }
    /* ----------------------------------------- */

    /**
     * +-- code coverage 計測範囲外行を取得する
     *
     * @access      public
     * @param       string $file_name ファイルパス
     * @return      array
     */
    public function getSkipLine($file_name)
    {
        static $codeCoverageIgnore_len,
            $codeCoverageIgnoreStart_len,
            $codeCoverageIgnoreEnd_len;
        if (empty($codeCoverageIgnore_len)) {
            $codeCoverageIgnore_len      = strlen('@codeCoverageIgnore')*-1;
            $codeCoverageIgnoreStart_len = strlen('@codeCoverageIgnoreStart')*-1;
            $codeCoverageIgnoreEnd_len   = strlen('@codeCoverageIgnoreEnd')*-1;
        }


        if (isset($this->ignored_lines[$file_name])) {
            return $this->ignored_lines[$file_name];
        }
        $this->ignored_lines[$file_name] = array();
        $ignore                        = false;
        $stop                          = false;
        $lines                         = file($file_name);
        $lines_count                   = count($lines);

        foreach ($lines as $index => $line) {
            if (!trim($line)) {
                $this->ignored_lines[$file_name][] = $index + 1;
            }
        }

        $token_result = $this->parseFile($file_name);

        $classes = array_merge($token_result->getClasses(), $token_result->getTraits());
        $token_list  = $token_result->getTokenList();

        foreach ($token_list as $token) {
            switch ($token->getTokenName()) {
            case 'COMMENT':
            case 'DOC_COMMENT':
                $_token = trim($token);
                $_line  = trim($lines[$token->getLine() - 1]);

                if (substr($_token, $codeCoverageIgnore_len) === '@codeCoverageIgnore') {
                    $ignore = true;
                    $stop   = true;
                } elseif (substr($_token, $codeCoverageIgnoreStart_len) === '@codeCoverageIgnoreStart') {
                    $ignore = true;
                } elseif (substr($_token, $codeCoverageIgnoreEnd_len) === '@codeCoverageIgnoreEnd') {
                    $stop = true;
                }

                if (strpos($_token, $_line) === 0) {
                    $count = substr_count($token, "\n");
                    $line  = $token->getLine();

                    for ($i = $line; $i < $line + $count; $i++) {
                        $this->ignored_lines[$file_name][] = $i;
                    }

                    if ($token->getTokenName() === 'DOC_COMMENT') {
                        if (substr(trim($lines[$i-1]), -2) === '*/') {
                            $this->ignored_lines[$file_name][] = $i;
                        }
                    }
                }
                break;

            case 'INTERFACE':
            case 'TRAIT':
            case 'CLASS':
            case 'FUNCTION':
                $doc_block = $token->getDocBlock();

                $this->ignored_lines[$file_name][] = $token->getLine();

                if (strpos($doc_block, '@codeCoverageIgnore')) {
                    $end_line = $token->getEndLine();

                    for ($i = $token->getLine(); $i <= $end_line; $i++) {
                        $this->ignored_lines[$file_name][] = $i;
                    }
                } elseif ($token instanceof EnviParserToken_INTERFACE ||
                    $token instanceof EnviParserToken_TRAIT ||
                    $token instanceof EnviParserToken_CLASS) {
                    if (empty($classes[$token->getName()]['methods'])) {
                        for ($i = $token->getLine();
                             $i <= $token->getEndLine();
                             $i++) {
                            $this->ignored_lines[$file_name][] = $i;
                        }
                    } else {
                        $firstMethod = array_shift(
                            $classes[$token->getName()]['methods']
                        );

                        do {
                            $lastMethod = array_pop(
                                $classes[$token->getName()]['methods']
                            );
                        } while ($lastMethod !== null &&
                            substr($lastMethod['signature'], 0, 18) === 'anonymous function');

                        if ($lastMethod === null) {
                            $lastMethod = $firstMethod;
                        }

                        for ($i = $token->getLine();
                             $i < $firstMethod['start_line'];
                             $i++) {
                            $this->ignored_lines[$file_name][] = $i;
                        }

                        for ($i = $token->getEndLine();
                             $i > $lastMethod['end_line'];
                             $i--) {
                            $this->ignored_lines[$file_name][] = $i;
                        }
                    }
                }
                break;

            case 'NAMESPACE':
                $this->ignored_lines[$file_name][] = $token->getEndLine();
                $this->ignored_lines[$file_name][] = $token->getLine();
                break;
            case 'OPEN_TAG':
            case 'CLOSE_TAG':
            case 'USE':
                $this->ignored_lines[$file_name][] = $token->getLine();
                break;
            }

            if ($ignore) {
                $this->ignored_lines[$file_name][] = $token->getLine();

                if ($stop) {
                    $ignore = false;
                    $stop   = false;
                }
            }
        }

        $this->ignored_lines[$file_name][] = $lines_count + 1;

        $this->ignored_lines[$file_name] = array_unique(
            $this->ignored_lines[$file_name]
        );

        sort($this->ignored_lines[$file_name]);

        return $this->ignored_lines[$file_name];
    }
    /* ----------------------------------------- */

}

