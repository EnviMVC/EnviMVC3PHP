<?php
/**
 * テスト用の処理
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 */




/**
 * テストAssert
 *
 * EnviTestCaseで継承されます。
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviTestAssert
{
    /**
     * +-- 配列にキーがあるかどうかを確認し、ない場合はエラー$message を報告します。
     *
     * このアサーションは、$arrayにキー$keyが存在するかどうかを確認し、存在しない場合はエラー$message を報告します。
     *
     * @access public
     * @param string $key 確認するキー
     * @param array $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertArrayHasKey($key, $array, $message = '')
    {
        if (!(!is_array($key) && is_array($array))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(array_key_exists($key, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列にキーがないかどうかを確認し、ある場合はエラー$message を報告します。
     *
     * このアサーションは、$arrayにキー$keyが存在しないかどうかを確認し、存在する場合はエラー$message を報告します。
     *
     * @access public
     * @param string $key 確認するキー
     * @param array $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertArrayNotHasKey($key, $array, $message = '')
    {
        if (!(!is_array($key) && is_array($array))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((array_key_exists($key, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $arrayに値$valueが存在するかどうかを確認します。 存在しない場合はエラー$message を報告します。
     *
     * このアサーションは、$arrayに値$valueが存在するかどうかを確認します。 存在しない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $value 存在確認する値
     * @param array $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertArrayHasValue($value, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(array_search($value, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- $arrayに値$valueが存在しないかどうかを確認します。 存在する場合はエラー$message を報告します。
     *
     * このアサーションは、$arrayに値$valueが存在するかどうかを確認します。 存在しない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $value 存在確認する値
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertArrayNotHasValue($value, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((array_search($value, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列かどうかを確認します。配列でない場合はエラー$message を報告します。
     *
     * このアサーションは、配列かどうかを確認します。配列でない場合はエラー$message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertArray($a, $message = '')
    {
        if (!(is_array($a))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- $class_name::$attribute_name() が存在しない場合にエラー $message を報告します。
     *
     * このアサーションは、$class_nameクラスに、$attribute_nameメソッドが存在するかを確認します。存在しない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $attribute_name メソッド名
     * @param mixed $class_name クラス名
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertClassHasAttribute($attribute_name, $class_name, $message = '')
    {
        $array = get_class_methods($class_name);
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(array_search($attribute_name, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }

    /**
     * +-- $class_name::attribute_name() が存在する場合にエラー $message を報告します。
     *
     * このアサーションは、$class_nameクラスに、$attribute_nameメソッドが存在するかを確認します。存在しない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $attribute_name メソッド名
     * @param mixed $class_name クラス名
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertClassNotHasAttribute($attribute_name, $class_name, $message = '')
    {
        $array = get_class_methods($class_name);
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((array_search($attribute_name, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }

    /**
     * +-- $arrayに値$valueが存在するかどうかを確認します。 存在しない場合はエラー $message を報告します。
     *
     * このアサーションは、$arrayに値$valueが存在するかどうかを確認します。 存在しない場合はエラー $message を報告します。
     * assertArrayHasValueとの違いは、$valueにstring以外使用でき無い点です。
     *
     * @access public
     * @param mixed $value 存在確認する値
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertContains($value, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(!is_array($value) && array_search($value, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }


    /**
     * +-- $arrayに値$valueが存在しないかどうかを確認します。 存在する場合はエラー $message を報告します。
     *
     * このアサーションは、$arrayに値$valueが存在しないかどうかを確認します。 存在する場合はエラー $message を報告します。
     * assertArrayNotHasValueとの違いは、$valueにstring以外使用でき無い点です。
     *
     * @access public
     * @param mixed $value 存在確認する値
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:''
     * @return boolean
     */
    public function assertNotContains($value, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((!is_array($value) && array_search($value, $array) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /**
     * +-- $arrayの中身の型が $type だけではない場合にエラー $message を報告します。
     *
     * このアサーションは、配列$arrayの中身の型が$typeとなっているかを確認します。$type以外が存在す場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $type 型の名前
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertContainsOnly($type, $array, $message = '')
    {
        if (!(is_array($array) && !is_array($value))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        foreach ($array as $value) {
            if (!(gettype($value) === $type)) {
                throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
            }
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $arrayの中身の型が $type だけの場合にエラー $message を報告します。
     *
     * このアサーションは、配列$arrayの中身の型が$type以外となっているかを確認します。$typeが存在す場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $type 型の名前
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotContainsOnly($type, $array, $message = '')
    {
        if (!(is_array($array) && !is_array($value))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        foreach ($array as $value) {
            if (!(gettype($value) === $type)) {
                throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
            }
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $array の要素数が $count でない場合にエラー $message を報告します。
     *
     * このアサーションは、配列$arrayの要素数が、$countであることを確認します。違う場合は、$message を報告します。
     *
     * @access public
     * @param mixed $count 配列の要素数
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertCount($count, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(count($array) === $count)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $array の要素数が $count の場合にエラー $message を報告します。
     *
     * このアサーションは、配列$arrayの要素数が、$countででないことを確認します。違う場合は、$message を報告します。
     *
     * @access public
     * @param mixed $count 配列の要素数
     * @param mixed $array 確認する配列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotCount($count, $array, $message = '')
    {
        if (!is_array($array)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((count($array) === $count)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 空であるかを確認し、空の場合は、エラー $message を報告します。
     *
     * このアサーションは空であるかを確認し、空の場合は、エラー $message を報告します。
     * 下記のような値が、空であると判断されます。
     * + "" (空文字列)
     * + 0 (整数 の 0)
     * + 0.0 (浮動小数点数の 0)
     * + "0" (文字列 の 0)
     * + NULL
     * + FALSE
     * + array() (空の配列)
     * + $var; (変数が宣言されているが、値が設定されていない)
     *
     * @access public
     * @param mixed $a 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertEmpty($a, $message = '')
    {
        if (!empty($a)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 空でないかを確認し、空の場合は、エラー $message を報告します。
     *
     * このアサーションは空でないかを確認し、空の場合は、エラー $message を報告します。
     * 下記のような値が、空であると判断されます。
     * + "" (空文字列)
     * + 0 (整数 の 0)
     * + 0.0 (浮動小数点数の 0)
     * + "0" (文字列 の 0)
     * + NULL
     * + FALSE
     * + array() (空の配列)
     * + $var; (変数が宣言されているが、値が設定されていない)
     *
     * @access public
     * @param mixed $a 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotEmpty($a, $message = '')
    {
        if (empty($a)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */



    /**
     * +-- 2 つの変数 $a と $b が等しいかどうかを確認し、等しくない場合にエラー $message を報告します。
     *
     * このアサーションは2 つの変数 $a と $b が等しいかどうかを確認し、等しくない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertEquals($a, $b, $message = '')
    {
        if (!($a == $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 2 つの変数 $a と $b が等しくないかどうかを確認し、等しい場合にエラー $message を報告します。
     *
     * このアサーションは2つの変数 $a と $b が等しくないかどうかを確認し、等しい場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotEquals($a, $b, $message = '')
    {
        if (($a == $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- falseかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、falseかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertFalse($a, $message = '')
    {
        if (!($a === false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 指定されたファイルが同じファイルかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、指定されたファイルが同じファイルかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param string $a 確認するファイル名
     * @param string $b 確認するファイル名
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertFileEquals($a, $b, $message = '')
    {
        if (!(file_get_contents($a) === file_get_contents($b))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 指定されたファイルが同じファイルでないかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、指定されたファイルが同じファイルでないかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param string $a 確認するファイル名
     * @param string $b 確認するファイル名
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertFileNotEquals($a, $b, $message = '')
    {
        if ((file_get_contents($a) === file_get_contents($b))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイルが存在するかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、ファイルが存在するかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認するファイルパス
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertFileExists($a, $message = '')
    {
        if (!(file_exists($a))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- ファイルが存在しないかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、ファイルが存在しないかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認するファイルパス
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotFileExists($a, $message = '')
    {
        if ((file_exists($a))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $a > $b かどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、$a > $b かどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertGreaterThan($a, $b, $message = '')
    {
        if (!($a > $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $a>=$bかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、$a>=$bかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertGreaterThanOrEqual($a, $b, $message = '')
    {
        if (!($a >= $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $actual が $expected のインスタンスでない場合にエラー $message を報告します。
     *
     * このアサーションは、$actual が $expected のインスタンスでない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $expected クラス名
     * @param mixed $actual オブジェクト
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertInstanceOf($expected, $actual, $message = '')
    {
        if (!is_object($actual) || !is_string($expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!($actual instanceof $expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- $actual が $expected のインスタンスの場合にエラー $message を報告します。
     *
     * このアサーションは、$actual が $expected のインスタンスの場合にエラー $message を報告します。
     *
     * @access public
     * @param string $expected クラス名
     * @param mixed $actual オブジェクト
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotInstanceOf($expected, $actual, $message = '')
    {
        if (!is_object($actual) || !is_string($expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (($actual instanceof $expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $actual の型が $expected でない場合にエラー $message を報告します。
     *
     * このアサーションは、$actual の型が $expected でない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $expected 型名
     * @param mixed $actual 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertInternalType($expected, $actual, $message = '')
    {
        if (!is_string($expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(gettype($actual) === $expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */
    /**
     * +-- $actual の型が $expected の場合にエラー $message を報告します。
     *
     * このアサーションは、$actual の型が $expected の場合にエラー $message を報告します。
     *
     * @access public
     * @param string $expected 型名
     * @param mixed $actual 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotInternalType($expected, $actual, $message = '')
    {
        if (!is_string($expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((gettype($actual) === $expected)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $a<$bかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * このアサーションは、$a<$bかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 調べる値
     * @param mixed $b 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertLessThan($a, $b, $message = '')
    {
        if (!($a < $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $a<=$bかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * このアサーションは、$a<=$bかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 調べる値
     * @param mixed $b 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertLessThanOrEqual($a, $b, $message = '')
    {
        if (!($a <= $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- Nullかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * このアサーションは、Nullかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNull($a, $message = '')
    {
        if (!($a === NULL)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- オブジェクト$objectにメソッド$attribute_name があるかを確認し、存在しない場合にエラー $message を報告します。
     *
     * このアサーションは、オブジェクト$objectにメソッド$attribute_name があるかを確認し、存在しない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $attribute_name メソッド名
     * @param mixed $object 調べるオブジェクト
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertObjectHasAttribute($attribute_name, $object, $message = '')
    {
        if (!is_string($attribute_name)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(method_exists($object, $attribute_name) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- オブジェクト$objectにメソッド$attribute_name がないかを確認し、存在する場合にエラー $message を報告します。
     *
     * このアサーションは、オブジェクト$objectにメソッド$attribute_name がないかを確認し、存在する場合にエラー $message を報告します。
     *
     * @access public
     * @param string $attribute_name メソッド名
     * @param mixed $object 調べるオブジェクト
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertObjectNotHasAttribute($attribute_name, $object, $message = '')
    {
        if (!is_string($attribute_name)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((method_exists($object, $attribute_name) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が正規表現 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が正規表現 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 正規表現
     * @param mixed $string 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertRegExp($pattern, $string, $message = '')
    {
        if (!is_string($pattern) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(mb_ereg($pattern, $string))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が正規表現 $pattern にマッチする場合にエラー $message を報告します。
     *
     * このアサーションは、$string が正規表現 $pattern にマッチする場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 正規表現
     * @param mixed $string 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotRegExp($format, $string, $message = '')
    {
        if (!is_string($format) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((mb_ereg($format, $string))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */



    /**
     * +-- $string が(Preg)正規表現 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が(Preg)正規表現 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 正規表現
     * @param mixed $string 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertPregMatch($pattern, $string, $message = '')
    {
        if (!is_string($pattern) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(preg_match($pattern, $string) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が(Preg)正規表現 $pattern にマッチする場合にエラー $message を報告します。
     *
     * このアサーションは、$string が(Preg)正規表現 $pattern にマッチする場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 正規表現
     * @param mixed $string 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotPregMatch($format, $string, $message = '')
    {
        if (!is_string($format) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((preg_match($format, $string) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */



    /**
     * +-- $string が書式文字列 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が書式文字列 $pattern にマッチしない場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 書式文字列
     * @param mixed $string 調べる値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringMatchesFormat($pattern, $string, $message = '')
    {
        if (!is_string($pattern) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(sprintf($pattern, $string) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が書式文字列 $pattern にマッチする場合にエラー $message を報告します。
     *
     * このアサーションは、$string が書式文字列 $pattern にマッチする場合にエラー $message を報告します。
     *
     * @access public
     * @param string $pattern 書式文字列
     * @param mixed $string
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringNotMatchesFormat($pattern, $string, $message = '')
    {
        if (!is_string($pattern) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((sprintf($pattern, $string) === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が $format_file の内容にマッチしない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $format_file の内容にマッチしない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $format_file ファイルパス
     * @param mixed $string 文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringMatchesFormatFile($format_file, $string, $message = '')
    {
        if (!is_string($format_file) || !is_string($string) || is_file($format_file)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(file_get_contents($format_file) === $string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- $string が $format_file の内容にマッチする場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $format_file の内容にマッチする場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $format_file ファイルパス
     * @param mixed $string 文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringNotMatchesFormatFile($format_file, $string, $message = '')
    {
        if (!is_string($format_file) || !is_string($string) || is_file($format_file)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((file_get_contents($format_file) === $string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 型と、値が同じかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * このアサーションは、型と、値が同じかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertSame($a, $b, $message = '')
    {
        if (!($a === $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- 型と、値が違うかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * このアサーションは、型と、値が違うかどうかを確認し、そうでない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param mixed $b 確認する値
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertNotSame($a, $b, $message = '')
    {
        if (($a === $b)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が $suffix で終わっていない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $suffix で終わっていない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $suffix 終端文字列
     * @param mixed $string 確認する文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringEndsWith($suffix, $string, $message = '')
    {
        if (!is_string($suffix) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(mb_strpos($string, $suffix) === mb_strlen($string))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }

    /**
     * +-- $string が $suffix で終わっている場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $suffix で終わっている場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $suffix 終端文字列
     * @param mixed $string 確認する文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringNotEndsWith($suffix, $string, $message = '')
    {
        if (!is_string($suffix) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((mb_strpos($string, $suffix) === mb_strlen($string))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }

    /**
     * +-- expected_file で指定したファイルの内容に $string が含まれない場合にエラー $message を報告します。
     *
     * このアサーションは、expected_file で指定したファイルの内容に $string が含まれない場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $expected_file ファイルパス
     * @param mixed $string 調べる文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringEqualsFile($expected_file, $string, $message = '')
    {
        if (!is_string($expected_file) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(mb_strpos($string, file_get_contents($expected_file)) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */


    /**
     * +-- expected_file で指定したファイルの内容に $string が含まれる場合にエラー $message を報告します。
     *
     * このアサーションは、expected_file で指定したファイルの内容に $string が含まれる場合にエラー $message を報告します。
     *
     * @access public
     * @param mixed $expected_file ファイルパス
     * @param mixed $string 調べる文字列
     * @param string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringNotEqualsFile($expected_file, $string, $message = '')
    {
        if (!is_string($expected_file) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if ((mb_strpos($string, file_get_contents($expected_file)) !== false)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }

    /**
     * +-- $string が $prefix で始まっていない場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $prefix で始まっていない場合にエラー $message を報告します。
     *
     * @access      public
     * @param       mixed $prefix 先頭文字列
     * @param       mixed $string 調べる文字列
     * @param       string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return      boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringStartsWith($prefix, $string, $message = '')
    {
        if (!is_string($prefix) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(mb_strpos($string, $prefix) === 0)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- $string が $prefix で始まっている場合にエラー $message を報告します。
     *
     * このアサーションは、$string が $prefix で始まっている場合にエラー $message を報告します。
     *
     * @access      public
     * @param       mixed $prefix 先頭文字列
     * @param       mixed $string 調べる文字列
     * @param       string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return      boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertStringNotStartsWith($prefix, $string, $message = '')
    {
        if (!is_string($prefix) || !is_string($string)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        if (!(mb_strpos($string, $prefix) === 0)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    public function assertTag()
    {

    }

    /**
     * +-- アサーションの追加
     *
     * @access public
     * @param mixed $value
     * @param EnviTestContain $contain
     * @param       string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return      boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertThat($value, EnviTestContain $contain, $message = '')
    {
        if (!($contain->execute($value))) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    /**
     * +-- trueかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * このアサーションは、trueかどうかを確認します。 そうでない場合はエラー $message を報告します。
     *
     * @access public
     * @param mixed $a 確認する値
     * @param       string $message OPTIONAL:'' 表示するエラーメッセージ
     * @return      boolean OKの場合trueを返します。 テストがNGの場合は、何も返しません。
     */
    public function assertTrue($a, $message = '')
    {
        if (!($a === true)) {
            throw new EnviTestException(__METHOD__.' '.$this->toString(func_get_args()));
        }
        return true;
    }
    /* ----------------------------------------- */

    public function free()
    {
    }

    /**
     * +-- 配列を文字列にする
     *
     * @access public
     * @param array $arr
     * @return string
     */
    public function toString($arr){
        $str = '';
        foreach ($arr as $val) {
            if (is_array($val)) {
                $str .= "{".$this->toString($val).'}';
                continue;
            }
            $val = (string)$val;
            $str .= ",{$val}";
        }
        return $str;
    }
    /* ----------------------------------------- */


}


/**
 * テストケースクラスで継承されるクラス
 *
 * @package Envi3
 * @subpackage EnviTest
 * @since 0.1
 * @author     Akito <akito-artisan@five-foxes.com>
 */
abstract class EnviTestCase extends EnviTestAssert
{
    public $get         = array();
    public $post        = array();
    public $headers     = array();
    public $cookie      = array();
    public $request_method = 'POST';
    private $request_url = '';

    public $system_conf;

    public function __construct()
    {
    }

    private function getContext($request_method, $post, $headers_arr, $cookie)
    {

        $headers_arr_sub = array();
        foreach ($headers_arr as $k => $value) {
            $headers_arr_sub[strtolower($k)] = trim($value);
        }
        $headers_arr = $headers_arr_sub;
        unset($headers_arr_sub);

        if (count($cookie)) {
            $headers_arr['cookie'] = "";
            foreach ($cookie as $k => $v) {
                $headers_arr['cookie'] = " $k=$v;";
            }
        }
        $headers = array();
        foreach ($headers_arr as $k => $value) {
            $headers[] = "{$k}: $value";
        }

        if ($request_method === 'GET') {
            $opts = array(
                'http' => array(
                    'method' => $request_method,
                    'header'=> join("\r\n", $headers)."\r\n"
                )
            );
        } else {
            $data_url       = http_build_query ($post);
            $data_length    = strlen ($data_url);
            $headers[] = 'Content-Length: '.$data_length;
            $opts = array(
                'http' => array(
                    'method' => $request_method,
                    'header'=> join("\r\n", $headers)."\r\n",
                    'content' => $data_url
                )
            );
        }
        return stream_context_create($opts);
    }


    abstract public function initialize();

    abstract public function shutdown();

    /**
     * +-- テストの実行
     *
     * @access private
     * @return string
     */
    final protected function emulateExecute($_request_method = false)
    {
        $get         = is_array($this->system_conf['parameter']['get']) ? $this->system_conf['parameter']['get'] : array();
        $post        = is_array($this->system_conf['parameter']['post']) ? $this->system_conf['parameter']['post'] : array();
        $headers     = is_array($this->system_conf['parameter']['headers']) ? $this->system_conf['parameter']['headers'] : array();
        $cookie      = is_array($this->system_conf['parameter']['cookie']) ? $this->system_conf['parameter']['cookie'] : array();

        $request_method    = $this->system_conf['parameter']['request_method'];

        $get    = array_merge($get, $this->get);
        $post   = array_merge($post, $this->post);
        $headers = array_merge($headers, $this->headers);
        $cookie = array_merge($cookie, $this->cookie);
        $request_method    = $_request_method ? $_request_method : $request_method;
        $context = $this->getContext($request_method, $post, $headers, $cookie);
        $query_string = count($get) ? '?'.http_build_query($get) : '';
        // echo $this->request_url.$query_string."\n";
        // $aa = debug_backtrace();
        // var_dump($aa[0]['file'].$aa[0]['line']);
        $contents = file_get_contents($this->request_url.$query_string, false, $context);
        return array($http_response_header, $contents);
    }


    final public function setModuleAction($module, $action)
    {
        $this->request_url = $this->system_conf['app']['url']."/{$module}/{$action}";
    }

}

/**
 * シナリオクラスで継承されるクラス
 *
 *
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviTestScenario
{
    public $system_conf;


    /**
     * +-- 実行するスイートのリストを返す
     *
     * @access public
     * @return array
     */
    public function execute()
    {
        return $this->getTestByDir(dirname($this->system_conf['scenario']['path']));
    }
    /* ----------------------------------------- */

    /**
     * +-- DIRECTORYの再帰検索
     *
     * @access public
     * @param mixed $dir_name
     * @param mixed $node OPTIONAL:0
     * @param mixed $arr OPTIONAL:array
     * @return array
     */
    public function getTestByDir($dir_name, $node = 0, $arr = array())
    {
        if (is_dir($dir_name)) {
            if ($dh = opendir($dir_name)) {
                while (($file = readdir($dh)) !== false) {
                    if (strpos($file, '.') === 0) {
                        continue;
                    }
                    if (is_dir($dir_name.DIRECTORY_SEPARATOR.$file)) {
                        if (mb_ereg('Test$', $file)) {
                            $arr = $this->getTestByDir($dir_name.DIRECTORY_SEPARATOR.$file, $node +1, $arr);
                        }
                        continue;
                    }
                    if (!mb_ereg('Test(\.class)?\.php$', $file)) {
                        continue;
                    }
                    if (is_file($dir_name.DIRECTORY_SEPARATOR.$file) && $node > 0) {
                        $arr[] = array(
                            'class_path' => $dir_name.DIRECTORY_SEPARATOR.$file,
                            'class_name' => str_replace(array('.class.php', '.php'), '', $file)
                        );
                    }
                }
                closedir($dh);
            }
        }
        return $arr;
    }
    /* ----------------------------------------- */

}



/**
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
abstract class EnviTestBlankMockBase
{
    public function __get($key)
    {
    }
    public function __set($key, $val)
    {
    }
    public function __call($func_name, $arg_arr)
    {
    }
}
/* ----------------------------------------- */

/**
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviMock
{

    /**
     * +-- コンストラクタ
     *
     * static クラスなので、privateコンストラクタ
     *
     * @access      private
     * @return      void
     */
    private function __construct()
    {

    }
    /* ----------------------------------------- */

    /**
     * +-- モックの取得
     *
     * @access      public
     * @static
     * @param       string $class_name モックを作成するクラス名
     * @return      EnviTestMockEditor
     */
    public static function mock($class_name)
    {
        if (!extension_loaded('runkit')) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $res = dl('runkit.dll');
            } else {
                $res = dl('runkit.so');
            }
            if ($res) {
                throw new Exception('please install runkit.http://pecl.php.net/package-changelog.php?package=runkit');
            }
        }

        $mock_editor = new EnviTestMockEditor($class_name);
        if (!class_exists($class_name, false)) {
            self::addMockClass($class_name);
        }
        return $mock_editor;
    }
    /* ----------------------------------------- */


    /**
     * +-- モッククラスの作成
     *
     * @access      public
     * @static
     * @param       string $class_name
     * @return      void
     */
    private static function addMockClass($class_name)
    {
        $cf = tmpfile();
        $file_path = stream_get_meta_data($cf);
        $file_path = $file_path['uri'];
        fwrite($cf, '<?php
        class '.$class_name.' extends EnviTestBlankMockBase
        {
        }'
        );
        include $file_path;
    }
    /* ----------------------------------------- */
}
/* ----------------------------------------- */

/**
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviTestMockEditor
{
    private $class_name;
    private $method_name;
    private $with;
    private $times;

    /**
     * +-- コンストラクタ
     *
     * @access      public
     * @param       any $cla_name
     * @return      void
     */
    public function __construct($class_name)
    {
        $this->class_name = $class_name;
    }
    /* ----------------------------------------- */


    /**
     * +-- メソッド一覧
     *
     * @access      public
     * @return      array
     */
    public function getMethods()
    {
        return get_class_methods($this->class_name);
    }
    /* ----------------------------------------- */

    /**
     * +-- 継承を解除する
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function emancipate()
    {
        runkit_class_emancipate($this->class_name);
        runkit_class_adopt($this->class_name , 'EnviTestBlankMockBase');
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- メソッドを削除する
     *
     * @access      public
     * @param       any $method
     * @return      EnviTestMockEditor
     */
    public function removeMethod($method)
    {
        if (method_exists($this->class_name, $method)) {
            runkit_method_remove($this->class_name, $method);
        }
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- 空のメソッドを定義する。既にメソッドが定義されている場合は、空のメソッドに置き換える
     *
     * @access      public
     * @param       any $method
     * @return      EnviTestMockEditor
     */
    public function blankMethod($method)
    {
        $this->removeMethod($method);
        runkit_method_add($this->class_name, $method, '', '');
        return $this;
    }
    /* ----------------------------------------- */


    /**
     * +-- 複数の空メソッドを定義する。既に定義済の場合は、空のメソッドに置き換える
     *
     * @access      public
     * @param       array $methods
     * @return      EnviTestMockEditor
     */
    public function blankMethodByArray(array $methods)
    {
        foreach ($methods as $method) {
            $this->blankMethod($method);
        }
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- 全ての定義済メソッドを空メソッドに置き換える
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function blankMethodAll()
    {
        foreach ($this->getMethods() as $method) {
            $this->blankMethod($method);
        }
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- 制約を追加するためのメソッドを定義します。
     *
     * @access      public
     * @param       string $method_name
     * @return      EnviTestMockEditor
     */
    public function shouldReceive($method_name)
    {
        $this->free();
        $this->method_name = $method_name;
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- このメソッドにのみ期待される引数のリストの制約を追加します。
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function with()
    {
        $this->with = func_get_args();
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- メソッドが二回以上呼び出されないことを定義します
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function once()
    {
        $this->times = 1;
        return $this;
    }
    /* ----------------------------------------- */


    /**
     * +-- メソッドが三回以上呼び出されないことを定義します
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function twice()
    {
        $this->times = 2;
        return $this;
    }
    /* ----------------------------------------- */


    /**
     * +-- メソッドが呼び出されないことを定義します
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function never()
    {
        $this->times = -1;
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- メソッドが$n回以上呼び出されないことを定義します。
     *
     * @access      public
     * @param       any $n
     * @return      EnviTestMockEditor
     */
    public function times($n)
    {
        $this->times = $n;
        return $this;
    }
    /* ----------------------------------------- */


    /**
     * +-- 期待メソッドが0回以上呼び出すことができますことを宣言します。そうでない場合に設定しない限り、これは、すべてのメソッドのデフォルトです。
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function zeroOrMoreTimes()
    {
        $this->times = false;
        return $this;
    }
    /* ----------------------------------------- */



    /**
     * +-- 戻り値を設定します。
     *
     * この後のモックメソッドの全ての呼び出しは常にこの宣言に指定された値を返すことに注意してください。
     *
     * @access      public
     * @param       any $res
     * @return      EnviTestMockEditor
     */
    public function andReturn($res)
    {
        $this->removeMethod($this->method_name);
        $method_script = $this->createMethodScript();
        $method_script .= 'return '.var_export($res, true).';';
        runkit_method_add($this->class_name, $this->method_name, '', $method_script);
        $this->free();
        return $this;
    }
    /* ----------------------------------------- */

    /**
     * +-- NULLを返すメソッドであると定義します
     *
     * @access      public
     * @return      EnviTestMockEditor
     */
    public function andReturnNull()
    {
        $this->removeMethod($this->method_name);
        $method_script = $this->createMethodScript();
        $method_script .= 'return NULL;';
        runkit_method_add($this->class_name, $this->method_name, '', $method_script);

        $this->free();
        return $this;
    }
    /* ----------------------------------------- */



    /**
     * +-- 呼び出された場合、このメソッドは、指定された例外オブジェクトをスローすることを宣言します。
     *
     * @access      public
     * @param       any $exception_class_name
     * @param       any $message OPTIONAL:''
     * @return      EnviTestMockEditor
     */
    public function andThrow($exception_class_name, $message = '')
    {
        if (is_object($exception_class_name)) {
            $message = $exception_class_name->getMessage();
            $exception_class_name = get_class($exception_class_name);
        }
        $this->removeMethod($this->method_name);
        $method_script = $this->createMethodScript();
        $method_script .= ' throw new '.$exception_class_name.'('.var_export($message, true).');';
        runkit_method_add($this->class_name, $this->method_name, '', $method_script);

        $this->free();
        return $this;
    }
    /* ----------------------------------------- */


    private function createMethodScript()
    {
        $method_script = "\n";

        if (is_integer($this->times)) {
            $method_script .= 'static $times = 0;
                if ($times < '.$this->times.') {
                    ++$times;
                } else {
                    $e = new EnviTestMockException("duplicate call method");
                    $e->setArgument('.$this->method_name.');
                    $e->setArgument(func_get_args());
                    $e->setArgument('.var_export($this->with, true).');
                    throw $e;
                }
                ';
        }

        if (is_array($this->with)) {
            $method_script .= 'if (func_get_args() !== '.var_export($this->with, true).') {
                $e = new EnviTestMockException("arguments Error");
                $e->setArgument('.$this->method_name.');
                $e->setArgument(func_get_args());
                $e->setArgument('.var_export($this->with, true).');
                throw $e;
            }
            ';
        }
        return $method_script;
    }



    private function free()
    {
        $this->method_name = '';
        $this->with = false;
        $this->once = false;
        return $this;
    }

}
/* ----------------------------------------- */

/**
 * @package    Envi3
 * @category   MVC
 * @subpackage UnitTest
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviTestMockException extends exception
{
   public $argument;
   public function setArgument($setter)
   {
       $this->argument[] = $setter;
   }
}
/* ----------------------------------------- */

