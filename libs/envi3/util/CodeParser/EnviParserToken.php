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
 * @abstract
 */
abstract class EnviParserToken
{
    /**
     * @var string
     */
    protected $token_name;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var integer
     */
    protected $line;

    /**
     * @var EnviParserResult
     */
    protected $token_result;

    /**
     * @var integer
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param string           $text
     * @param integer          $line
     * @param EnviParserResult $token_result
     * @param integer          $id
     */
    public function __construct($text, $line, EnviParserResult $token_result, $id)
    {
        $this->text          = $text;
        $this->line          = $line;
        $this->token_result  = $token_result;
        $this->id            = $id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    public function getTokenName()
    {
        if (!$this->token_name) {
            $this->token_name = substr(get_class($this), strlen('EnviParserToken_'));
        }
        return $this->token_name;
    }
}

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
 * @abstract
 */

class EnviParserToken_DOC_COMMENT extends EnviParserToken
{

    public function getDocBlockSubject($strip_text = '+--')
    {
        $arr = explode("\n", $this->text);
            $arr[1] = ltrim($arr[1]);
            if (strpos($arr[1], '* @') === 0) {
                return '';
            }
        return mb_ereg_replace('\* *('.preg_quote($strip_text).')?', '', $arr[1]);
    }

    public function getDocBlockDetail($strip_text = '+--')
    {
        $arr = explode("\n", $this->text);
        $limit = count($arr);
        $res = '';
        for ($i = 3; $i < $limit; $i++) {
            $arr[$i] = ltrim($arr[$i]);
            if (strpos($arr[$i], '* @') === 0) {
                return $res;
            }
            $res .= mb_ereg_replace('\* ?('.preg_quote($strip_text).')?', '', $arr[$i])."\r\n";
        }
        return $res;
    }

    public function getDocBlockArray()
    {
        preg_match_all('/@(.*)\n/' ,$this->getDocBlock(), $match);
        $docs = array();
        foreach ($match[1] as $doc) {
            $doc = mb_ereg_replace(' +', ' ', $doc);
            $doc = explode(' ', $doc);
            $tag = trim(array_shift($doc));
            $docs[$tag][] = $doc;
        }
        return $docs;
    }

    public function getDocBlock()
    {
        return $this->text;
    }
}

abstract class EnviParserTokenWithDocBlock extends EnviParserToken
{
    protected $doc_block_token_id;

    public function getDocBlock()
    {
        $token_list            = $this->token_result->getTokenList();
        if ($this->getDocBlockToken() instanceof EnviParserToken) {
            return $this->getDocBlockToken()->getDocBlock();
        }
    }

    public function getDocBlockArray()
    {
        if ($this->getDocBlockToken() instanceof EnviParserToken) {
            return $this->getDocBlockToken()->getDocBlockArray();
        }
    }
    public function getDocBlockToken()
    {
        $token_list            = $this->token_result->getTokenList();
        $id = $this->getDocBlockTokenId();
        return isset($token_list[$id]) && $id ? $token_list[$id] : NULL;
    }

    public function getDocBlockTokenId()
    {
        if ($this->doc_block_token_id !== NULL) {
            return $this->doc_block_token_id;
        }
        $token_list            = $this->token_result->getTokenList();
        $currentLineNumber = $token_list[$this->id]->getLine();
        $prevLineNumber    = $currentLineNumber - 1;

        for ($i = $this->id - 1; $i; $i--) {
            if (!isset($token_list[$i])) {
                return;
            }

            if ($token_list[$i] instanceof EnviParserToken_FUNCTION ||
                $token_list[$i] instanceof EnviParserToken_CLASS ||
                $token_list[$i] instanceof EnviParserToken_TRAIT) {
                break;
            }

            $line = $token_list[$i]->getLine();

            if ($line == $currentLineNumber ||
                ($line == $prevLineNumber &&
                 $token_list[$i] instanceof EnviParserToken_WHITESPACE)) {
                continue;
            }

            if ($line < $currentLineNumber &&
                !$token_list[$i] instanceof EnviParserToken_DOC_COMMENT) {
                break;
            }
            $this->doc_block_token_id = $i;
            return $this->doc_block_token_id ;
        }
    }
}


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
 * @abstract
 */
abstract class EnviParserTokenWithScope extends EnviParserTokenWithDocBlock
{
    protected $endTokenId;


    public function getEndTokenId()
    {
        $block  = 0;
        $i      = $this->id;
        $token_list = $this->token_result->getTokenList();
        if ($this->endTokenId !== NULL) {
            return $this->endTokenId;
        }
        while (isset($token_list[$i])) {
            if ($token_list[$i] instanceof EnviParserToken_OPEN_CURLY ||
                $token_list[$i] instanceof EnviParserToken_CURLY_OPEN) {
                $block++;
            } else if ($token_list[$i] instanceof EnviParserToken_CLOSE_CURLY) {
                $block--;

                if ($block === 0) {
                    $this->endTokenId = $i;
                }
            } else if (($this instanceof EnviParserToken_FUNCTION ||
                $this instanceof EnviParserToken_NAMESPACE) &&
                $token_list[$i] instanceof EnviParserToken_SEMICOLON) {
                if ($block === 0) {
                    $this->endTokenId = $i;
                }
            }

            $i++;
        }

        $this->endTokenId = $this->id;
        return $this->endTokenId;
    }

    public function getEndLine()
    {
        return $this->token_result[$this->getEndTokenId()]->getLine();
    }

}


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
 * @abstract
 */
abstract class EnviParserTokenWithScopeAndVisibility extends EnviParserTokenWithScope
{

    public function getVisibility()
    {
        $token_list = $this->token_result->getTokenList();

        for ($i = $this->id - 2; $i > $this->id - 7; $i -= 2) {
            if (isset($token_list[$i]) &&
               ($token_list[$i] instanceof EnviParserToken_PRIVATE ||
                $token_list[$i] instanceof EnviParserToken_PROTECTED ||
                $token_list[$i] instanceof EnviParserToken_PUBLIC)) {
                return strtolower(
                  $token_list[$i]->getTokenName()
                );
            }
            if (isset($token_list[$i]) &&
              !($token_list[$i] instanceof EnviParserToken_STATIC ||
                $token_list[$i] instanceof EnviParserToken_FINAL ||
                $token_list[$i] instanceof EnviParserToken_ABSTRACT)) {
                break;
            }
        }
    }

    public function getKeywords()
    {
        $keywords = array();
        $token_list = $this->token_result->getTokenList();

        for ($i = $this->id - 2; $i > $this->id - 7; $i -= 2) {
            if (isset($token_list[$i]) &&
               ($token_list[$i] instanceof EnviParserToken_PRIVATE ||
                $token_list[$i] instanceof EnviParserToken_PROTECTED ||
                $token_list[$i] instanceof EnviParserToken_PUBLIC)) {
                continue;
            }

            if (isset($token_list[$i]) &&
               ($token_list[$i] instanceof EnviParserToken_STATIC ||
                $token_list[$i] instanceof EnviParserToken_FINAL ||
                $token_list[$i] instanceof EnviParserToken_ABSTRACT)) {
                $keywords[] = strtolower(
                  $token_list[$i]->getTokenName()
                );
            }
        }
        return join(',', $keywords);
    }

}


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
 * @abstract
 */
abstract class EnviParserToken_Includes extends EnviParserToken
{
    protected $name;
    protected $type;

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        $token_list = $this->token_result->getTokenList();

        if ($token_list[$this->id+2] instanceof EnviParserToken_CONSTANT_ENCAPSED_STRING) {
            $this->name = trim($token_list[$this->id+2], "'\"");
            $this->type = strtolower(
                $token_list[$this->id]->getTokenName()
            );
        }

        return $this->name;
    }

    public function getType()
    {
        $this->getName();
        return $this->type;
    }
}


class EnviParserToken_FUNCTION extends EnviParserTokenWithScopeAndVisibility
{
    protected $arguments;
    protected $code_route_coverage;
    protected $name;
    protected $method_name;
    protected $signature;
    protected $interface_token;

    /**
     * +-- メソッド名を返す
     *
     * @access      public
     * @return      string
     */
    public function getMethodName()
    {
        $interface_token = $this->getInterFaceToken();
        if ($interface_token instanceof EnviParserToken_INTERFACE) {
            return $interface_token->getName().'::'.$this->getName();
        }
        return $this->getName();
    }
    /* ----------------------------------------- */

    /**
     * +-- interfaceTokenを返す
     *
     * @access      public
     * @return      EnviParserToken_INTERFACE
     */
    public function getInterFaceToken()
    {
        if ($this->interface_token !== NULL) {
            return $this->interface_token;
        }
        $token_list          = $this->token_result->getTokenList();
        foreach ($token_list as $token) {
            if ($token instanceof EnviParserToken_INTERFACE) {
                $token->getFunctionList();
            }
        }

        if ($this->interface_token === NULL) {
            $this->interface_token = false;
        }

        return $this->interface_token;
    }
    /* ----------------------------------------- */

    /**
     * +-- 所属するinterfaceTokenをaddする
     *
     * @access      public
     * @param       EnviParserToken_INTERFACE $interface_token
     * @return      void
     */
    public function addInterFaceToken(EnviParserToken_INTERFACE $interface_token)
    {
        $this->interface_token = $interface_token;
    }
    /* ----------------------------------------- */

    public function getArguments()
    {
        if ($this->arguments !== NULL) {
            return $this->arguments;
        }

        $this->arguments = array();
        $token_list          = $this->token_result->getTokenList();
        $type_hint        = NULL;

        // Search for first token inside brackets
        $i = $this->id + 2;
        while (!$token_list[$i-1] instanceof EnviParserToken_OPEN_BRACKET) {
            $i++;
        }
        $is_equal = false;
        while (!$token_list[$i] instanceof EnviParserToken_CLOSE_BRACKET) {
            if (isset($last_param) && $is_equal) {
                $this->arguments[$last_param]['optional'] = (string)$token_list[$i];
            } elseif ($token_list[$i] instanceof EnviParserToken_STRING) {
                $type_hint = (string)$token_list[$i];
            } else if ($token_list[$i] instanceof EnviParserToken_VARIABLE) {
                $last_param = (string)$token_list[$i];
                $this->arguments[$last_param]['type_hint'] = $type_hint;
                $this->arguments[$last_param]['optional'] = NULL;
                $type_hint                             = NULL;
                $is_equal = false;
            } else if ($token_list[$i] instanceof EnviParserToken_AMPERSAND) {
            } else if ($token_list[$i] instanceof EnviParserToken_COMMA) {
                $is_equal = false;
            } else if ($token_list[$i] instanceof EnviParserToken_WHITESPACE) {
            } else if ($token_list[$i] instanceof EnviParserToken_EQUAL) {
                $is_equal = true;
            }

            $i++;
        }

        return $this->arguments;
    }

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        $token_list = $this->token_result->getTokenList();

        for ($i = $this->id + 1; $i < count($token_list); $i++) {
            if ($token_list[$i] instanceof EnviParserToken_STRING) {
                $this->name = (string)$token_list[$i];
                break;
            } elseif ($token_list[$i] instanceof EnviParserToken_AMPERSAND &&
                     $token_list[$i+1] instanceof EnviParserToken_STRING) {
                $this->name = (string)$token_list[$i+1];
                break;
            } elseif ($token_list[$i] instanceof EnviParserToken_OPEN_BRACKET) {
                $this->name = 'anonymous function';
                break;
            }
        }

        if ($this->name != 'anonymous function') {
            for ($i = $this->id; $i; --$i) {
                if ($token_list[$i] instanceof EnviParserToken_NAMESPACE) {
                    $this->name = $token_list[$i]->getName() . '\\' . $this->name;
                    break;
                }

                if ($token_list[$i] instanceof EnviParserToken_INTERFACE) {
                    break;
                }
            }
        }

        return $this->name;
    }

    public function getCodeRouteCoverage()
    {
        if ($this->code_route_coverage !== NULL) {
            return $this->code_route_coverage;
        }

        $this->code_route_coverage = 1;
        $end       = $this->getEndTokenId();
        $token_list    = $this->token_result->getTokenList();
        for ($i = $this->id; $i <= $end; $i++) {
            switch ($token_list[$i]->getTokenName()) {
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
                $this->code_route_coverage++;
            break;
            }
        }

        return $this->code_route_coverage;
    }

    public function getSignature()
    {
        if ($this->signature !== NULL) {
            return $this->signature;
        }

        if ($this->getName() == 'anonymous function') {
            $this->signature = 'anonymous function';
            $i               = $this->id + 1;
        } else {
            $this->signature = '';
            $i               = $this->id + 2;
        }

        $token_list = $this->token_result->getTokenList();

        while (isset($token_list[$i]) &&
               !$token_list[$i] instanceof EnviParserToken_OPEN_CURLY &&
               !$token_list[$i] instanceof EnviParserToken_SEMICOLON) {
            $this->signature .= $token_list[$i++];
        }

        $this->signature = trim($this->signature);

        return $this->signature;
    }
}

class EnviParserToken_INTERFACE extends EnviParserTokenWithScopeAndVisibility
{
    protected $interfaces;
    protected $function_list;
    protected $constant_list;

    public function getName()
    {
        return (string)$this->token_result[$this->id + 2];
    }

    public function hasParent()
    {
        return $this->token_result[$this->id + 4] instanceof EnviParserToken_EXTENDS;
    }

    public function getFunctionList()
    {
        if ($this->function_list !== NULL) {
            return $this->function_list;
        }
        $i             = $this->id;
        $end_token_id  = $this->getEndTokenId();
        $token_list    = $this->token_result->getTokenList();

        while (isset($token_list[$i]) && $end_token_id <= $i) {
            if ($token_list[$i] instanceof EnviParserToken_FUNCTION) {
                $token_list[$i]->addInterFaceToken($this);
                $this->function_list[$i] = $token_list[$i];
            }
            ++$i;
        }
        return $this->function_list;
    }

    public function getConstantList()
    {
        if ($this->constant_list !== NULL) {
            return $this->constant_list;
        }
        $i             = $this->id;
        $end_token_id  = $this->getEndTokenId();
        $token_list    = $this->token_result->getTokenList();

        while (isset($token_list[$i]) && $end_token_id <= $i) {
            if ($token_list[$i] instanceof EnviParserToken_CONST) {
                $token_list[$i]->addInterFaceToken($this);
                $this->constant_list[$i] = $token_list[$i];
            }
            ++$i;
        }


        return $this->constant_list;
    }

    public function getPackage()
    {
        $className  = $this->getName();
        $docComment = $this->getDocBlock();

        $result = array(
          'namespace'   => '',
          'fullPackage' => '',
          'category'    => '',
          'package'     => '',
          'subpackage'  => ''
        );

        for ($i = $this->id; $i; --$i) {
            if ($this->token_result[$i] instanceof EnviParserToken_NAMESPACE) {
                $result['namespace'] = $this->token_result[$i]->getName();
                break;
            }
        }

        if (preg_match('/@category[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['category'] = $matches[1];
        }

        if (preg_match('/@package[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['package']     = $matches[1];
            $result['fullPackage'] = $matches[1];
        }

        if (preg_match('/@subpackage[\s]+([\.\w]+)/', $docComment, $matches)) {
            $result['subpackage']   = $matches[1];
            $result['fullPackage'] .= '.' . $matches[1];
        }

        if (empty($result['fullPackage'])) {
            $result['fullPackage'] = $this->arrayToName(
              explode('_', str_replace('\\', '_', $className)), '.'
            );
        }

        return $result;
    }

    protected function arrayToName(array $parts, $join = '\\')
    {
        $result = '';

        if (count($parts) > 1) {
            array_pop($parts);

            $result = join($join, $parts);
        }

        return $result;
    }

    public function getParent()
    {
        if (!$this->hasParent()) {
            return FALSE;
        }

        $i         = $this->id + 6;
        $token_list    = $this->token_result->getTokenList();
        $className = (string)$token_list[$i];

        while (isset($token_list[$i+1]) &&
               !$token_list[$i+1] instanceof EnviParserToken_WHITESPACE) {
            $className .= (string)$token_list[++$i];
        }

        return $className;
    }

    public function hasInterfaces()
    {
        return (isset($this->token_result[$this->id + 4]) &&
                $this->token_result[$this->id + 4] instanceof EnviParserToken_IMPLEMENTS) ||
               (isset($this->token_result[$this->id + 8]) &&
                $this->token_result[$this->id + 8] instanceof EnviParserToken_IMPLEMENTS);
    }

    public function getInterfaces()
    {
        if ($this->interfaces !== NULL) {
            return $this->interfaces;
        }

        if (!$this->hasInterfaces()) {
            return ($this->interfaces = FALSE);
        }

        if ($this->token_result[$this->id + 4] instanceof EnviParserToken_IMPLEMENTS) {
            $i = $this->id + 3;
        } else {
            $i = $this->id + 7;
        }

        $token_list = $this->token_result->getTokenList();

        while (!$token_list[$i+1] instanceof EnviParserToken_OPEN_CURLY) {
            $i++;

            if ($token_list[$i] instanceof EnviParserToken_STRING) {
                $this->interfaces[] = (string)$token_list[$i];
            }
        }

        return $this->interfaces;
    }
}


class EnviParserToken_NAMESPACE extends EnviParserTokenWithScope
{
    public function getName()
    {
        $token_list    = $this->token_result->getTokenList();
        $namespace = (string)$token_list[$this->id+2];

        for ($i = $this->id + 3; ; $i += 2) {
            if (isset($token_list[$i]) &&
                $token_list[$i] instanceof EnviParserToken_NS_SEPARATOR) {
                $namespace .= '\\' . $token_list[$i+1];
            } else {
                break;
            }
        }

        return $namespace;
    }
}

class EnviParserToken_CONST extends EnviParserTokenWithDocBlock {
    protected $name;
    protected $value;
    protected $interface_token;


    /**
     * +-- 所属するinterfaceTokenをaddする
     *
     * @access      public
     * @param       EnviParserToken_INTERFACE $interface_token
     * @return      void
     */
    public function addInterFaceToken(EnviParserToken_INTERFACE $interface_token)
    {
        $this->interface_token = $interface_token;
    }
    /* ----------------------------------------- */

    public function getName()
    {
        if ($this->name !== NULL) {
            return $this->name;
        }

        $token_list = $this->token_result->getTokenList();
        $last_token_id = count($token_list);

        for ($i = $this->id + 1; $i < $last_token_id; $i++) {
            if ($token_list[$i] instanceof EnviParserToken_STRING) {
                $this->name = (string)$token_list[$i];
                break;
            } elseif ($token_list[$i] instanceof EnviParserToken_SEMICOLON) {
                $this->name = 'anonymous const';
                break;
            }
        }
        return $this->name;
    }

    public function getValue()
    {
        if ($this->value !== NULL) {
            return $this->value;
        }

        $token_list = $this->token_result->getTokenList();
        $last_token_id = count($token_list);
        for ($i = $this->id + 1; $i < $last_token_id; $i++) {
            if ($token_list[$i] instanceof EnviParserToken_EQUAL) {
                while (++$i < $last_token_id) {
                    if ($token_list[$i] instanceof EnviParserToken_SEMICOLON) {
                        break 2;
                    } elseif (!($token_list[$i] instanceof EnviParserToken_WHITESPACE)) {
                        $this->value .= (string)$token_list[$i];
                    }
                }

                break;
            } elseif ($token_list[$i] instanceof EnviParserToken_SEMICOLON) {
                $this->value = '';
                break;
            }
        }
        return $this->value;
    }
}

class EnviParserToken_REQUIRE_ONCE extends EnviParserToken_Includes {}
class EnviParserToken_REQUIRE extends EnviParserToken_Includes {}
class EnviParserToken_EVAL extends EnviParserToken {}
class EnviParserToken_INCLUDE_ONCE extends EnviParserToken_Includes {}
class EnviParserToken_INCLUDE extends EnviParserToken_Includes {}

class EnviParserToken_LOGICAL_OR extends EnviParserToken {}
class EnviParserToken_LOGICAL_XOR extends EnviParserToken {}
class EnviParserToken_LOGICAL_AND extends EnviParserToken {}
class EnviParserToken_PRINT extends EnviParserToken {}
class EnviParserToken_SR_EQUAL extends EnviParserToken {}
class EnviParserToken_SL_EQUAL extends EnviParserToken {}
class EnviParserToken_XOR_EQUAL extends EnviParserToken {}
class EnviParserToken_OR_EQUAL extends EnviParserToken {}
class EnviParserToken_AND_EQUAL extends EnviParserToken {}
class EnviParserToken_MOD_EQUAL extends EnviParserToken {}
class EnviParserToken_CONCAT_EQUAL extends EnviParserToken {}
class EnviParserToken_DIV_EQUAL extends EnviParserToken {}
class EnviParserToken_MUL_EQUAL extends EnviParserToken {}
class EnviParserToken_MINUS_EQUAL extends EnviParserToken {}
class EnviParserToken_PLUS_EQUAL extends EnviParserToken {}
class EnviParserToken_BOOLEAN_OR extends EnviParserToken {}
class EnviParserToken_BOOLEAN_AND extends EnviParserToken {}
class EnviParserToken_IS_NOT_IDENTICAL extends EnviParserToken {}
class EnviParserToken_IS_IDENTICAL extends EnviParserToken {}
class EnviParserToken_IS_NOT_EQUAL extends EnviParserToken {}
class EnviParserToken_IS_EQUAL extends EnviParserToken {}
class EnviParserToken_IS_GREATER_OR_EQUAL extends EnviParserToken {}
class EnviParserToken_IS_SMALLER_OR_EQUAL extends EnviParserToken {}
class EnviParserToken_SR extends EnviParserToken {}
class EnviParserToken_SL extends EnviParserToken {}
class EnviParserToken_INSTANCEOF extends EnviParserToken {}
class EnviParserToken_UNSET_CAST extends EnviParserToken {}
class EnviParserToken_BOOL_CAST extends EnviParserToken {}
class EnviParserToken_OBJECT_CAST extends EnviParserToken {}
class EnviParserToken_ARRAY_CAST extends EnviParserToken {}
class EnviParserToken_STRING_CAST extends EnviParserToken {}
class EnviParserToken_DOUBLE_CAST extends EnviParserToken {}
class EnviParserToken_INT_CAST extends EnviParserToken {}
class EnviParserToken_DEC extends EnviParserToken {}
class EnviParserToken_INC extends EnviParserToken {}
class EnviParserToken_CLONE extends EnviParserToken {}
class EnviParserToken_NEW extends EnviParserToken {}
class EnviParserToken_EXIT extends EnviParserToken {}
class EnviParserToken_IF extends EnviParserToken {}
class EnviParserToken_ELSEIF extends EnviParserToken {}
class EnviParserToken_ELSE extends EnviParserToken {}
class EnviParserToken_ENDIF extends EnviParserToken {}
class EnviParserToken_LNUMBER extends EnviParserToken {}
class EnviParserToken_DNUMBER extends EnviParserToken {}
class EnviParserToken_STRING extends EnviParserToken {}
class EnviParserToken_STRING_VARNAME extends EnviParserToken {}
class EnviParserToken_VARIABLE extends EnviParserToken {}
class EnviParserToken_NUM_STRING extends EnviParserToken {}
class EnviParserToken_INLINE_HTML extends EnviParserToken {}
class EnviParserToken_CHARACTER extends EnviParserToken {}
class EnviParserToken_BAD_CHARACTER extends EnviParserToken {}
class EnviParserToken_ENCAPSED_AND_WHITESPACE extends EnviParserToken {}
class EnviParserToken_CONSTANT_ENCAPSED_STRING extends EnviParserToken {}
class EnviParserToken_ECHO extends EnviParserToken {}
class EnviParserToken_DO extends EnviParserToken {}
class EnviParserToken_WHILE extends EnviParserToken {}
class EnviParserToken_ENDWHILE extends EnviParserToken {}
class EnviParserToken_FOR extends EnviParserToken {}
class EnviParserToken_ENDFOR extends EnviParserToken {}
class EnviParserToken_FOREACH extends EnviParserToken {}
class EnviParserToken_ENDFOREACH extends EnviParserToken {}
class EnviParserToken_DECLARE extends EnviParserToken {}
class EnviParserToken_ENDDECLARE extends EnviParserToken {}
class EnviParserToken_AS extends EnviParserToken {}
class EnviParserToken_SWITCH extends EnviParserToken {}
class EnviParserToken_ENDSWITCH extends EnviParserToken {}
class EnviParserToken_CASE extends EnviParserToken {}
class EnviParserToken_DEFAULT extends EnviParserToken {}
class EnviParserToken_BREAK extends EnviParserToken {}
class EnviParserToken_CONTINUE extends EnviParserToken {}
class EnviParserToken_GOTO extends EnviParserToken {}
class EnviParserToken_CALLABLE extends EnviParserToken {}
class EnviParserToken_INSTEADOF extends EnviParserToken {}

class EnviParserToken_RETURN extends EnviParserToken {}
class EnviParserToken_YIELD extends EnviParserToken {}
class EnviParserToken_TRY extends EnviParserToken {}
class EnviParserToken_CATCH extends EnviParserToken {}
class EnviParserToken_FINALLY extends EnviParserToken {}
class EnviParserToken_THROW extends EnviParserToken {}
class EnviParserToken_USE extends EnviParserToken {}
class EnviParserToken_GLOBAL extends EnviParserToken {}
class EnviParserToken_PUBLIC extends EnviParserToken {}
class EnviParserToken_PROTECTED extends EnviParserToken {}
class EnviParserToken_PRIVATE extends EnviParserToken {}
class EnviParserToken_FINAL extends EnviParserToken {}
class EnviParserToken_ABSTRACT extends EnviParserToken {}
class EnviParserToken_STATIC extends EnviParserToken {}
class EnviParserToken_VAR extends EnviParserToken {}
class EnviParserToken_UNSET extends EnviParserToken {}
class EnviParserToken_ISSET extends EnviParserToken {}
class EnviParserToken_EMPTY extends EnviParserToken {}
class EnviParserToken_HALT_COMPILER extends EnviParserToken {}

class EnviParserToken_CLASS extends EnviParserToken_INTERFACE {}
class EnviParserToken_TRAIT extends EnviParserToken_INTERFACE {}
class EnviParserToken_CLASS_NAME_CONSTANT extends EnviParserToken {}
class EnviParserToken_EXTENDS extends EnviParserToken {}
class EnviParserToken_IMPLEMENTS extends EnviParserToken {}
class EnviParserToken_OBJECT_OPERATOR extends EnviParserToken {}
class EnviParserToken_DOUBLE_ARROW extends EnviParserToken {}
class EnviParserToken_LIST extends EnviParserToken {}
class EnviParserToken_ARRAY extends EnviParserToken {}
class EnviParserToken_CLASS_C extends EnviParserToken {}
class EnviParserToken_TRAIT_C extends EnviParserToken {}
class EnviParserToken_METHOD_C extends EnviParserToken {}
class EnviParserToken_FUNC_C extends EnviParserToken {}
class EnviParserToken_LINE extends EnviParserToken {}
class EnviParserToken_FILE extends EnviParserToken {}
class EnviParserToken_COMMENT extends EnviParserToken {}
class EnviParserToken_OPEN_TAG extends EnviParserToken {}
class EnviParserToken_OPEN_TAG_WITH_ECHO extends EnviParserToken {}
class EnviParserToken_CLOSE_TAG extends EnviParserToken {}
class EnviParserToken_WHITESPACE extends EnviParserToken {}
class EnviParserToken_START_HEREDOC extends EnviParserToken {}
class EnviParserToken_END_HEREDOC extends EnviParserToken {}
class EnviParserToken_DOLLAR_OPEN_CURLY_BRACES extends EnviParserToken {}
class EnviParserToken_CURLY_OPEN extends EnviParserToken {}
class EnviParserToken_PAAMAYIM_NEKUDOTAYIM extends EnviParserToken {}

class EnviParserToken_NS_C extends EnviParserToken {}
class EnviParserToken_DIR extends EnviParserToken {}
class EnviParserToken_NS_SEPARATOR extends EnviParserToken {}
class EnviParserToken_DOUBLE_COLON extends EnviParserToken {}
class EnviParserToken_OPEN_BRACKET extends EnviParserToken {}
class EnviParserToken_CLOSE_BRACKET extends EnviParserToken {}
class EnviParserToken_OPEN_SQUARE extends EnviParserToken {}
class EnviParserToken_CLOSE_SQUARE extends EnviParserToken {}
class EnviParserToken_OPEN_CURLY extends EnviParserToken {}
class EnviParserToken_CLOSE_CURLY extends EnviParserToken {}
class EnviParserToken_SEMICOLON extends EnviParserToken {}
class EnviParserToken_DOT extends EnviParserToken {}
class EnviParserToken_COMMA extends EnviParserToken {}
class EnviParserToken_EQUAL extends EnviParserToken {}
class EnviParserToken_LT extends EnviParserToken {}
class EnviParserToken_GT extends EnviParserToken {}
class EnviParserToken_PLUS extends EnviParserToken {}
class EnviParserToken_MINUS extends EnviParserToken {}
class EnviParserToken_MULT extends EnviParserToken {}
class EnviParserToken_DIV extends EnviParserToken {}
class EnviParserToken_QUESTION_MARK extends EnviParserToken {}
class EnviParserToken_EXCLAMATION_MARK extends EnviParserToken {}
class EnviParserToken_COLON extends EnviParserToken {}
class EnviParserToken_DOUBLE_QUOTES extends EnviParserToken {}
class EnviParserToken_AT extends EnviParserToken {}
class EnviParserToken_AMPERSAND extends EnviParserToken {}
class EnviParserToken_PERCENT extends EnviParserToken {}
class EnviParserToken_PIPE extends EnviParserToken {}
class EnviParserToken_DOLLAR extends EnviParserToken {}
class EnviParserToken_CARET extends EnviParserToken {}
class EnviParserToken_TILDE extends EnviParserToken {}
class EnviParserToken_BACKTICK extends EnviParserToken {}
