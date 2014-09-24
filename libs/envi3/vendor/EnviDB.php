<?php
/**
 * DB処理
 *
 * [PDO](http://jp2.php.net/manual/ja/book.pdo.php)をラップした、DBへのアクセスを提供するエクステンションです。
 *
 * エクステンション設定で設定された接続情報によって、接続先を設定できます。
 *
 * [データアクセサ](/c/man/v3/core/db/データアクセサ)や、[データオブジェクト](/c/man/v3/core/db/データオブジェクト)を使用することによって、プログラム側での、水平分割、垂直分割にも対応できます。
 *
 * `envi build-model`の使用方法や、クラスリファレンス以外の、詳しい使用方法は、[こちらを参照して下さい](/c/man/v3/core/db)
 *
 * PHP versions 5
 *
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage DB
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.1.0
 * @subpackage_main
 */

/**
 * DIから呼ばれるクラス
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage DB
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 3.1.0
 */
class EnviDBInstance
{
    private $_system_conf;
    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param  $config
     * @return void
     * @doc_ignore
     */
    public function __construct($config)
    {
        $this->_system_conf = $config;
    }
    /* ----------------------------------------- */

    /**
     * +-- instanceの取得
     *
     * デフォルトでは、
     * `extension()->DBI()->getInstance($db_key);`
     * という形でコールします。直接、クラスインスタンスを作成することはありません。
     *
     * また、build-modelで生成される、データアクセサは、必ず`extension()->DBI()->getInstance($db_key);`として、エクステンションにアクセスします。
     *
     * 別名を使用したい場合は、テンプレートを変更して下さい。
     *
     * @access public
     * @param string $db_key yamlで設定した接続キー
     * @return DBIBase
     */
    public function getInstance($db_key)
    {
        if (!isset($this->_system_conf[$db_key])) {
            throw new EnviException('DB: '.$db_key.'が存在してません。');
        }
        return EnviDB::getConnection($this->_system_conf[$db_key]['params'], $db_key);
    }
    /* ----------------------------------------- */
}

/**
 * pearDB風のインスタンスを提供するためのラッパー
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage DB
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviDB
{
    const AUTOQUERY_INSERT = 1;
    const AUTOQUERY_UPDATE = 2;
    const AUTOQUERY_REPLACE = 3;
    const AUTOQUERY_INSERT_IGNORE = 4;

    private static $connections = array();


    /**
     * +-- DSNパラメータから接続インスタンスを作成します。
     *
     * dsnパラメータ配列を利用して、接続インスタンスを作成します。
     * 同一リクエスト内であれば、インスタンスはプールされ、同じinstance_nameが指定された場合は、プールされた接続情報が返されます。
     *
     * 通常は、直接コールされることは無く、エクステンションから、`extension()->DBI->()->getInstance('instance_name')`形式でコールされ、DSNパラメータは、その設定ファイルに記述されます。
     *
     *
     *
     * @access public
     * @static
     * @param array $param 接続パラメータ
     * @param string $instance_name インスタンス名
     * @return EnviDBIBase
     * @see EnviDBInstance::getInstance()
     * @deprecated `extension()->DBI->()->getInstance()`を使用して下さい。
     * @see EnviDBInstance::getInstance()
     */
    public static function getConnection(array $param, $instance_name)
    {
        if (isset(self::$connections[$instance_name])) {
            return self::$connections[$instance_name];
        }
        if (is_array($param['dsn'])) {
            shuffle($param['dsn']);
            foreach ($param['dsn'] as $dsn) {
                $dbi = self::getNewConnection($dsn, $param);
                if ($dbi !== false) {
                    break;
                }
            }
        } else {
            $dbi = self::getNewConnection($param['dsn'], $param);
        }

        if ($param['instance_pool']) {
            self::$connections[$instance_name] = $dbi;
        }
        return $dbi;
    }
    /* ----------------------------------------- */


    private static function getNewConnection($dsn, array $param)
    {
        parse_str($dsn, $conf);
        try{
            $dbi = self::connect($conf, '', '', $param['connection_pool']);
            if (isset($param['initialize_query']) && !empty($param['initialize_query'])) {
                $dbi->query($param['initialize_query']);
            }
            return $dbi;
        } catch (exception $e) {
            throw $e;
            return false;
        }
    }

    /**
     * +-- EnviDBIBaseを取得する
     *
     * dsnパラメータおよび、ユーザーパスワードを指定して、DB インスタンスクラスを作成します。
     * `EnviDB::getConnection()`とは違いコールする度に新しい接続インスタンスを作成します。
     *
     * @access public
     * @static
     * @param string|array $dsn DSN
     * @param string|boolean $user DSNにユーザー名を記述しない場合、ユーザー名を指定する OPTIONAL:false
     * @param string|boolean $password DSNパスワード名を記述しない場合、ユーザー名を指定する OPTIONAL:false
     * @param boolean $is_pool コネクションプールの使用 OPTIONAL:false
     * @return EnviDBIBase
     * @deprecated `extension()->DBI->()->getInstance()`を使用して下さい。
     * @see EnviDBInstance::getInstance()
     */
    public static function connect($dsn, $user = false, $password = false, $is_pool = false)
    {
        if ($is_pool) {
            if ($user === false) {
                return new EnviDBIBase($dsn, '', '',  array(PDO::ATTR_PERSISTENT => true));
            } else {
                return new EnviDBIBase($dsn, $user, $password, array(PDO::ATTR_PERSISTENT => true));
            }
        } else {
            if ($user === false) {
                return new EnviDBIBase($dsn, '', '');
            } else {
                return new EnviDBIBase($dsn, $user, $password);
            }
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- $objがNULLもしくは、オブジェクト名にerrorと言う文字列が入っていた場合に、trueを返します
     *
     * PearDBのDB::isError();の代わりに用意されているメソッドです。
     *
     * 単純なGREPでの置き換えで、移行出来るように用意されていますが、
     * EnviDBでは、例外を使用するため、このメソッドを使用する意味はありません。
     *
     * @static
     * @param mixed $obj チェックする変数
     * @return boolean
     * @deprecated ダミークラスです。
     */
    public static function isError(&$obj)
    {
        if ($obj === NULL) {
            return true;
        } elseif (is_object($obj)) {
            return false;
        }
        return stripos(get_class($obj), 'error') !== false;
    }
    /* ----------------------------------------- */
}


/**
 * DBの処理
 *
 * @category   EnviMVC拡張
 * @package    EnviPHPが用意するエクステンション
 * @subpackage DB
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      Class available since Release 1.0.0
 */
class EnviDBIBase
{
    protected $default_fetch_mode;
    protected $PDO;
    public $last_query;
    public $last_parameters;
    public $is_tran;
    protected $tran_count = 0;

    /**
     * +-- PDOオブジェクトを返す
     *
     * DBに接続されたPDOオブジェクトを返します
     *
     * @access public
     * @return PDO
     */
    public function &PDO()
    {
        return $this->PDO;
    }
    /* ----------------------------------------- */

    /**
     * +-- スマートにクオートする
     *
     * $valueをクオートして返します。
     *
     * @access public
     * @param mixied $value クオートするデータ
     * @return string クオートされた文字列
     */
    public function quotesmart($value)
    {
        if (is_int($value)) {
            $pdo_param_type = PDO::PARAM_INT;
        } elseif(is_bool($value)){
            $pdo_param_type = PDO::PARAM_BOOL;
        } elseif ($value === NULL){
            $pdo_param_type = PDO::PARAM_NULL;
        } else {
            $pdo_param_type = PDO::PARAM_STR;
        }
        return $this->PDO->quote($value, $pdo_param_type);
    }
    /* ----------------------------------------- */

    /**
     * +-- 文字列をエスケープする
     *
     * EnviDBIBase::quotesmart()と等価です。
     *
     * @access public
     * @param mixied $str クオートするデータ
     * @return string クオートされた文字列
     * @see EnviDBIBase::quotesmart()
     */
    public function quote($str)
    {
        return $this->quotesmart($str);
    }
    /* ----------------------------------------- */

    /**
     * +-- インスタンス全体のフェッチモードを指定する
     *
     * フェッチモードは下記から選択できます。
     *
     *
     * PDO::FETCH_LAZY (integer)
     * : 取得する方法として、 結果セットが返すカラム名と同じ名前の変数を有するオブジェクトとして各行を返す方法を 指定します。 PDO::FETCH_LAZY は、アクセスされたものと同じ名前のオブジェクト変数を作成します。 PDOStatement::fetchAll() の中では使えません。
     *
     *
     * PDO::FETCH_ASSOC (integer)
     * : 結果セットの対応するカラム名にふられているものと同じキーを付けた 連想配列として各行を返す取得方法を指定します。 もし結果セットが複数のカラムを同名で含む場合、 PDO::FETCH_ASSOC はカラム名毎に 1 つの値のみ返します。
     *
     *
     * PDO::FETCH_NAMED (integer)
     * : 結果セットの対応するカラム名にふられているものと同じキーを付けた 連想配列として各行を返す取得方法を指定します。 もし結果セットが複数のカラムを同名で含む場合、 PDO::FETCH_NAMED はカラム名毎に値の配列を返します。
     *
     *
     * PDO::FETCH_NUM (integer)
     * : 結果セットの対応するカラム番号にふられているものと同じ添字を付けた 配列として各行を返す取得方法を指定します。番号は0から始まります。
     *
     *
     * PDO::FETCH_BOTH (integer)
     * : 結果セットと同じカラム名と0から始まるカラム番号を付けた配列として各行を返す 方法を指定します。
     *
     *
     * PDO::FETCH_OBJ (integer)
     * : 結果セットが返すカラム名と同じ名前のプロパティを有する オブジェクトとして各行を返す方法を指定します。
     *
     *
     * PDO::FETCH_BOUND (integer)
     * : 結果セットのカラムの値を PDOStatement::bindParam() または PDOStatement::bindColumn() メソッドでバインドされた PHP変数に代入し、TRUEを返すという取得方法を指定します。
     *
     *
     * PDO::FETCH_COLUMN (integer)
     * : 結果セットの次の行から指定された一つのカラムのみを返す取得方法を指定します。
     *
     *
     * PDO::FETCH_CLASS (integer)
     * : カラムをクラスのプロパティにマップしつつ、 指定されたクラスの新規インスタンスを返す取得方法を指定します。
     *
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     * 注意: 要求されたクラスにプロパティが存在しない場合は、マジックメソッド __set() がコールされます。
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * PDO::FETCH_INTO (integer)
     * : カラムをクラスのプロパティにマップしつつ、 指定されたクラスの既存のインスタンスを更新する取得方法を指定します。
     *
     *
     * PDO::FETCH_FUNC (integer)
     * : データをその場で扱う方法を完全にカスタマイズできるようにします (PDOStatement::fetchAll() の中でしか使えません)。
     *
     *
     * PDO::FETCH_GROUP (integer)
     * : 値で返すグループ。 PDO::FETCH_COLUMN あるいは PDO::FETCH_KEY_PAIR と組み合わせます。
     *
     *
     * PDO::FETCH_UNIQUE (integer)
     * : 一意な値だけを取得します。
     *
     *
     * PDO::FETCH_KEY_PAIR (integer)
     * : ふたつのカラムからなる結果を配列で取得します。最初のカラムの値がキー、二番目のカラムの内容が値となります。 PHP 5.2.3 以降で使用可能です。
     *
     *
     * PDO::FETCH_CLASSTYPE (integer)
     * : 最初のカラムの値からクラス名を決定します。
     *
     *
     * PDO::FETCH_SERIALIZE (integer)
     * : PDO::FETCH_INTO と同様ですが、 シリアライズした文字列としてオブジェクトを提供します。 PHP 5.1.0 以降で使用可能です。 PHP 5.3.0 以降、このフラグを設定した場合はコンストラクタが呼ばれないようになりました。
     *
     *
     * PDO::FETCH_PROPS_LATE (integer)
     * : コンストラクタを呼んでからプロパティを設定します。 PHP 5.2.0 以降で使用可能です。
     *
     *
     * @access public
     * @param integer $fetch_mode フェッチモード
     * @return void
     */
    public function setFetchMode($fetch_mode)
    {
        $this->default_fetch_mode = $fetch_mode;
    }
    /* ----------------------------------------- */

    /**
     * +-- 最後にインサートされたIDを返す
     *
     * 最後に挿入された行の ID、 あるいはシーケンスオブジェクトから次の値をを返します。
     * これは、構成しているドライバに依存します。例えば PDO_PGSQL の場合、name パラメータにシーケンスオブジェクト名を指定する必要があります。
     *
     * もし name パラメータにシーケンス名が指定されなかった場合、 PDO::lastInsertId() はデータベースに挿入された最後の行の行IDに相当する文字列を返します。
     *
     * もし name パラメータにシーケンス名が指定された場合、 PDO::lastInsertId() は指定されたシーケンスオブジェクトから取得した最後の値に相当する 文字列を返します。
     *
     * @access public
     * @param string  $name ID が返されるべきシーケンスオブジェクト名を指定します。 OPTIONAL:NULL
     * @return integer|boolean データベースに挿入された行IDに相当する文字列
     */
    public function lastInsertId($name = NULL)
    {
        return $this->PDO->lastInsertId($name);
    }
    /* ----------------------------------------- */


    /**
     * +-- SQLを実行する準備を行い、SQLオブジェクトを返す
     *
     * [PDO::prepare](http://jp1.php.net/manual/ja/pdo.prepare.php)へのラッパーです。
     *
     * [EnviDBIBase::execute()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/execute) メソッドによって実行される SQL ステートメントを準備します。
     * SQL ステートメントは、文が実行されるときに実際の値に置き換えられる 0 個もしくはそれ以上の名前 (:name) もしくは疑問符 (?) パラメータマークを含むことができます。
     * 名前と疑問符パラメータを同一 SQL ステートメント中で使用することはできません。
     * どちらか一方か、他のパラメータ形式を使用してください。
     * ユーザーの入力をバインドする際にはこれらのパラメータを使います。
     * ユーザーの入力を直接クエリに含めてはいけません。
     *
     * [EnviDBIBase::execute()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/execute) をコールする際には、 文に渡すパラメータにはそれぞれ固有のパラメータマークを設定する必要があります。
     * エミュレーションモードが有効になっていない限り、 ひとつのプリペアドステートメントの中で、同じ名前のパラメータマークを 複数使用することはできません。
     *
     * ~~~~~~~~~~~~~~~~~~~~~ {.alert .alert-warning}
     *   __注意：__
     * パラメータマーカーが表せるのは、データリテラルだけです。
     * リテラルの一部やキーワード、識別子、その他のクエリのパーツをパラメータにバインドすることはできません。
     * たとえば、SQL 文の IN() 句などで、 ひとつのパラメータに複数の値を割り当てることはできません。
     * ~~~~~~~~~~~~~~~~~~~~~
     *
     * 異なるパラメータを用いて複数回実行されるような文に対し EnviDBIBase::prepare() と [EnviDBIBase::execute()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/execute)  をコールすることで、 ドライバがクライアントまたはサーバー側にクエリプランやメタ情報を キャッシュさせるよう調整するため、 アプリケーションのパフォーマンスを最適化します。また、 パラメータに手動でクオートする必要がなくなるので SQL インジェクション攻撃から保護する助けになります。
     *
     * 元々この機能をサポートしていないドライバに対して プリペアドステートメントとバインドパラメータをエミュレートします。
     * このため、ある形式をサポートしているがその他の形式をサポートしていない ドライバの場合、名前もしくは疑問符形式のパラメータを他の適当な値に 書き換えることも可能です。
     *
     * @access public
     * @param string $statement sql
     * @param array $driver_options OPTIONAL:array
     * @return PDOStatement 結果オブジェクト
     */
    public function &prepare($statement, array $driver_options = array())
    {
        $pdos = $this->PDO->prepare($statement, $driver_options);
        $pdos->setFetchMode($this->default_fetch_mode);
        return $pdos;
    }
    /* ----------------------------------------- */



    /**
     * +-- プリペアドステートメントを実行する
     *
     * [PDO::prepare](http://jp1.php.net/manual/ja/pdostatement.execute.php)へのラッパーです。
     *
     * プリペアドステートメントを実行します。もし、プリペアドステートメントが パラメータマーカを含む場合、次のいずれかを行わなければなりません。
     *
     * * パラメータマーカに PHP 変数をバインドするため [PDOStatement::bindParam()](http://jp1.php.net/manual/ja/pdostatement.bindparam.php) をコールする。
     *   * 関連づけされたパラメータマーカがあれば、バインドされた変数は入力値を渡す もしくは出力値を受け取ります。
     * * あるいは、$driver_optionsに入力専用のパラメータ値の配列を渡す
     *
     * @access public
     * @param PDOStatement $pdos
     * @param array $driver_options OPTIONAL:array
     * @return PDOStatement 結果オブジェクト
     */
    public function &execute(PDOStatement $pdos, $driver_options = array())
    {
        $is_string_key = NULL;
        $last_parameters = array();
        $this->last_query = $pdos->queryString;
        foreach ($driver_options as $key => $value) {
            if ($is_string_key === NULL) {
                $is_string_key = !is_numeric($key);
            }
            if (is_int($value)) {
                $pdo_param_type = PDO::PARAM_INT;
            } elseif (is_bool($value)){
                $pdo_param_type = PDO::PARAM_BOOL;
            } elseif ($value === NULL){
                $pdo_param_type = PDO::PARAM_NULL;
            } else {
                $pdo_param_type = PDO::PARAM_STR;
            }
            if ($is_string_key) {
                $pdos->bindValue (':'.$key, $value, $pdo_param_type);
                $last_parameters[':'.$key] = $value;
            } else {
                $pdos->bindValue ($key+1, $value, $pdo_param_type);
                $last_parameters[$key] = $value;
            }
        }
        console()->stopwatch();
        $pdos->execute();
        $this->last_query = $pdos->queryString;
        $this->last_parameters = $last_parameters;
        console()->_queryLog($this);
        return $pdos;
    }
    /* ----------------------------------------- */

    /**
     * +-- SQL ステートメントを実行し、結果セットを PDOStatement オブジェクトとして返す
     *
     * EnviDBIBase::query() は、一回の関数コールの中で SQL ステートメントを実行し、このステートメントにより返された 結果セット (ある場合) を PDOStatement オブジェクトとして返します。
     *
     * [PDO::query](http://jp1.php.net/manual/ja/pdo.query.php)とは違い、$statementにバインドメカニズムを使用することが出来ます。
     *
     * 詳しい記述方法は、
     * [EnviDBIBase::execute()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/prepare)
     * を参考にして下さい。
     *
     * 複数回発行する必要があるステートメントの場合、[EnviDBIBase::execute()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/prepare)で PDOStatement ステートメントを準備し、
     *  EnviDBIBase::execute() でそのステートメントを 複数回発行する方がより良いパフォーマンスを得られると実感するでしょう。
     *
     * EnviDBIBase::query() を次にコールする前に 結果セット内の全てのデータを取得しない場合、そのコールは失敗します。
     * PDOStatement::closeCursor() をコールし、 次に EnviDBIBase::query() をコールする前に PDOStatement オブジェクトに関連付けられたリソースを解放してください。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param array $bind バインドする配列 OPTIONAL:array
     * @return PDOStatement 結果オブジェクト
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     */
    public function &query($statement, $bind = NULL)
    {
        if ($bind === NULL) {
            $this->last_query = $statement;
            console()->stopwatch();
            $pdos = $this->PDO->query($statement);
            $this->last_query = $pdos->queryString;
            console()->_queryLog($this);
            $pdos->setFetchMode($this->default_fetch_mode);
            $this->last_parameters = array();
        } else {
            $pdos = $this->execute($this->prepare($statement), $bind);
        }
        return $pdos;
    }
    /* ----------------------------------------- */

    /**
     * +-- クエリを実行し、全ての結果行を含む配列を返す
     *
     * SQLを実行し、全ての結果行を含む配列を返します。
     *
     * `EnviDBIBase::query($statement, $bind)->fetchAll($fetch_mode);`
     *
     * とほぼ等価の処理を行います。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param array $bind バインドする配列 OPTIONAL:array
     * @param integer $fetch_mode フェッチモード OPTIONAL:false
     * @return array 結果配列
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     * @see EnviDBIBase::query()
     * @see EnviDBIBase::setFetchMode()
     */
    public function getAll($statement, array $bind = array(), $fetch_mode = false)
    {
        $pdos = $this->query($statement, $bind);
        $fetch_mode = $fetch_mode === false ? $this->default_fetch_mode : $fetch_mode;
        return $pdos->fetchAll($fetch_mode);
    }
    /* ----------------------------------------- */

    /**
     * +-- クエリを実行し、結果の最初の一行を返す
     *
     * SQLを実行し、結果の最初の一行を返します。
     *
     * `EnviDBIBase::query($statement, $bind)->fetch($fetch_mode);`
     *
     * とほぼ等価の処理を行います。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param array $bind バインドする配列 OPTIONAL:array
     * @param integer $fetch_mode フェッチモード OPTIONAL:false
     * @return mixed fetch_modeに合わせた結果
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     * @see EnviDBIBase::query()
     * @see EnviDBIBase::setFetchMode()
     */
    public function getRow($statement, array $bind = array(), $fetch_mode = false)
    {
        $pdos = $this->query($statement, $bind);
        $fetch_mode = $fetch_mode === false ? $this->default_fetch_mode : $fetch_mode;
        return $pdos->fetch($fetch_mode);
    }
    /* ----------------------------------------- */


    /**
     * +-- クエリを実行し、結果の1カラム分だけ返す
     *
     * SQLを実行し、結果を1カラム分だけ返します
     *
     * `EnviDBIBase::query($statement, $bind)->fetchColumn(0);`
     *
     * とほぼ等価の処理を行います。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param array $bind バインドする配列 OPTIONAL:array
     * @return string SQLの実行結果
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     * @see EnviDBIBase::query()
     * @see EnviDBIBase::setFetchMode()
     */
    public function getOne($statement, array $bind = array())
    {
        $pdos = $this->query($statement, $bind);
        return $pdos->fetchColumn(0);
    }
    /* ----------------------------------------- */



    /**
     * +-- クエリを実行し、結果の特定列を配列で取得する
     *
     * SQLを実行し、結果の特定列を配列で取得します。
     * $colで指定する場所は、一番最初の列が0となります。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param integer $col 取得列 OPTIONAL:0
     * @param array $bind バインドする配列 OPTIONAL:array
     * @return array 結果配列
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     * @see EnviDBIBase::query()
     * @see EnviDBIBase::setFetchMode()
     */
    public function getCol($statement, $col = 0, array $bind = array())
    {
        $pdos = $this->query($statement, $bind);
        $res = array();
        while (($row = $pdos->fetchColumn($col)) !== false) {
            $res[] = $row;
        }
        return count($res) !== 0 ? $res : false;
    }
    /* ----------------------------------------- */

    /**
     * +-- クエリでマッチした数を返します
     *
     * SQLを実行し、結果行数を返します。
     *
     * `EnviDBIBase::query($statement, $bind)->rowCount();`
     *
     * とほぼ等価の処理です。
     *
     * @access public
     * @param string $statement 実行するSQL
     * @param array $bind バインドする配列 OPTIONAL:array
     * @return integer 結果行数
     * @see EnviDBIBase::prepare()
     * @see EnviDBIBase::execute()
     * @see EnviDBIBase::query()
     * @see EnviDBIBase::setFetchMode()
     */
    public function count($statement, array $bind = array())
    {
        $pdos = $this->query($statement, $bind);
        $fetch_mode = $fetch_mode === false ? $this->default_fetch_mode : $fetch_mode;
        return $pdos->rowCount();
    }
    /* ----------------------------------------- */


    /**
     * +-- Transaction開始
     *
     * オートコミットモードをオフにします。オートコミットモードがオフの間、 PDO オブジェクトを通じてデータベースに加えた変更は [EnviDBIBase::commit()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/commit)をコールするまでコミットされません。
     * [EnviDBIBase::rollback()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/rollback) をコールすると、 データベースへの全ての変更をロールバックし、 オートコミットモードに設定された接続を返します。
     *
     * MySQL を含むいくつかのデータベースでは、DROP TABLE や CREATE TABLE のようなデータベース定義言語 (DDL) ステートメントがトランザクション中に 発行される場合、暗黙的なコミットが自動的に発行されます。
     * この暗黙的なコミットにより、そのトランザクション境界で 他のあらゆる変更をロールバックすることができなくなるでしょう。
     *
     * また、簡易的にトランザクションのネストに対応しています。
     * 複数回EnviDBIBase::beginTransaction()した場合は、同じ数だけ、[EnviDBIBase::commit()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/commit) するまで、commitされません。
     * [EnviDBIBase::rollback()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/rollback)はネストを解除して、すべて元に戻します。
     *
     * @access public
     * @return boolean 成功すればtrue
     * @see EnviDBIBase::rollback()
     * @see EnviDBIBase::commit()
     */
    public function beginTransaction()
    {
        $this->tran_count++;
        if ($this->is_tran) {
            return true;
        }
        $this->is_tran = true;
        return $this->PDO->beginTransaction();
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクションのロールバック
     *
     * [EnviDBIBase::beginTransaction()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/beginTransaction) によって開始された 現在のトランザクションをロールバックします。
     * 有効なトランザクションがない場合は PDOException をスローします。
     *
     * データベースがオートコミットモードに設定されている場合、 この関数はトランザクションをロールバックした後に オートコミットモードを元に戻します。
     *
     * MySQL を含むいくつかのデータベースでは、DROP TABLE や CREATE TABLE のようなデータベース定義言語 (DDL) ステートメントがトランザクション中に 発行される場合、暗黙的なコミットが自動的に発行されます。
     * この暗黙的なコミットにより、そのトランザクション境界で 他のあらゆる変更をロールバックすることができなくなるでしょう。
     *
     *
     * @access public
     * @return boolean 成功すればtrue
     * @see EnviDBIBase::beginTransaction()
     * @see EnviDBIBase::commit()
     */
    public function rollback()
    {
        $this->is_tran = false;
        $this->tran_count = 0;
        return $this->PDO->rollback();
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクションのコミット
     *
     * トランザクションをコミットし、 次に [EnviDBIBase::beginTransaction()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/beginTransaction) で新たなトランザクションが開始されるまで、 データベース接続をオートコミットモードに戻します。
     *
     * @access public
     * @return boolean 成功すればtrue
     * @see EnviDBIBase::beginTransaction()
     * @see EnviDBIBase::rollback()
     */
    public function commit()
    {
        if (!$this->is_tran) {
            return;
        }
        if (--$this->tran_count === 0) {
            $this->is_tran = false;
            return $this->PDO->commit();
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- INSERT文やREPLACE文、UPDATE文を配列から実行する
     *
     * INSERT文やREPLACE文、UPDATE文を配列から実行し、結果オブジェクトを返します。
     *
     * @access public
     * @param string $table テーブル名
     * @param array $table_fields フィールドの配列
     * @param integer $mode OPTIONAL:EnviDB::AUTOQUERY_INSERT
     * @param string $where UPDATE時のWHERE文 OPTIONAL:false
     * @return PDOStatement 結果オブジェクト
     * @see EnviDBIBase::query()
     */
    public function autoExecute($table, array $table_fields, $mode = EnviDB::AUTOQUERY_INSERT, $where = false)
    {
        $sql = $this->buildManipSQL($table, $table_fields, $mode, $where);
        return $this->query($sql, $table_fields);
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクション中かどうかを返します。
     *
     *
     * inTransaction()と等価です。
     * 現在 [EnviDBIBase::beginTransaction()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/beginTransaction)でオートコミットがoffになっているかどうかを確認します。
     *
     * @access public
     * @return boolean トランザクション中ならTRUE
     * @see EnviDBIBase::beginTransaction()
     */
    public function isTran()
    {
        return $this->is_tran;
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクション中かどうかを返します
     *
     * isTran()と等価です。
     * 現在 [EnviDBIBase::beginTransaction()](/c/man/v3/reference/vendor/EnviDB/EnviDBIBase/beginTransaction)でオートコミットがoffになっているかどうかを確認します。
     *
     * @access public
     * @return boolean トランザクション中ならTRUE
     * @see EnviDBIBase::beginTransaction()
     */
    public function inTransaction()
    {
        return $this->is_tran;
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクションのネスト回数を返します
     *
     * 現在のトランザクションのネスト回数を返します。
     *
     * @access public
     * @return integer トランザクションのネスト回数
     * @see EnviDBIBase::beginTransaction()
     */
    public function transactionCount()
    {
        return $this->tran_count;
    }
    /* ----------------------------------------- */


    /**
     * +-- コネクションを解放し、DBへの接続を切断します
     *
     * コネクションを解放し、DBへの接続を切断します。
     * オブジェクト自体をunset()した場合も、コネクションが解放されます。通常はそちらの方法を選択して下さい。
     *
     * @access public
     * @return void
     * @deprecated EnviDBIBaseオブジェクト自体を破棄して下さい
     */
    public function disconnect()
    {
        if ($this->is_tran) {
            error_log('un closed Transaction.');
            $this->rollback();
        }
        unset($this->PDO);
    }
    /* ----------------------------------------- */

    private function buildManipSQL(&$table, &$table_fields, &$mode, &$where)
    {
        if (count($table_fields) == 0) {
            return $this->raiseError(DB_ERROR_NEED_MORE_DATA);
        }
        $first = true;
        $arr = array();
        if ($mode === EnviDB::AUTOQUERY_UPDATE) {
            $set = '';
            foreach ($table_fields as $key => $value) {
                $key = trim($key);
                if ($first) {
                    $first = false;
                } else {
                    $set .= ',';
                }
                $set .= '`'.$key.'` = ?';
                $arr[] = $value;
            }
            $sql = 'UPDATE '.$table.' SET '.$set.' ';
            if ($where) {
                $sql .= ' WHERE '.$where;
            }
            // table_fields置き換え
            $table_fields = $arr;
            return $sql;
        }
        $values = '';
        $names = '';
        foreach ($table_fields as $key => $value) {
            $key = trim($key);
            if ($first) {
                $first = false;
            } else {
                $names .= ',';
                $values .= ',';
            }
            $names .= '`'.$key.'`';
            $values .= '?';
            $arr[] = $value;
        }
        // table_fields置き換え
        $table_fields = $arr;
        switch ($mode) {
            case EnviDB::AUTOQUERY_INSERT:
                return 'INSERT INTO '.$table.' ('.$names.') VALUES ('.$values.')';
                break;
            case EnviDB::AUTOQUERY_INSERT_IGNORE:
                return 'INSERT IGNORE INTO '.$table.' ('.$names.') VALUES ('.$values.')';
                break;
            case EnviDB::AUTOQUERY_REPLACE:
                return 'REPLACE INTO '.$table.' ('.$names.') VALUES ('.$values.')';
                break;
            default:
                throw new PDOException;
                break;
        }
    }

    /**
     * +-- コンストラクタ
     *
     * @access public
     * @param  $dsn
     * @param  $username OPTIONAL:''
     * @param  $password OPTIONAL:''
     * @param  $driver_options OPTIONAL:array
     * @return void
     */
    public function __construct($dsn, $username = '', $password = '', $driver_options = array())
    {
        if (is_array($dsn)) {
            $username = $dsn['username'];
            $password = $dsn['password'];
            if (isset($dsn['dsn'])) {
                $dsn = $dsn['dsn'];
            } elseif ($dsn['phptype'] === 'mysql') {
                $dsn_key = $dsn['phptype'].':dbname='.$dsn['database'].';host='.$dsn['hostspec'];
                if (isset($dsn['charset']) && strlen($dsn['charset'])) {
                    $dsn_key .= ';charset='.$dsn['charset'];
                }
                if (isset($dsn['port']) && strlen($dsn['port'])) {
                    $dsn_key .= ';port='.$dsn['port'];
                }
                $dsn = $dsn_key;
            } else {
                throw new EnviException('phptype が mysql以外は、dsn={dsn format}&username={username}&password={password}形式で登録してください。');
            }
        }
        $this->PDO = new PDO($dsn, $username, $password, $driver_options);
        // エラーモードを修正する
        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->setFetchMode(PDO::FETCH_ASSOC);
    }
    /* ----------------------------------------- */

    /**
     * +-- デストラクタ
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }
    /* ----------------------------------------- */

    /**
     * +-- 最後に実行したSQLを取得する
     *
     * このオブジェクト内で最後に実行したSQLを取得します。
     *
     * DB自体の機能を利用する場合と違い、取得単位はオブジェクト単位となります。
     *
     * @access      public
     * @return      string 最後に実行したSQL
     */
    public function getLastQuery()
    {
        $sql     = $this->last_query;
        $values  = $this->last_parameters;
        $res = '';
        $prepare_sql = explode('?', $sql);
        if (isset($values[0])) {
            $values = array_values($values);
            foreach ($prepare_sql as $key => $query_front) {
                $res .= $query_front;
                if (isset($values[$key])) {
                    $res .= $this->quotesmart($values[$key]);
                }
            }
            return $res;
        }
        if (!count($values)) {
            return $sql;
        }
        foreach ($values as &$item) {
            $item = $this->quotesmart($item);
        }
        $sql = strtr($sql, $values);
        return $sql;
    }
    /* ----------------------------------------- */
}
