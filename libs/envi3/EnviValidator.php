<?php
/**
 * 雑多な入力検証を提供する入力検証クラス。
 *
 * EnviValidatorは、ユーザーの入力データを検証するための、高性能なインターフェイスを提供します。
 *
 * EnviValidatorは、シングルトンとして動作します。
 * つまり、EnviValidatorは`$EnviValidator = new EnviValidator;`のように、インスタンスを作成することが出来ません。
 *
 * インスタンスを受け取るには、下記のようにする必要があります。
 *
 * どちらの例も、EnviValidatorオブジェクトを返します。
 *
 * * 例1
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $EnviValidator =  EnviValidator::singleton();
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * * 例2
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $EnviValidator =  validator();
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 例2で表した方法が、よりシンプルなコードになるでしょう。
 *
 * 本マニュアルでは、例2の方法を使用して、記載していきます。
 *
 *
 * PHP versions 5
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Validator
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 * @subpackage_main
*/

/**
 * このクラスは、定数を提供するのみです。
 *
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Validator
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 * @codeCoverageIgnore
 */
class validator
{
    /**
     * timeバリデータで使用。時間のみのチェック
     *
     * @var         integer
     */
    const HOUR_ONLY        = 2;
    /**
     * timeバリデータで使用。時間と分のみのチェック
     *
     * @var         integer
     */
    const HOUR_TO_MINUTE   = 4;
    /**
     * timeバリデータで使用。時分秒のチェック
     *
     * @var         integer
     */
    const HOUR_TO_SECOND   = 6;

    /**
     * EnviValidate::autoPrepareで使用。POSTデータ
     *
     * @var         integer
     */
    const METHOD_POST      = 1;

    /**
     * EnviValidate::autoPrepareで使用。GETデータ
     *
     * @var         integer
     */
    const METHOD_GET       = 2;

    /**
     * +-- オブジェクト化させない
     *
     * @access      private
     * @return      void
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
    /* ----------------------------------------- */
}

/**
 * +-- EnviValidator
 *
 * @access public
 * @static
 * @return EnviValidator
 */
function validator()
{
    return EnviValidator::singleton();
}
/* ----------------------------------------- */

/**
 * 雑多な入力検証を提供する入力検証クラス。
 *
 *
 * EnviValidatorは、ユーザーの入力データを検証するための、高性能なインターフェイスを提供します。
 * よく使う入力検証の機能が集まっています。
 *
 * エラーがあれば、エラーオブジェクトを返しそうで無いなら、値を返すのが基本動作です。
 * エラークラスをバンドルしていますが、オリジナルの物を使用することもできます。
 *
 * EnviValidatorは、シングルトンとして動作します。
 * つまり、EnviValidatorは`$EnviValidator = new EnviValidator;`のように、インスタンスを作成することが出来ません。
 *
 * インスタンスを受け取るには、下記のようにする必要があります。
 *
 * どちらの例も、EnviValidatorオブジェクトを返します。
 *
 * * 例1
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $EnviValidator =  EnviValidator::singleton();
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * * 例2
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $EnviValidator =  validator();
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * 例2で表した方法が、よりシンプルなコードになるでしょう。
 *
 * 本マニュアルでは、例2の方法を使用して、記載していきます。
 *
 * バリデータのリスト
 * --------------------------------------------------------------
 * * Upper Version 3.3.1
 *   * 'equal'            => 値が同じであるかどうか
 *   * 'notequal'         => 値が違うかどうか
 *   * 'xdigit'           => 16 進数を表す文字かどうかを調べる
 *   * 'digit'            => すべての文字が数字かどうかを調べる
 *   * 'cntrl'            => すべての文字が制御文字であるかどうかを調べます。
 *   * 'graph'            => すべての文字が空白以外の印字可能な文字かどうかを調べる
 *   * 'lower'            => すべての文字が小文字かどうかを調べる
 *   * 'upper'            => すべての文字が大文字かどうかを調べる
 *   * 'print'            => すべての文字が印字可能な文字かどうかを調べる
 *   * 'punct'            => すべての文字が記号文字かどうかを調べる
 *   * 'space'            => すべての文字が空白文字かどうか調べる
 *   * 'notxdigit'         => 16 進数を表す文字でないかどうかを調べる
 *   * 'withoutdigit'      => 数字以外の文字が含まれているかどうかを調べる
 *   * 'withoutcntrl'      => 制御文字以外の文字が含まれているかどうかを調べる
 *   * 'withoutgraph'      => 改行,空白,タブのような印字も制御もできない文字が含まれるかどうか調べる
 *   * 'withoutlower'      => 小文字以外の文字が含まれるかどうかを調べる
 *   * 'withoutupper'      => 大文字以外の文字が含まれるかどうかを調べる
 *   * 'withoutprint'      => (改行、タブ,空白,制御文字などの)印字不可能な文字を含むかどうかを調べる
 *   * 'withoutpunct'      => 記号文字以外を含むかどうかを調べる
 *   * 'withoutspace'      => 空白文字以外を含むかどうかを調べる
 *   * 'withoutalphabet'              => アルファベット以外を含むかどうかを調べる
 *   * 'withoutalphabetornumber'      => アルファベットと数字以外を含むかどうかを調べる
 * * Upper Version 3.3.0
 *   * "number"           => 数値かどうか。小数点も許容します。(OPTION:なし)
 *   * "naturalnumber"    => 整数かどうか。0も許容します。(OPTION:なし)
 *   * "integer"          => 数字かどうか。小数点は使用出来ません(OPTION:なし)
 *   * "numbermax"        => 数値の最大(OPTION:最大数)
 *   * "numbermin"        => 数値の最小(OPTION:最少数)
 *   * "alphabet"         => アルファベットかどうか(OPTION:なし)
 *   * "alphabetornumber" => アルファベットもしくは数字かどうか(OPTION:なし)
 *   * "rome"             => ローマ字区域(半角英語+半角数字+半角記号)の文字列か(OPTION:半角化するか)
 *   * "maxlen"           => 最大文字数(OPTION:最大数)
 *   * "minlen"           => 最小文字数(OPTION:最小数)
 *   * "maxwidth"         => 最大文字幅。全角2半角1(OPTION:最大数)
 *   * "minwidth"         => 最小文字幅。全角2半角1(OPTION:最少数)
 *   * "blank"            => 空白かどうか(OPTION:なし)
 *   * "noblank"          => 空白でないか(OPTION:なし)
 *   * "nosubmit"         => 送信されているか(OPTION:なし)
 *   * "encoding"         => 文字のエンコーディング(OPTION:エンコード名)
 *   * "notags"           => タグが含まれていないか(OPTION:なし)
 *   * "depend"           => 機種依存文字が含まれていないか(OPTION:なし)
 *   * "mailformat"       => メールフォーマットの文字列になっているか(OPTION:なし)
 *   * "mailsimple"       => simpleなメールフォーマットチェック
 *   * "mail"             => ドメインも確認(OPTION:なし)
 *   * "hiragana"         => ひらがなか(OPTION:カタカナをひらがなに直すか)
 *   * "katakana"         => カタカナか(OPTION:ひらがなをカタカナに直すか)
 *   * "hfurigana"        => ひらがなのふりがなか(OPTION:カタカナをひらがなに直すか)
 *   * "kfurigana"        => カタカナのフリガナか(OPTION:ひらがなをカタカナに直すか)
 *   * "urlformat"        => URLフォーマットの文字列になっているか(OPTION:なし)
 *   * "url"              => ドメインも確認(OPTION:なし)
 *   * "telephone"        => 電話番号のフォーマットになっているか
 *   * "postcodeformat"   => 郵便番号のフォーマットになっているか(OPTION:なし)
 *   * "whitelist"        => ホワイトリストに含まれているか(OPTION:ホワイトリストの配列)
 *   * "date"             => YYYYMMDD形式のデータもしくは配列が、日付フォーマットになっているか(OPTION:array("year" => "年の配列キー","month" => "月の配列キー","day" => "日の配列キー"))
 *   * "time"             => 時間フォーマットになっているか(OPTION:時間のフォーマット。定数参照)
 *   * "array"            => 配列か(OPTION:なし)
 *   * "notarray"         => でないか配列か(OPTION:なし)
 *   * "arraykeyexists"   => 配列の中に指定されたキーが入っているか
 *   * "arraynumber"      => 配列の中身は全て数字か(OPTION:なし)
 *   * "arraynumbermax"   => 配列の中身の数字の合計最大(OPTION:最大数)
 *   * "arraynumbermin"   => 配列の中身の数字の合計最小(OPTION:最小数)
 *   * "arraycountmax"    => 配列の数の最大(OPTION:最大数)
 *   * "arraycountmin"    => 配列の数の最小(OPTION:最小数)
 *   * "arrayunique"      => 配列の値がuniqueかどうか(空文字列・NULLはuniqueチェックから省くかどうか)
 *   * "maxbr"            => 改行数の最大(OPTION:最大数)
 *   * "minbr"            => 改行数の最小(OPTION:最小数)
 *   * "dirpath"          => 存在するディレクトリパスか(OPTION:なし)
 *   * "file"             => 存在するファイルか(OPTION:なし)
 *   * "ereg"             => ルビー互換の正規表現にマッチするか(OPTION:正規表現)
 *   * "preg"             => パール互換の正規表現にマッチするか(OPTION:正規表現)
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Validator
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviValidator
{
    const VALIDATE_MODE     = 'mode';
    const VALIDATE_DATA     = 'data';
    const VALIDATOR         = 'EnviValidator';
    const VALIDATOR_CHAIN   = 'chain';
    const FORM_NAME         = 'form';


    private static $_error_object = NULL;

    /**
     * バリデーションチェインのフォーマット
     *
     * バリデーションチェインの既定値グループを登録します。
     * 何度も同じチェインを使用する場合などに便利です。
     * 直接配列で指定可能ですが、メソッドを呼び出して簡単に登録することもできます。
     *
     * @var array Format:
     * <pre>
     * array(
     *    "[フォーマット名]" => array(
     *       "[バリデーションの実行順序]" => array(
     *          "[バリデータ名(半角小文字)]"=> array(
     *             self::VALIDATE_MODE   => [バリデートモード],
     *             self::VALIDATOR_CHAIN => [エラーがおこっても次ぎのバリデータを起動するか],
     *             )
     *       )
     *    )
     * )
     * </pre>
     * @see setChainFormat();
     * @see getChainFormat();
     */
    public $chain_format = array();

    private static $singleton_obj;

    public $error_class  = 'ValidatorError';

    /**#@+
    * @access private
    */

    /**
     * フォームの値が空の場合割り振る値
     *
     * @var mixed
     */
    private $_empty_form_data = false;

    /**
     * Executeされるバリデーションのリスト
     *
     * @var array
     */
    private $_validation_list = array();


    /**
     * 使用可能な、バリデータのリスト
     *
     * @var array
     */
    private $_validator_list = array(

        'equal'             => '_typeEqual',
        'notequal'          => '_typeNotEqual',
        'xdigit'            => '_typeXdigit',
        'digit'             => '_typeDigit',
        'cntrl'             => '_typeCntrl',
        'graph'             => '_typeGraph',
        'lower'             => '_typeLower',
        'upper'             => '_typeUpper',
        'print'             => '_typePrint',
        'punct'             => '_typePunct',
        'space'             => '_typeSpace',
        'notxdigit'         => '_typeNotXdigit',
        'withoutdigit'      => '_typeWithoutDigit',
        'withoutcntrl'      => '_typeWithoutCntrl',
        'withoutgraph'      => '_typeWithoutGraph',
        'withoutlower'      => '_typeWithoutLower',
        'withoutupper'      => '_typeWithoutUpper',
        'withoutprint'      => '_typeWithoutPrint',
        'withoutpunct'      => '_typeWithoutPunct',
        'withoutspace'      => '_typeWithoutSpace',
        'withoutalphabet'              => '_typeWithoutAlphabet',
        'withoutalphabetornumber'      => '_typeWithoutAlphabetOrNumber',
        'number'           => '_typeNumber',
        'naturalnumber'    => '_typeNaturalNumber',
        'integer'          => '_typeInteger',
        'numbermax'        => '_typeNumberMax',
        'numbermin'        => '_typeNumberMin',
        'alphabet'         => '_typeAlphabet',
        'alphabetornumber' => '_typeAlphabetOrNumber',
        'rome'             => '_typeRome',
        'maxlen'           => '_typeMaxLen',
        'minlen'           => '_typeMinLen',
        'maxwidth'         => '_typeMaxWidth',
        'minwidth'         => '_typeMinWidth',
        'blank'            => '_typeBlank',
        'noblank'          => '_typeNoBlank',
        'nosubmit'         => '_typeNoSubmit',
        'encoding'         => '_typeEncoding',
        'notags'           => '_typeNoTags',
        'depend'           => '_typeDepend',
        'mailformat'       => '_typeMailFormat',
        'mailsimple'       => '_typeMailFormatSymple',
        'mail'             => '_typeMail',
        'hiragana'         => '_typeHiragana',
        'katakana'         => '_typeKatakana',
        'hfurigana'        => '_typeHFurigana',
        'kfurigana'        => '_typeKFurigana',
        'urlformat'        => '_typeUrlFormat',
        'url'              => '_typeUrl',
        'postcodeformat'   => '_typePostcodeFormat',
        'telephone'        => '_typeTelephoneFormat',
        'whitelist'        => '_typeWhiteList',
        'date'             => '_typeDate',
        'time'             => '_typeTime',
        'array'            => '_typeArray',
        'notarray'         => '_typeNotArray',
        'arraykeyexists'   => '_typeArrayKeyExists',
        'arraynumber'      => '_typeArrayNumber',
        'arraynumbermax'   => '_typeArrayNumberMax',
        'arraynumbermin'   => '_typeArrayNumberMin',
        'arraycountmax'    => '_typeArrayCountMax',
        'arraycountmin'    => '_typeArrayCountMin',
        'arrayunique'      => '_typeArrayUnique',
        'maxbr'            => '_typeMaxBr',
        'minbr'            => '_typeMinBr',
        'dirpath'          => '_typeDirPath',
        'file'             => '_typeFile',
        'ereg'             => '_typeEreg',
        'preg'             => '_typePreg',
        //'' => '',
    );

    /**
     * ユーザー定義バリデータのリスト
     *
     * @var array
     */
    private $_register_validators = array();

    /**
     * +-- エラーオブジェクトを取得する
     *
     * @access      public
     * @return      EnviValidatorError
     */
    public function &error()
    {
        if(!is_object(self::$_error_object)) {
            self::$_error_object = new $this->error_class();
        }
        return self::$_error_object;
    }
    /* ----------------------------------------- */

    /**#@-*/

    /**
     * コンストラクタ
     *
     * @return void
     */
    private function __construct()
    {
        $this->errors =& EnviRequest::getErrorsByRef();

    }

    public static function singleton()
    {
        if (!is_object(self::$singleton_obj)) {
            self::$singleton_obj = new EnviValidator();
        }

        return self::$singleton_obj;
    }


    /**
     * +-- 実行するバリデータチェイン名を指定して実行
     *
     * 入力検証を実行しエラークラス、もしくは、保証されたデータを受け取ります。
     * isError()メソッドでエラーのチェックを行えます。
     *
     * @param string|array $validation_name バリデートするフォームデータ名
     * @param bool $object_clean エラーオブジェクトを毎回空にするか
     * @return array,object
     * @see EnviValidator::isError()
     * @see EnviValidator::prepare()
     * @see EnviValidator::autoPrepare()
     */
    public function execute($validation_name, $object_clean = TRUE)
    {
        if (is_array($validation_name)) {
            $validation_name = key($validation_name);
        }
        if (!isset($this->_validation_list[$validation_name])) {
            throw new EnviException('Unknown validator chain selected');
        }

        $validation_datas =& $this->_validation_list[$validation_name];
        // error flag
        $is_error         = false;
        foreach ($validation_datas[self::VALIDATOR] as $validator_data) {
            $each = each ($validator_data);
            $validator = $each['key'];
            $option    = $each['value'];
            //foreach ($validator_data as $validator => $option) {
            //blankチェック以外のblankはすり抜ける
            $ck = $this->_validation($validator, $validation_datas[self::VALIDATE_DATA], $option[self::VALIDATE_MODE]);
            if (!$ck) {
                //エラーハンドリング
                $is_error = true;
                $this->_handleError(
                    $validation_name,
                    $validation_datas[self::FORM_NAME],
                    $validator, $validation_datas[self::VALIDATE_DATA],
                    $option[self::VALIDATE_MODE]
                );
                if($option[self::VALIDATOR_CHAIN] === false){
                    //連続バリデートが不可なら抜ける
                    break;
                }
                continue;
            }
            //}
        }

        //結果
        $res[$validation_name] =& $this->_validation_list[$validation_name][self::VALIDATE_DATA];
        if ($is_error) {
            //エラーの場合
            if ($object_clean === true) {
                $res = self::$_error_object;
                self::$_error_object = null;
            } else {
                return self::$_error_object;
            }
        }
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +--入力検証を全て実行しエラークラス、もしくは、保証されたデータを受け取ります。
     *
     * 入力検証を全て実行しエラークラス、もしくは、保証されたデータを受け取ります。
     * isError()メソッドでエラーのチェックを行えます。
     *
     * @see EnviValidator::isError()
     * @see EnviValidator::prepare()
     * @see EnviValidator::autoPrepare()
     * @return array|object 入力検証を実行しエラークラス、もしくは、保証されたデータが格納された配列を受け取ります。 validator()->isError()メソッドでエラーのチェックを行えます。
     */
    public function executeAll()
    {
        $is_error = false;
        $res = array();
        foreach ($this->_validation_list as $validator_name => $validation_datas) {
            if ($this->isError($this->execute($validator_name, false))){
                $is_error = true;
            }
            //結果
            $res[$validator_name] =& $this->_validation_list[$validator_name][self::VALIDATE_DATA];
        }

        if ($is_error) {
            //エラーが一つでも含まれている場合
            return self::$_error_object;
        }
        //正常終了
        return $res;

    }
    /* ----------------------------------------- */

    /**
     * +-- バリデーション設定を返します
     *
     * @access      public
     * @param       string $validator_name バリデーション名
     * @return      array
     * @see EnviValidator::prepare()
     * @see EnviValidator::autoPrepare()
     */
    public function getValidationSetting($validator_name)
    {
        return isset($this->_validation_list[$validator_name]) ? $this->_validation_list[$validator_name] : NULL;
    }
    /* ----------------------------------------- */


    /**
     * +-- バリデート機能定義(自動)
     *
     * 検証する入力データを自動で取得・定義して、新規にバリデータにかけます。
     * デフォルト取得されるデータは、$_POST・$_GETの両方からサーチします。
     * $post_only に
     * VM_METHOD_GET(GETのみ)
     * VM_METHOD_POST(POSTのみ)
     * VM_METHOD_POST|VM_METHOD_GET(両方/デフォルトはこれ)
     * を指定することで、受け取るデータに制限をかける事が出来ます。
     * ※上位互換のため、bool型もサポートしていますが、これは推奨されません。
     *
     *
     * $validation_name に配列を指定することで、表示用のフォーム名をつけることができます。
     * 表示用のフォーム名は、エラー発生時にエラークラスに渡されます。
     *
     * 例)
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * array("データ名" => "表示用のフォーム名")
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * @param string|array $validation_name バリデートするフォームデータ名。
     * @param string|array $validator バリデータ名,$this->getChainFormat()の結果
     * @param bool $validator_chain エラーがあった場合に確認処理を継続するか
     * @param bool $trim 入力検証データをtrimするかどうか
     * @param integer|boolean VM_METHOD_POST = POSTのみ VM_METHOD_GET = GETのみ VM_METHOD_POST|VM_METHOD_GET = POSTかGETのどちらか。
     * @param mix $validate_mode バリデータオプション
     * @see EnviValidator::prepare()
     * @return void
     */
    public function autoPrepare($validation_name, $validator, $validator_chain = true, $trim = false, $post_only = 3, $validate_mode = false)
    {
        $this->prepare($validation_name,
            $validator,
            $this->_getValidationData(is_array($validation_name) ? key($validation_name) : $validation_name, $trim, $post_only),
            $validator_chain,$validate_mode
        );
    }
    /* ----------------------------------------- */

    /**
     * +-- バリデート機能定義(手動)
     *
     * 検証する入力データを手動で取得・定義して、新規にバリデータにかけます。
     *
     * $validation_name に配列を指定することで、表示用のフォーム名をつけることができます。
     * 表示用のフォーム名は、エラー発生時にエラークラスに渡されます。
     *
     * 例)
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * array("データ名" => "表示用のフォーム名")
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * @param string|array $validation_name バリデートするフォームデータ名。
     * @param string|array $validator バリデータ名,$this->getChainFormat()の結果
     * @param mixed $validation_data バリデートするデータ
     * @param bool $validator_chain エラーがあった場合に確認処理を継続するか
     * @param mixed $validate_mode バリデータオプション
     * @see EnviValidator::autoPrepare()
     * @return void
     */
    public function prepare($validation_name, $validator, $validation_data, $validator_chain = true, $validate_mode = false)
    {
        if (is_array($validation_name)) {
            //配列が渡されたら、要素をバリデータ名に、値をフォーム名に
            $validation_name_key = key($validation_name);
            $this->_validation_list[$validation_name_key][self::FORM_NAME] = $validation_name[$validation_name_key];
            $validation_name = $validation_name_key;
        }else{
            $this->_validation_list[$validation_name][self::FORM_NAME] = $validation_name;
        }

        $this->_validation_list[$validation_name][self::VALIDATE_DATA] = $validation_data;
        if (is_array($validator)) {
            $this->_validation_list[$validation_name][self::VALIDATOR] = array_values($validator);
        } elseif(isset($this->_validator_list[strtolower($validator)]) ||
                isset($this->_register_validators[strtolower($validator)])) {
            $this->chain($validation_name, $validator, $validator_chain, $validate_mode);
        } else {
            unset($this->_validation_list[$validation_name]);
            throw new EnviException('Unknown validator selected');
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- ユーザー定義のバリデータを追加する
     *
     * execute()実行時に、
     * userFunction([検証されるデータ], [指定されたオプション]);
     * の形で、指定された関数にわたります。
     * ユーザー定義関数からは、正しい場合true・間違っている場合falseを返してください。
     *
     * @param string $validator_name 読み出しに使用するバリデータ名
     * @param string $function_name 関数名
     * @param string $error_message エラーメッセージ
     * @return void
     */
    public function registerValidators($validator_name, $function_name, $error_message = false)
    {
        if (is_string($function_name) && !function_exists($function_name)) {
            throw new EnviException('No exists function selected');
        }
        if (isset($this->_register_validators[strtolower($validator_name)])) {
            return $this->_register_validators[strtolower($validator_name)];
        }
        if ($error_message) {
            $this->error()->setUserErrorList($validator_name, $error_message);
        }

        $this->_register_validators[strtolower($validator_name)] = $function_name;
    }
    /* ----------------------------------------- */

    /**
     * +-- 検証に使用するバリデータを鎖状につなぎます。
     *
     * ひとつの入力データに対して、複数のバリデータを使いたい場合に使用します。
     * バリデータはchain()で呼び出された順番に、実行されます。
     *
     * @param string $validation_name バリデートするフォームデータ名
     * @param string $validator バリデータ名
     * @param bool $validator_chain エラーの場合につなげてバリデート処理を行うか
     * @param bool,string,int,array $validate_mode バリデータオプション
     * @return void
     * @see EnviValidator::prepare()
     * @see EnviValidator::autoPrepare()
     */
    public function chain($validation_name, $validator, $validator_chain = true, $validate_mode = false)
    {
        if (is_array($validation_name)) {
            $validation_name = key($validation_name);
        }
        if (isset($this->_validation_list[$validation_name])) {
            $this->_validation_list[$validation_name][self::VALIDATOR][][strtolower($validator)] = array(
                                                                self::VALIDATE_MODE   => $validate_mode,
                                                                self::VALIDATOR_CHAIN => $validator_chain,);
        } else {
            $this->autoPrepare($validation_name, $validator, $validator_chain, $validate_mode);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- チェインのフォーマットを作成します
     *
     * 入力検証のフォーマットを作成して、簡単に再利用可能にします。
     *
     * @param string $group フォーマットグループ名
     * @param string $validator バリデータ名
     * @param string,int $order チェインされる順番
     * @param bool $validator_chain エラーの場合につなげてバリデート処理を行うか
     * @param bool,string,int,array $validate_mode バリデータオプション
     * @see EnviValidator::getChainFormat()
     * @return void
     */
    public function setChainFormat($group, $validator, $order = 'AUTO', $validator_chain = true, $validate_mode = false)
    {
        if (is_numeric($order)) {
            $this->chain_format[$group][(int)$order][strtolower($validator)] = array(
                                                                self::VALIDATE_MODE   => $validate_mode,
                                                                self::VALIDATOR_CHAIN => $validator_chain,);
            ksort($this->chain_format[$group]);
            return $this->chain_format[$group];
        }
        $this->chain_format[$group][][strtolower($validator)] = array(
                                                            self::VALIDATE_MODE   => $validate_mode,
                                                            self::VALIDATOR_CHAIN => $validator_chain,);
        return $this->chain_format[$group];
    }
    /* ----------------------------------------- */

    /**
     * +-- チェインのフォーマットを返します
     *
     * setChainFormat()メソッドなどで指定された、フォーマットを返します。
     *
     * @param string $group フォーマットグループ名
     * @see setChainFormat();
     * @see prepare();
     * @see EnviValidator::autoPrepare();
     * @return array
     */
    public function getChainFormat($group)
    {
        return $this->chain_format[$group];
    }

    /**
     * +-- バリデータの使用をキャンセルする
     *
     * 一度チェインされたバリデータの使用をキャンセルします。
     * 全てのチェインを消す場合は、free()メソッドが高速です。
     *
     * @param string $validation_name バリデーション名
     * @param string $validator キャンセルするバリデータ
     * @see EnviValidator::free()
     * @return void
     */
    public function unchain($validation_name, $validator)
    {
        foreach ($this->_validation_list[$validation_name][self::VALIDATOR] as $key => $value) {
            if(isset($value[strtolower($validator)])){
                unset($this->_validation_list[$validation_name][self::VALIDATOR][$key]);
                break;
            }
        }
    }
    /* ----------------------------------------- */

    /**
     * +--バリデート結果がエラーかどうかを判断する
     *
     * バリデート結果がエラーかどうかを判断し、実行結果はエラーの場合に、TRUEを返します。
     *
     * @param object,string,int,array $result execute()・executeAllの結果
     * @return bool エラーかどうか
     * @see EnviValidator::executeAll()
     * @see EnviValidator::execute()
     */
    public function isError($result)
    {
        return $result instanceof $this->error_class;
    }
    /* ----------------------------------------- */

    /**
     * +-- 初期化
     *
     * ValidatorMagicの初期化をします。
     * 全てのエラー、チェインは空になります。
     *
     * @return void
     * @see EnviValidator::unchain()
     */
    public function free()
    {
        $this->_validation_list = array();
        self::$_error_object = null;
    }
    /* ----------------------------------------- */

    /**
     * +-- 簡単にバリデートする
     *
     * 入力データを簡単に検証します。
     * 正しければ、TRUE違っていれば、FALSEを返します。
     *
     * @param string $validator 使用するバリデータ
     * @param string,arrray $data バリデータにかけるデータ
     * @param string|array $option バリデータオプション
     * @return bool 正しいかどうか
     */
    public function validation($validator, $data, $option)
    {
        $validator = strtolower($validator);
        return $this->_validation($validator, $data, $option);
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーオブジェクトを直接指定する
     *
     * @param object $error_obj
     * @return void
     */
    public function setErrorObject($error_obj)
    {
        self::$_error_object =& $error_obj;
        $this->error_class = get_class($error_obj);
    }
    /* ----------------------------------------- */

    /**
     * +-- 空欄フォームの標準値を設定する
     *
     * @param mixed $empty_form_data フォームデータが空欄の場合に使用するデータ
     * @return void
     */
    public function setEmptyFormData($empty_form_data)
    {
        $this->_empty_form_data = $empty_form_data;
    }
    /* ----------------------------------------- */

    /**#@+
     * @access private
     * @return void
     */

    /**
     * バリデートする
     *
     * @param string $validator 使用するバリデータ
     * @param string,arrray $data バリデータにかけるデータ
     * @param string|array $option バリデータオプション
     * @access private
     * @return bool
     */
    protected function _validation(&$validator, &$data, &$option)
    {
        if (($validator !== 'blank' && $validator !== 'noblank' && $validator !== 'nosubmit' && !is_array($data)) ?
            $this->_typeNoBlank($data, false) : true) {
            if (isset($this->_validator_list[$validator])) {
                $method =& $this->_validator_list[$validator];
                $ck = $this->$method($data, $option);
            } elseif (isset($this->_register_validators[$validator])) {
                if ($this->_register_validators[$validator] instanceof Closure) {
                    $ck = $this->_register_validators[$validator]($data, $option);
                } else {
                    $ck = call_user_func_array($this->_register_validators[$validator], array($data, $option));
                }

            // @codeCoverageIgnoreStart
            } else {
                trigger_error('Unknown validator selected', E_USER_ERROR);
            }
            // @codeCoverageIgnoreEnd
        } else {
            return true;
        }
        return $ck;
    }

    /**
     * バリデートするデータを取得
     *
     * @param string $validation_name バリデーションチェイン名
     * @param bool $trim trimするか？
     * @param bool $post_only POSTのみ取得
     * @return mixed
     */
    protected function _getValidationData($validation_name, &$trim, &$post_only)
    {
        if (!strstr($validation_name, '[')) {
            if (($post_only === 3 || $post_only === 1 || is_bool($post_only)) &&
                (isset($_POST[$validation_name]) !== false ? $_POST[$validation_name] !== '' : false)) {
                $res = $_POST[$validation_name];
            } elseif (($post_only === 3 || $post_only === 2 ||  (is_bool($post_only) && !$post_only)) &&
                (isset($_GET[$validation_name]) !== false ? $_GET[$validation_name] !== '' : false)) {
                $res = $_GET[$validation_name];
            } else {
                $res = $this->_empty_form_data;
                return $res;
            }
        } else {
            $regs = array();
            $regs2 = array();
            preg_match_all("/([^\\[]+)/", $validation_name, $regs);
            preg_match_all("/\\[([^\\]]*)\\]/", $validation_name, $regs2);
            if (($post_only === 3 || $post_only === 1 ||  is_bool($post_only)) &&
                (isset($_POST[$regs[1][0]]) !== false ? $_POST[$regs[1][0]] !== '' : false)) {
                $res = $_POST[$regs[1][0]];
            } elseif (($post_only === 3 || $post_only === 2 ||  (is_bool($post_only) && !$post_only)) &&
                (isset($_GET[$regs[1][0]]) !== false ? $_GET[$regs[1][0]] !== '' : false)) {
                $res = $_GET[$regs[1][0]];
            } else {
                $res = $this->_empty_form_data;
                return $res;
            }

            foreach ($regs2[1] as $value) {
                if (isset($res[$value])) {
                    $res = $res[$value];
                } else {
                    $res = $this->_empty_form_data;
                    return $res;
                }
            }
        }

        if ($trim == true) {
            $res = $this->_trimmer($res);
        }
        return $res;
    }

    /**
     * 再帰的にtrimする
     *
     * @param string|array $validation_data trimするデータ
     *
     */
    protected function _trimmer($validation_data)
    {
        if (is_array($validation_data)) {
            foreach ($validation_data as $key => $value) {
                $validation_data[$key] = $this->_trimmer($validation_data[$key]);
            }
        } elseif (is_string($validation_data)) {
            $validation_data = trim($validation_data);
        }
        return $validation_data;
    }

    /**
     * エラー処理
     *
     * @param string $name バリデータチェイン名
     * @return void
     */
    protected function _handleError($name, $form_name, $validator, $data, $option)
    {
        if(!is_object(self::$_error_object)) {
            self::$_error_object = new $this->error_class();
        }
        self::$_error_object->setError($name, $form_name, $validator, $data, $option);
    }


    /**#@-*/

    /* ここからバリデータメソッド */

    /**#@+
     * @access private
     * @return bool
    */

    /**
     * +-- 値が同じであるかどうか
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $check_data
     * @return      void
     */
    protected function _typeEqual(&$ValidationData, $check_data)
    {
        return $ValidationData === $check_data;
    }
    /* ----------------------------------------- */

    /**
     * +-- 値が違うかどうか
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $check_data
     * @return      void
     */
    protected function _typeNotEqual(&$ValidationData, $check_data)
    {
        return $ValidationData !== $check_data;
    }
    /* ----------------------------------------- */




    /**
     * +-- 16 進数を表す文字でないかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeNotXdigit(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_xdigit($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- 数字以外の文字が含まれているかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutDigit(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_digit($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- 制御文字以外の文字が含まれているかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutCntrl(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_cntrl($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- 改行,空白,タブのような印字も制御もできない文字が含まれるかどうか調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutGraph(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_graph($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- 小文字以外の文字が含まれるかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutLower(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_lower($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- 大文字以外の文字が含まれるかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutUpper(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_upper($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- (改行、タブ、空白、制御文字などの)印字不可能な文字を含むかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutPrint(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_print($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- 記号文字以外を含むかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutPunct(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_punct($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- 空白文字以外を含むかどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeWithoutSpace(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_space($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- 16 進数を表す文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeXdigit(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_xdigit($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- すべての文字が数字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeDigit(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_digit($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- すべての文字が制御文字であるかどうかを調べます。
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeCntrl(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_cntrl($ValidationData);
    }
    /* ----------------------------------------- */



    /**
     * +-- すべての文字が空白以外の印字可能な文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeGraph(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_graph($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- すべての文字が小文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeLower(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_lower($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- すべての文字が大文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeUpper(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_upper($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- すべての文字が(改行、タブなどを含まない)印字可能な文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typePrint(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_print($ValidationData);
    }
    /* ----------------------------------------- */


    /**
     * +-- すべての文字が記号文字かどうかを調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typePunct(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_punct($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * +-- すべての文字が空白文字かどうか調べる
     *
     * @access      protected
     * @param       & $ValidationData
     * @param       var_text $dummy
     * @return      void
     */
    protected function _typeSpace(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_space($ValidationData);
    }
    /* ----------------------------------------- */

    /**
     * 数値を表す値かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNumber(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return is_numeric($ValidationData);
    }

    /**
     * X以下の数字かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeNumberMax(&$ValidationData, &$max)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_digit($ValidationData) ? $max >= $ValidationData : false;
    }

    /**
     * X以上の数字かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeNumberMin(&$ValidationData, &$min)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_digit($ValidationData) ? $min <= $ValidationData : false;
    }

    /**
     * 電話番号かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana $ValidationDataを半角英数字に変更
     */
    protected function _typeTelephoneFormat(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'a');
        }
        return preg_match('/^\+?[0-9][0-9\-]*[0-9]$/', $ValidationData) === 1;
    }


    /**
     * アルファベットかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeAlphabet(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_alpha($ValidationData);
    }

    /**
     * アルファベット以外の文字が含まれるかどうかを調べる
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeWithoutAlphabet(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_alpha($ValidationData);
    }

    /**
     * アルファベットもしくは数字かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeAlphabetOrNumber(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_alnum($ValidationData);
    }


    /**
     * アルファベットと数字以外の文字が入っているかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeWithoutAlphabetOrNumber(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return !ctype_alnum($ValidationData);
    }

    /**
     * ローマ字区域の文字列かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $convert 半角変換するか？
     */
    protected function _typeRome(&$ValidationData, $convert)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($convert) {
            $ValidationData = mb_convert_kana($ValidationData, 'a');
        }
        return ctype_graph($ValidationData);
    }

    /**
     * 整数かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeInteger(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if (ctype_digit($ValidationData)) {
            return true;
        }
        return preg_match('/^-?[1-9][0-9]*$/', $ValidationData) === 1;

    }

    /**
     * 自然数かどうか(0も許容)
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNaturalNumber(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return ctype_digit($ValidationData) && $ValidationData >= 0;

    }

    /**
     * 文字数が既定値以内かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeMaxLen(&$ValidationData, &$max)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return $max >= mb_strlen($ValidationData);
    }


    /**
     * 文字数が既定値以上かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeMinLen(&$ValidationData, &$min)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return $min <= mb_strlen($ValidationData);
    }

    /**
     * 文字はばが既定値以内かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeMaxWidth(&$ValidationData, &$max)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return $max >= mb_strwidth($ValidationData);
    }


    /**
     * 文字はばが既定値以上かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeMinWidth(&$ValidationData, &$min)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return $min <= mb_strwidth($ValidationData);
    }

    /**
     * 空欄(もしくは送信されていない)かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeBlank(&$ValidationData, $dummy)
    {
        if (!is_array($ValidationData)) {
            return strlen($ValidationData) === 0;
        }
        foreach ($ValidationData as $value) {
            $res = $this->_typeNoBlank($value, $dummy);
            if ($res) {
                return false;
            }
        }

        return true;
    }


    /**
     * 空欄(もしくは送信されていない)になっていないかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNoBlank(&$ValidationData, $dummy)
    {
        if (!is_array($ValidationData)) {
            return strlen($ValidationData) > 0;
        }
        foreach ($ValidationData as $value) {
            $res = $this->_typeNoBlank($value, $dummy);
            if (!$res) {
                return false;
            }
        }

        return true;
    }

    /**
     * 非送信になっていないかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNoSubmit(&$ValidationData, $dummy)
    {
        return $ValidationData !== false;
    }

    /**
     * 文字コードが既定値どおりになっているか？
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param strings $encoding エンコード名
     */
    protected function _typeEncoding(&$ValidationData, &$encoding)
    {
        if (!is_array($ValidationData)) {
            return mb_detect_encoding($ValidationData) == $encoding;
        }
        $ck = each($ValidationData);
        return mb_detect_encoding($ck[1]) == $encoding;

    }

    /**
     * タグが含まれていないか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNoTags(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return strip_tags($ValidationData) == $ValidationData;
    }

    /**
     * 機種依存文字
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 半角カナを全角カナに
     */
    protected function _typeDepend(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'KV');
        }
        $ie = mb_internal_encoding();
        mb_internal_encoding('EUC-JP');
        $_DEPEND_CHAR_PRE     = '(?-xism:(?<!\x8F))';
        $_DEPEND_CHAR_PATTERN = '[\xA9\xAA\xAB\xAC\xAD\xF9\xFA\xFB\xFC][\xA1-\xFE]';
        $_DEPEND_CHAR_POST    = '(?x-ism:(?=(?:[\xA1-\xFE][\xA1-\xFE])*(?:[\x00-\x7F\x8E\x8F]|\z)))';
        $REG_PATTERN  = '/'.$_DEPEND_CHAR_PRE.'('.$_DEPEND_CHAR_PATTERN.')'.$_DEPEND_CHAR_POST.'/';
        $res =  preg_match($REG_PATTERN, mb_convert_encoding($ValidationData, 'EUCJP-win', $ie)) === 0;
        mb_internal_encoding($ie);
        return $res;
    }

    /**
     * ひらがな
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角片カナ・半角片カナを全角平がなに変える
     */
    protected function _typeHiragana(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'HVc');
        }
        return mb_ereg('^[ぁ-ん]+$',$ValidationData) != false;
    }

    /**
     * カタカナ
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角平がな・半角片カナを全角片カナに変える
     */
    protected function _typeKatakana(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'KVC');
        }
        return mb_ereg('^[ァ-ヶ]+$',$ValidationData) != false;
    }

    /**
     * ひらがなのふりがな
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角片カナ・半角片カナを全角平がなに変える
     */
    protected function _typeHFurigana(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'HVc');
        }
        return mb_ereg('^[ぁ-んー]+$',$ValidationData) != false;
    }

    /**
     * カタカナのフリガナ
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角平がな・半角片カナを全角片カナに変える
     */
    protected function _typeKFurigana(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'KVC');
        }
        return mb_ereg('^[ァ-ヶー]+$',$ValidationData) != false;
    }

    /**
     * メールアドレスフォーマット
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     * @link http://www.din.or.jp/~ohzaki/perl.htm
     */
    protected function _typeMailFormat(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'a');
        }
        $REG_PATTERN = '/(?:[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff])|"[^\\\x80-\xff\n\015"]*(?:\\[^\x80-\xff][^\\\x80-\xff\n\015"]*)*")(?:\.(?:[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff])|"[^\\\x80-\xff\n\015"]*(?:\\[^\x80-\xff][^\\\x80-\xff\n\015"]*)*"))*@(?:[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\x80-\xff\n\015\[\]]|\\[^\x80-\xff])*\])(?:\.(?:[^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\x80-\xff\n\015\[\]]|\\[^\x80-\xff])*\]))+/';
        return preg_match($REG_PATTERN, $ValidationData) === 1;
    }

    /**
     * シンプルなメールアドレスフォーマット
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     */
    protected function _typeMailFormatSymple(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'a');
        }
        $REG_PATTERN = '/^[0-9a-zA-Z!#$%&=\-|_\/+\^\~][0-9a-zA-Z!#$%&=\-|_\/+.\^\~]*@[0-9a-zA-Z!#$%&=\-|_\/+\^\~]+\.[0-9a-zA-Z!#$%&=\-|_\/+.\^\~]*[a-zA-Z]$/';
        return preg_match($REG_PATTERN, $ValidationData)  === 1;
    }

    /**
     * URLフォーマット
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     * @link http://bakera.jp/hatomaru.aspx/yomoyama/perlnote
     */
    protected function _typeUrlFormat(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'a');
        }
        $REG_PATTERN = "/\b(?:https?|shttp|ftp):\/\/(?:(?:[-_.!~*'()a-zA-Z0-9;:&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*@)?(?:(?:[a-zA-Z0-9](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.)*[a-zA-Z](?:[-a-zA-Z0-9]*[a-zA-Z0-9])?\.?|[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)(?::[0-9]*)?(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)*(?:\/(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*(?:;(?:[-_.!~*'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)*)*)?(?:\?(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)?(?:#(?:[-_.!~*'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)?/";
        return preg_match($REG_PATTERN, $ValidationData)  === 1;
    }

    /**
     * 郵便番号フォーマット
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     */
    protected function _typePostcodeFormat(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }

        if ($kana == true) {
            $ValidationData = mb_convert_kana($ValidationData,'a');
        }
        $REG_PATTERN = "/^\d{3}-\d{4}$|^\d{3}-\d{2}$|^\d{3}$/";
        return preg_match($REG_PATTERN, $ValidationData)  === 1;
    }

    /**
     * メールアドレス
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     */
    protected function _typeMail(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($this->_typeMailFormatSymple($ValidationData, $kana)) {
            list(, $host) = explode('@', $ValidationData);
            if (gethostbyname($host) !== $host) {
                return true;
            }
        }
        return false;
    }

    /**
     * URL
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $kana 全角英数字を半角に変換
     */
    protected function _typeUrl(&$ValidationData, &$kana)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        if ($this->_typeUrlFormat($ValidationData, $kana)) {
            $a = parse_url($ValidationData);
            $host = $a['host'];
            if (gethostbyname($host) !== $host) {
                return true;
            }
        }
        return false;
    }

    /**
     * WhiteListに入っているデータかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param array $White_list ホワイトリスト
     */
    protected function _typeWhiteList(&$ValidationData, &$WhiteList)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return array_search($ValidationData, $WhiteList) !== false;

    }

    /**
     * 有効な日付かどうか
     *
     * 日付の有効性をチェックします。
     * YYYYMMDDフォーマットのデータの他に、配列を使用することが出来ます。
     * $DateListに、年月日に対応する配列名を入れる事で日付配列のチェックも出来ます。
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param array,bool $DateList array(month(月の配列名),day(日の配列名),year(年の配列名))
     */
    protected function _typeDate(&$ValidationData, &$DateList)
    {
        //プログラマレベルのエラー
        if (is_array($DateList)) {
            if (!isset($DateList['month'], $DateList['day'], $DateList['year'])) {
                throw new EnviException('Undefined option selected type year');
            }
        }

        if (!is_array($ValidationData)) {
            if (strlen($ValidationData) === 8 && ctype_digit($ValidationData)) {
                $month = (int)substr($ValidationData, 4, 2);
                $day   = (int)substr($ValidationData, 6, 2);
                $year  = (int)substr($ValidationData, 0, 4);
            } elseif (mb_ereg('^([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2})$', $ValidationData, $match)) {
                $month = (int)$match[2];
                $day   = (int)$match[3];
                $year  = (int)$match[1];
            } elseif (mb_ereg('^([0-9]{1,4})/([0-9]{1,2})/([0-9]{1,2})$', $ValidationData, $match)) {
                $month = (int)$match[2];
                $day   = (int)$match[3];
                $year  = (int)$match[1];
            }
        } elseif (is_array($ValidationData) && isset($ValidationData[$DateList['month']], $ValidationData[$DateList['day']], $ValidationData[$DateList['year']])) {
            $month = (int)$ValidationData[$DateList['month']];
            $day   = (int)$ValidationData[$DateList['day']];
            $year  = (int)$ValidationData[$DateList['year']];
        } else {
            return false;
        }
        return checkdate($month, $day, $year);
    }

    /**
     * 有効な時間かどうか
     *
     * 時間の有効性をチェックします。
     * HH || HHMM || HHMMSS フォーマットのデータの他に、配列を使用することが出来ます。
     * $DateListに、時分病秒に対応する配列名を入れる事で時間配列のチェックも出来ます。
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param array,int $TimeFormat array(hour(時の配列名),minute(分の配列名),second(秒の配列名)),VM_HOUR_ONLY,VM_HOUR_TO_MINUTE,VM_HOUR_TO_SECOND
     */
    protected function _typeTime(&$ValidationData, &$TimeFormat)
    {
        $format = array(2 => 2, 4 => 5, 6 => 8);
        //プログラマレベルのエラー
        if (is_array($TimeFormat)) {
            if (!isset($TimeFormat['hour'], $TimeFormat['minute'], $TimeFormat['second'])) {
                throw new EnviException('Undefined option selected type time');
            }
        } elseif (!isset($format[$TimeFormat])) {
            throw new EnviException('Undefined option selected type time');
        }

        if (!is_array($ValidationData) && !is_array($TimeFormat)) {
            $len = strlen($ValidationData);
            if($len === $TimeFormat){
                $hour   = (int)substr($ValidationData, 0, 2);
                $minute = ($len == 4 || $len == 6) ? (int)substr($ValidationData, 2, 2) : 0;
                $second = $len == 6 ? (int)substr($ValidationData, 4, 2) : 0;
            } elseif ($len === $format[$TimeFormat]){
                    $hour   = (int)substr($ValidationData, 0, 2);
                    $minute = ($len === 5 || $len === 8) ? (int)substr($ValidationData, 3, 2) : 0;
                    $second = $len === 8 ? (int)substr($ValidationData, 6, 2) : 0;
            } else {
                return false;
            }
        } elseif (is_array($ValidationData) && is_array($TimeFormat) &&
            isset($ValidationData[$TimeFormat['hour']], $ValidationData[$TimeFormat['minute']], $ValidationData[$TimeFormat['second']])) {
            $hour     = (int)$ValidationData[$TimeFormat['hour']];
            $minute   = (int)$ValidationData[$TimeFormat['minute']];
            $second   = (int)$ValidationData[$TimeFormat['second']];
        } else {
            return false;
        }
        return ($hour >= 0 && $minute >= 0 && $second >= 0 && $hour <= 24 && $minute < 60 && $second < 60);
    }

    /**
     * 配列かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeArray(&$ValidationData, $dummy)
    {
        return is_array($ValidationData);
    }

    /**
     * 配列で無いかかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeNotArray(&$ValidationData, $dummy)
    {
        return !is_array($ValidationData);
    }

    /**
     * 配列で送信されたデータで、指定されたキーが送信されているかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param array|string|int $keys キー
     */
    protected function _typeArrayKeyExists(&$ValidationData, $keys)
    {
        if (!is_array($ValidationData)) {
            return false;
        }
        if (is_array($keys)){
            foreach ($keys as $value) {
                if (!isset($ValidationData[$value])) {
                    return false;
                }
            }
            return true;
        }
        return isset($ValidationData[$keys]);
    }

    /**
     * 数字のみの配列かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param bool $dummy ダミー変数
     */
    protected function _typeArrayNumber(&$ValidationData, $dummy)
    {
        if ($this->_typeArray($ValidationData, $dummy)) {
            foreach ($ValidationData as $value) {
                if (!$this->_typeNumber($value, $dummy)) {
                    return false;
                }
            }
            return true;
        }
        //配列で無ければエラー
        return false;
    }

    /**
     * 数字のみの配列の合計が既定値以内かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeArrayNumberMax(&$ValidationData, &$max)
    {
        if ($this->_typeArrayNumber($ValidationData, $max) ? $max >= array_sum($ValidationData) : false) {
            return true;
        }
        return false;
    }

    /**
     * 数字のみの配列の合計が既定値以上かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeArrayNumberMin(&$ValidationData, &$min)
    {
        if ($this->_typeArrayNumber($ValidationData, $min) ? $min <= array_sum($ValidationData) : false) {
            return true;
        }
        return false;
    }

    /**
     * 配列の数が指定より少ないか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeArrayCountMax(&$ValidationData, &$max)
    {
        if(!$this->_typeArray($ValidationData, $max)){
            return false;
        }
        return count($ValidationData) <= $max;
    }

    /**
     * 配列の数が指定より多いか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeArrayCountMin(&$ValidationData, &$min)
    {
        if(!$this->_typeArray($ValidationData, $min)){
            return false;
        }
        return count($ValidationData) >= $min;
    }

    /**
     * 改行数が指定より少ないか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $max 最大値
     */
    protected function _typeMaxBr(&$ValidationData, &$max)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return count(explode("\n", $ValidationData, $max+2)) <= $max;
    }

    /**
     * 改行数が指定より多いか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param integer $min 最小値
     */
    protected function _typeMinBr(&$ValidationData, &$min)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return count(explode("\n", $ValidationData, $min+2)) >= $min;
    }

    /**
     * 指定されたファイルが、指定されたディレクトリ上にあるか
     *
     * @param strings $ValidationData 入力検証を行う変数
     * @param strings $ 最小値
     */
    protected function _typeDirPath(&$ValidationData, &$path)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return dirname(@realpath($ValidationData)) === realpath($path);
    }

    /**
     * 指定されたファイルが存在するかどうか
     *
     * @param strings $ValidationData 入力検証を行う変数
     * @param string $dummy ダミー変数
     */
    protected function _typeFile(&$ValidationData, $dummy)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        clearstatcache();
        return is_file($ValidationData);
    }

    /**
     * 正規表現にマッチするかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param string $regformat 正規表現文字列
     */
    protected function _typeEreg(&$ValidationData, &$regformat)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return mb_ereg($regformat, $ValidationData) != false;
    }

    /**
     * 正規表現にマッチするかどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param string $regformat 正規表現文字列
     */
    protected function _typePreg(&$ValidationData, &$regformat)
    {
        if (is_array($ValidationData)) {
            return false;
        }
        return preg_match($regformat, $ValidationData)  === 1;
    }

    /**
     * すべての値がユニークな配列かどうか
     *
     * @param strings,array $ValidationData 入力検証を行う変数
     * @param string $skip_blank 空の値をスキップして、ユニークチェックするかどうか (OPTIONAL)
     */
    protected function _typeArrayUnique($ValidationData, $skip_blank = true)
    {
        if (!is_array($ValidationData)) {
            return false;
        }
        if ($skip_blank) {
            $ValidationData = array_filter($ValidationData, 'strlen');
        } elseif (count(array_filter($ValidationData, 'strlen')) !== count($ValidationData)) {
            return false;
        }
        return count($ValidationData) === count(array_unique($ValidationData, SORT_REGULAR));
    }

}


/**
 * 入力検証エラークラス
 *
 * @category   フレームワーク基礎処理
 * @package    Envi3
 * @subpackage Validator
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 * @codeCoverageIgnore
 */
class ValidatorError
{
    /**#@+
    * @access private
    */
    protected $_error_message;
    protected $_error_list   = array();
    protected $_error_user_list   = array();
    protected $_error_default_list = array(
       'equal'      => '{form}が一致しません。',
       'notequal'  => '{form}が同じです。',
       'xdigit'   => '{form}は16進数で入力して下さい。',
       'digit'    => '{form}は全て数字で入力して下さい。',
       'cntrl'    => '{form}は制御文字以外が含まれています。',
       'graph'    => '{form}には空白、タブ、改行、制御文字を含めることが出来ません。',
       'lower'    => '{form}はすべて小文字である必要があります',
       'upper'    => '{form}はすべて大文字である必要があります',
       'print'    => '{form}は空白、タブ、改行、制御文字で入力する必要があります。',
       'punct'    => '{form}は全て記号になっていません。',
       'space'    => '{form}は空白文字ではありません。',

       'notxdigit'       => '{form}は16進数での入力はできません',
       'withoutdigit'    => '{form}には数字以外を含める必要があります。',
       'withoutcntrl'    => '{form}には制御文字以外の文字を含める必要があります。',
       'withoutgraph'    => '{form}には空白、タブ、改行、制御文字以外の文字を含める必要があります。',
       'withoutlower'    => '{form}には小文字以外の文字を含める必要があります。',
       'withoutupper'    => '{form}には大文字以外の文字を含める必要があります。',
       'withoutprint'    => '{form}には空白、タブ、改行以外の文字を含める必要があります。',
       'withoutpunct'    => '{form}には記号以外の文字を含める必要があります。',
       'withoutspace'    => '{form}には、空白以外の文字を含める必要があります。',

       'withoutalphabet'          => '{form}には、アルファベット以外の文字を含める必要があります。',
       'withoutalphabetornumber'  => '{form}には、英数字以外の文字を含める必要があります。',

        'number'           => '{form}は数字で入力してください。',
        'naturalnumber'    => '{form}は数値で入力してください。',
        'integer'          => '{form}は数値で入力してください。',
        'numbermax'        => '{form}は{option}以下で入力してください。',
        'numbermin'        => '{form}は{option}以上で入力してください。',
        'alphabet'         => '{form}はアルファベットで入力してください。',
        'alphabetornumber' => '{form}はアルファベットか数字で入力してください。',
        'rome'             => '{form}はローマ字で入力してください。',
        'maxlen'           => '{form}は{option}文字以下で入力してください。',
        'minlen'           => '{form}は{option}文字以上で入力してください。',
        'maxwidth'         => '{form}は半角{option}文字以下で入力してください。',
        'minwidth'         => '{form}は半角{option}文字以上で入力してください。',
        'noblank'          => '{form}を入力してください。',
        'nosubmit'         => '{form}を入力してください。',
        'encoding'         => '{form}を正しく送信してください。',
        'notags'           => '{form}にはタグを使用で来ません。',
        'depend'           => '{form}に機種依存文字が含まれています。',
        'mailformat'       => '{form}がメールアドレスになっていません。',
        'mailsimple'       => '{form}がメールアドレスになっていません。',
        'mail'             => '{form}は正しいメールアドレスではありません。',
        'hiragana'         => '{form}はひらがなで入力してください。',
        'katakana'         => '{form}はカタカナで入力してください。',
        'hfurigana'        => '{form}はひらがなで入力してください。',
        'kfurigana'        => '{form}はカタカナで入力してください。',
        'urlformat'        => '{form}がURLになっていません。',
        'url'              => '{form}のようなURLは存在しません。',
        'telephone'        => '{form}の入力は正しくありません。',
        'postcodeformat'   => '{form}のような郵便番号はありません。',
        'whitelist'        => '{form}を正しく入力してください。',
        'date'             => '{form}が正しい日付になっていません。',
        'time'             => '{form}が正しい時間になっていません。',
        'array'            => '{form}を正しく入力してください。',
        'arraynumber'      => '{form}が数字になっていません。',
        'arraynumbermax'   => '{form}は{option}以下で入力してください。',
        'arraynumbermin'   => '{form}は{option}以上で入力してください。',
        'arraycountmax'    => '{form}を正しく入力してください。',
        'arraycountmin'    => '{form}を正しく入力してください。',
        'arrayunique'      => '{form}が重複しています。',
        'maxbr'            => '{form}の改行が多すぎます。',
        'minbr'            => '{form}の改行が少なすぎます。',
        'dirpath'          => '不正なリクエストです。',
        'file'             => '不正なリクエストです。',
        'ereg'             => '{form}の入力は正しくありません。',
        'preg'             => '{form}の入力は正しくありません。',
    );
    /**#@-*/

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct()
    {
        $this->_error_message =& EnviRequest::getErrorsByRef();

        if (Envi::singleton()->getConfiguration('SYSTEM', 'use_i18n')) {
            $this->_error_user_list = Envi::singleton()->getI18n('validator');
        } elseif (Envi::singleton()->getConfiguration('VALIDATOR', 'error_list')) {
            $this->_error_user_list = Envi::singleton()->getConfiguration('VALIDATOR', 'error_list');
        }
    }

    /**
     * +-- デフォルトのエラーメッセージを変更します。
     *
     * @access      public
     * @param       string $validator 変更するバリデータ
     * @param       string $error_message エラーメッセージ
     * @return      void
     */
    public function setUserErrorList($validator, $error_message)
    {
        $this->_error_user_list[$validator] = $error_message;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーテキストメッセージを出す
     *
     * @access      public
     * @param       string $name
     * @param       string $validator
     * @return      string エラーテキストメッセージ
     */
    public function getErrorText($name, $validator)
    {
        if (isset($this->_error_list[$name][$validator])) {
            return $this->_error_list[$name][$validator];
        }

        if (isset($this->_error_user_list[$validator])) {
            return $this->_error_user_list[$validator];
        }

        if (isset($this->_error_default_list[$validator])) {
            return $this->_error_default_list[$validator];
        }

        if (isset($this->_error_user_list['default'])) {
            return $this->_error_user_list['default'];
        }

        if (isset($this->_error_default_list['default'])) {
            return $this->_error_default_list['default'];
        }
        return '';
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーのセット
     *
     * @param string $name バリデーションチェイン名
     * @param string $form_name フォーム名
     * @param string $validator バリデータ名
     * @param string|array $data データ
     * @param mixed $option 渡されたoption
     * @return void
     */
    public function setError($name, $form_name, $validator, $data, $option)
    {
        $mess = $this->getErrorText($name, $validator);
        $this->_error_message[$name][$validator] = str_replace(
            array(
                '{name}',
                '{form}',
                '{option}',
                '{data}',
            ),
            array(
                $name,
                $form_name,
                $this->_array2str($option),
                $this->_array2str($data),
            ),
            $mess
        );
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーメッセージのセット
     *
     * @param string $name バリデーションチェイン名
     * @param string $validator バリデータ名
     * @param string $message エラーメッセージ
     * @return void
     */
    public function setErrorMess($name, $validator, $message)
    {
        $this->_error_message[$name][$validator] = $message;
    }
    /* ----------------------------------------- */


    /**
     * +-- エラーメッセージのセット
     *
     * @param string $name バリデーションチェイン名
     * @param string $validator バリデータ名
     * @param string $message エラーメッセージ
     * @return void
     */
    public function setErrorList($name, $validator, $message)
    {
        $this->_error_list[$name][$validator] = $message;
    }
    /* ----------------------------------------- */

    /**
     * +-- エラーメッセージを受け取る
     *
     * @param string,boolean $name エラーを取りたいバリデーションチェイン名。空にすると全て取得
     * @return array
     */
    public function getErrorMessage($name = false)
    {
        return $name == false ? $this->_error_message : $this->_error_message[$name];
    }
    /* ----------------------------------------- */


    /**
     * +-- Errorの詳細を取得します。
     *
     * @return array エラー詳細配列の取得
     */
    public function getErrorDetail()
    {
        $i = 0;
        foreach ($this->_error_message as $key => $values) {
            foreach ($values as $id => $value) {
                $res['message'][$i] = $value;
                $res['code'][$i] = $this->_error_code[$key][$id];
                $res['keys'][$key][] = $i;
                $i++;
            }
        }
        $res['count'] = $i;
        return $res;
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列をstring型に強制変換
     *
     * @param array|string $arr 変換したい配列
     */
    public function _array2str($arr)
    {
        if (is_array($arr)) {
            return $this->_array2str(current($arr));
        }
        return $arr;
    }
    /* ----------------------------------------- */
}
