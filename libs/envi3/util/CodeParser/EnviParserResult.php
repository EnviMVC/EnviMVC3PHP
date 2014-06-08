<?php
/**
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

/**
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
class EnviParserResult implements ArrayAccess, Countable, SeekableIterator
{
    /**
     * @var array
     */
    protected static $customTokens = array(
      '(' => 'EnviParserToken_OPEN_BRACKET',
      ')' => 'EnviParserToken_CLOSE_BRACKET',
      '[' => 'EnviParserToken_OPEN_SQUARE',
      ']' => 'EnviParserToken_CLOSE_SQUARE',
      '{' => 'EnviParserToken_OPEN_CURLY',
      '}' => 'EnviParserToken_CLOSE_CURLY',
      ';' => 'EnviParserToken_SEMICOLON',
      '.' => 'EnviParserToken_DOT',
      ',' => 'EnviParserToken_COMMA',
      '=' => 'EnviParserToken_EQUAL',
      '<' => 'EnviParserToken_LT',
      '>' => 'EnviParserToken_GT',
      '+' => 'EnviParserToken_PLUS',
      '-' => 'EnviParserToken_MINUS',
      '*' => 'EnviParserToken_MULT',
      '/' => 'EnviParserToken_DIV',
      '?' => 'EnviParserToken_QUESTION_MARK',
      '!' => 'EnviParserToken_EXCLAMATION_MARK',
      ':' => 'EnviParserToken_COLON',
      '"' => 'EnviParserToken_DOUBLE_QUOTES',
      '@' => 'EnviParserToken_AT',
      '&' => 'EnviParserToken_AMPERSAND',
      '%' => 'EnviParserToken_PERCENT',
      '|' => 'EnviParserToken_PIPE',
      '$' => 'EnviParserToken_DOLLAR',
      '^' => 'EnviParserToken_CARET',
      '~' => 'EnviParserToken_TILDE',
      '`' => 'EnviParserToken_BACKTICK',
    );

    /**
     * @var string
     */
    protected $file_name;

    /**
     * @var array
     */
    protected $token_list = array();

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var array
     */
    protected $code_lines = array('total' => 0, 'comment' => 0, 'code_only' => 0);

    /**
     * @var array
     */
    protected $classes;

    /**
     * @var array
     */
    protected $functions;

    /**
     * @var array
     */
    protected $includes;

    /**
     * @var array
     */
    protected $interfaces;

    /**
     * @var array
     */
    protected $traits;

    /**
     * @var array
     */
    protected $lineToFunctionMap = array();
    /**
     * @var array
     */
    protected $code_route_coverage = array();


    /**
     * +-- コンストラクタ
     *
     * @access      private
     * @return      void
     */
    private function __construct()
    {
    }
    /* ----------------------------------------- */

    /**
     * +-- ソースコードを指定してパースする
     *
     * @access      public
     * @static
     * @param       string $source_code
     * @return      EnviParserResult
     */
    public static function parseSourceCode($source_code)
    {
        $obj = new EnviParserResult;
        $obj->tokenScan($source_code);
        return $obj;
    }
    /* ----------------------------------------- */


    /**
     * +-- ソースコードのパスを指定してパースする
     *
     * @access      public
     * @static
     * @param       string $source_code
     * @return      EnviParserResult
     */
    public static function parseFile($source_code)
    {
        $obj = new EnviParserResult;
        if (!is_file($source_code)) {
            throw new EnviParserResultException($source_code.' can not be found.');
        }
        $obj->file_name = $source_code;
        $source_code = file_get_contents($source_code);
        $obj->tokenScan($source_code);
        return $obj;
    }
    /* ----------------------------------------- */



    /**
     * +-- DocBlockのリストを取得する
     *
     * @access      public
     * @param       var_text $file_name
     * @return      array
     */
    public function getDocTagList()
    {
        $res = array();
        foreach ($this->token_list as $token) {
            switch ($token->getTokenName()) {
            case 'INTERFACE':
            case 'TRAIT':
            case 'CLASS':
                $res[$token->getTokenName()][$token->getName()]['doc_block_all'] = $token->getDocBlock();
                $res[$token->getTokenName()][$token->getName()]['doc_block']     = $token->getDocBlockArray();
            break;
            case 'FUNCTION':
                $res[$token->getTokenName()][$token->getMethodName()]['doc_block_all'] = $token->getDocBlock();
                $res[$token->getTokenName()][$token->getMethodName()]['doc_block'] = $token->getDocBlockArray();
            break;
            }
        }
        return $res;
    }
    /* ----------------------------------------- */






    /**
     * +-- 全体の経路複雑度を行数毎に集計して返す
     *
     * @access      public
     * @return      array
     */
    public function getCodeRouteCoverage()
    {
        if ($this->code_route_coverage) {
            return $this->code_route_coverage;
        }

        foreach ($this->token_list as $token) {
            if (!isset($this->code_route_coverage[$token->getLine()])) {
                $this->code_route_coverage[$token->getLine()] = 1;
            }
            switch ($token->getTokenName()) {
            case 'IF':
            case 'ELSEIF':
            case 'FOR':
            case 'FOREACH':
            case 'WHILE':
            case 'CASE':
            case 'CATCH':
            case 'BOOLEAN_AND':
            case 'LOGICAL_AND':
            case 'BOOLEAN_OR':
            case 'LOGICAL_OR':
            case 'QUESTION_MARK':
                $this->code_route_coverage[$token->getLine()]++;
            break;
            }
        }
        return $this->code_route_coverage;
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
        $this->token_list = array();
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access      public
     * @return      string
     */
    public function __toString()
    {
        $buffer = '';

        foreach ($this as $token) {
            $buffer .= $token;
        }

        return $buffer;
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイル名を返す
     *
     * @access      public
     * @return      string
     */
    public function getFileName()
    {
        return $this->file_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- ソースコードをスキャンして配列に格納する
     *
     * @access      protected
     * @param       var_text $source_code
     * @return      void
     */
    protected function tokenScan($source_code)
    {
        $line          = 1;
        $token_list    = token_get_all($source_code);
        $token_count   = count($token_list);

        $lastNonWhitespaceTokenWasDoubleColon = false;

        for ($i = 0; $i < $token_count; ++$i) {
            $token = $token_list[$i];
            unset($token_list[$i]);

            if (is_array($token)) {
                $name = substr(token_name($token[0]), 2);
                $text = $token[1];

                if ($lastNonWhitespaceTokenWasDoubleColon && $name === 'CLASS') {
                    $name = 'CLASS_NAME_CONSTANT';
                }

                $token_class_name = 'EnviParserToken_' . $name;
            } else {
                $text       = $token;
                $token_class_name = self::$customTokens[$token];
            }

            $this->token_list[] = new $token_class_name($text, $line, $this, $i);
            $lines          = substr_count($text, "\n");
            $line          += $lines;

            if ($token_class_name === 'EnviParserToken_HALT_COMPILER') {
                break;
            } elseif ($token_class_name === 'EnviParserToken_COMMENT' ||
                $token_class_name === 'EnviParserToken_DOC_COMMENT') {
                $this->code_lines['comment'] += $lines + 1;
            }

            if ($name === 'DOUBLE_COLON') {
                $lastNonWhitespaceTokenWasDoubleColon = true;
            } elseif ($name !== 'WHITESPACE') {
                $lastNonWhitespaceTokenWasDoubleColon = false;
            }
        }

        $this->code_lines['total']   = substr_count($source_code, "\n");
        $this->code_lines['code_only'] = $this->code_lines['total'] -
                                      $this->code_lines['comment'];
    }
    /* ----------------------------------------- */


    /**
     * +-- トークンオブジェクトの配列を返す
     *
     * @access      public
     * @return      array
     */
    public function getTokenList()
    {
        return $this->token_list;
    }
    /* ----------------------------------------- */

    /**
     * +-- 登録されているクラスを返す
     *
     * @access      public
     * @return      array
     */
    public function getClassList()
    {
        if ($this->classes === NULL) {
            $this->parse();
        }
        return $this->classes;
    }
    /* ----------------------------------------- */

    /**
     * +-- 登録されている関数を返す
     *
     * @access      public
     * @return      void
     */
    public function getFunctionList()
    {
        if ($this->functions === NULL) {
            $this->parse();
        }
        return $this->functions;
    }
    /* ----------------------------------------- */


    /**
     * +-- 登録されているインターフェイスを返す
     *
     * @access      public
     * @return      array
     */
    public function getInterfaceList()
    {
        if ($this->interfaces === NULL) {
            $this->parse();
        }
        return $this->interfaces;
    }
    /* ----------------------------------------- */

    /**
     * +-- 登録されているTraitを返す
     *
     * @access      public
     * @return      array
     */
    public function getTraitList()
    {
        if ($this->traits === NULL) {
            $this->parse();
        }
        return $this->traits;
    }
    /* ----------------------------------------- */

    /**
     * +-- includeされているファイルを返す
     *
     * @access      public
     * @param       boolean $categorize OPTIONAL:false include方法で分ける
     * @param       string $category OPTIONAL:NULL include方法を指定する
     * @return      array
     */
    public function getIncludes($categorize = false, $category = NULL)
    {
        if ($this->includes === NULL) {
            $this->includes = array(
              'require_once' => array(),
              'require'      => array(),
              'include_once' => array(),
              'include'      => array()
            );

            foreach ($this->token_list as $token) {
                switch ($token->getTokenName()) {
                case 'REQUIRE_ONCE':
                case 'REQUIRE':
                case 'INCLUDE_ONCE':
                case 'INCLUDE':
                    $this->includes[$token->getType()][] = $token->getName();
                break;
                }
            }
        }

        if (isset($this->includes[$category])) {
            return $this->includes[$category];
        } elseif ($categorize === false) {
            return array_merge(
              $this->includes['require_once'],
              $this->includes['require'],
              $this->includes['include_once'],
              $this->includes['include']
            );
        }

        return $this->includes;
    }
    /* ----------------------------------------- */

    /**
     * +-- 指定行に登録されている関数を返す
     *
     * @access      public
     * @param       integer $line
     * @return      string
     */
    public function getFunctionForLine($line)
    {
        $this->parse();
        if (isset($this->lineToFunctionMap[$line])) {
            return $this->lineToFunctionMap[$line];
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- トークン配列をパースして配列に格納する
     *
     * @access      protected
     * @return      void
     */
    protected function parse()
    {
        $this->interfaces = array();
        $this->classes    = array();
        $this->traits     = array();
        $this->functions  = array();
        $class_name            = false;
        $class_end_line        = false;
        $trait_name            = false;
        $trait_end_line        = false;
        $interface_name        = false;
        $interface_end_line    = false;

        foreach ($this->token_list as $token) {
            switch ($token->getTokenName()) {
            case 'HALT_COMPILER':
                return;
            break;

            case 'INTERFACE':
                $interface_name        = $token->getName();
                $interface_end_line    = $token->getEndLine();

                $this->interfaces[$interface_name] = array(
                  'methods'   => array(),
                  'token'      => $token,
                );

            break;

            case 'CLASS':
            case 'TRAIT':
                $tmp = array(
                  'methods'   => array(),
                  'token'      => $token,
                );

                if ($token->getTokenName() === 'CLASS') {
                    $class_name                 = $token->getName();
                    $class_end_line             = $token->getEndLine();
                    $this->classes[$class_name] = $tmp;
                } else {
                    $trait_name                = $token->getName();
                    $trait_end_line            = $token->getEndLine();
                    $this->traits[$trait_name] = $tmp;
                }

            break;

            case 'FUNCTION':
                $name = $token->getName();

                if ($class_name === false &&
                    $trait_name === false &&
                    $interface_name === false) {
                    $this->functions[$name] = $token;
                    $this->addFunctionToMap(
                        $name,
                        $token->getLine(), $token->getEndLine()
                    );
                } elseif ($class_name !== false) {
                    $this->classes[$class_name]['methods'][$name] = $token;

                    $this->addFunctionToMap(
                        $class_name . '::' . $name,
                        $token->getLine(), $token->getEndLine()
                    );
                } elseif ($trait_name !== false) {
                    $this->traits[$trait_name]['methods'][$name] = $token;
                    $this->addFunctionToMap(
                        $trait_name . '::' . $name,
                        $token->getLine(), $token->getEndLine()
                    );
                } else {
                    $this->interfaces[$interface_name]['methods'][$name] = $token;
                }

            break;

            case 'CLOSE_CURLY':
                if ($class_end_line !== false &&
                    $class_end_line == $token->getLine()) {
                    $class_name        = false;
                    $class_end_line = false;
                } elseif ($trait_end_line !== false &&
                    $trait_end_line == $token->getLine()) {
                    $trait_name        = false;
                    $trait_end_line = false;
                } elseif ($interface_end_line !== false &&
                    $interface_end_line == $token->getLine()) {
                    $interface_name     = false;
                    $interface_end_line = false;
                }

            break;
            }
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- コード行数情報配列を返す
     *
     * @access      public
     * @return      array
     */
    public function getCodeLines()
    {
        return $this->code_lines;
    }
    /* ----------------------------------------- */

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->token_list);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->token_list[$this->position]);
    }

    public function key()
    {
        return $this->position;
    }

    public function current()
    {
        return $this->token_list[$this->position];
    }

    public function next()
    {
        $this->position++;
    }


    public function offsetExists($offset)
    {
        return isset($this->token_list[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->token_list[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->token_list[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->token_list[$offset]);
    }

    public function seek($position)
    {
        $this->position = $position;

        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid seek position');
        }
    }

    private function addFunctionToMap($name, $start_line, $end_line)
    {
        for ($line = $start_line; $line <= $end_line; $line++) {
            $this->lineToFunctionMap[$line] = $name;
        }
    }
}

class EnviParserResultException extends exception
{}