<?php
/**
 * DB処理
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage DB
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 3.1.0
 */

/**
 * DIから呼ばれるクラス
 *
 * @package    Envi3
 * @category   MVC
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
     * `extension()->DBI->getInstance($db_key);`
     * というような感じでコールします。直接、クラスインスタンスを作成することはありません。
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
 * @package    Envi3
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
     * +-- Enviから呼ばれるメソッド
     *
     * @access public
     * @static
     * @param array $param 接続パラメータ
     * @param string $instance_name インスタンス名
     * @return EnviDBIBase
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
     * @access public
     * @static
     * @param string|array $dsn
     * @param string|boolean $user OPTIONAL:false
     * @param string|boolean $password OPTIONAL:false
     * @param boolean $is_pool OPTIONAL:false
     * @return EnviDBIBase
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
     * +-- ダミー
     *
     * @static
     * @param & $obj
     * @return boolean
     */
    public static function isError(&$obj)
    {
        if ($obj === NULL) {
            return true;
        }
        return stripos(get_class($obj), 'error') !== false;
    }
    /* ----------------------------------------- */
}


/**
 * DBの処理
 *
 * @package    Envi3
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
     * @access public
     * @param mixied $str クオートするデータ
     * @return string クオートされた文字列
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
     * +-- PDO::prepare()へのラッパー
     *
     * @access public
     * @param string $statement sql
     * @param array $driver_options OPTIONAL:array
     * @return PDOStatement
     */
    public function &prepare($statement, array $driver_options = array())
    {
        $pdos = $this->PDO->prepare($statement, $driver_options);
        $pdos->setFetchMode($this->default_fetch_mode);
        return $pdos;
    }
    /* ----------------------------------------- */



    /**
     * +-- PDOS::execute()へのラッパー
     *
     * @access public
     * @param PDOStatement $pdos
     * @param array $driver_options OPTIONAL:array
     * @return PDOStatement
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
     * +-- バインドメカニズムを利用出来るようにした、query
     *
     * 第二引数に何も入れなければ、単にPDO::query()へのrapperとなります
     *
     * @access public
     * @param string $statement sql
     * @param array $bind bindする値 OPTIONAL:array
     * @return PDOStatement
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
     * +-- 実行したクエリの結果を配列ですべて返します
     *
     * @access public
     * @param string $statement SQL
     * @param array $bind OPTIONAL:array
     * @param integer $fetch_mode OPTIONAL:false
     * @return array
     */
    public function getAll($statement, array $bind = array(), $fetch_mode = false)
    {
        $pdos = $this->query($statement, $bind);
        $fetch_mode = $fetch_mode === false ? $this->default_fetch_mode : $fetch_mode;
        return $pdos->fetchAll($fetch_mode);
    }
    /* ----------------------------------------- */

    /**
     * +-- 実行したクエリの結果を配列で一行返します
     *
     * @access public
     * @param string $statement SQL
     * @param array $bind OPTIONAL:array
     * @param integer $fetch_mode OPTIONAL:false
     * @return array
     */
    public function getRow($statement, array $bind = array(), $fetch_mode = false)
    {
        $pdos = $this->query($statement, $bind);
        $fetch_mode = $fetch_mode === false ? $this->default_fetch_mode : $fetch_mode;
        return $pdos->fetch($fetch_mode);
    }
    /* ----------------------------------------- */


    /**
     * +-- 実行したクエリの結果を1カラム分だけ返します
     *
     * @access public
     * @param string $statement SQL
     * @param array $bind OPTIONAL:array
     * @return string
     */
    public function getOne($statement, array $bind = array())
    {
        $pdos = $this->query($statement, $bind);
        return $pdos->fetchColumn(0);
    }
    /* ----------------------------------------- */



    /**
     * +-- 実行したクエリの結果を配列で縦1行返します
     *
     * @access public
     * @param string $statement SQL
     * @param integer $col OPTIONAL:0
     * @param array $bind OPTIONAL:array
     * @return array
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
     * @access public
     * @param string $statement SQL
     * @param array $bind OPTIONAL:array
     * @return integer
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
     * @access public
     * @return boolean
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
     * @access public
     * @return boolean
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
     * @access public
     * @return boolean
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
     * @access public
     * @param string $table テーブル名
     * @param array $table_fields フィールドの配列
     * @param integer $mode OPTIONAL:EnviDB::AUTOQUERY_INSERT
     * @param string $where UPDATE時のWHERE文 OPTIONAL:false
     * @return PDOStatement
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
     * inTransaction()と等価です。
     *
     * @access public
     * @return boolean トランザクション中ならTRUE
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
     *
     * @access public
     * @return boolean トランザクション中ならTRUE
     */
    public function inTransaction()
    {
        return $this->is_tran;
    }
    /* ----------------------------------------- */

    /**
     * +-- トランザクションのネスト回数を返します
     *
     * @access public
     * @return integer トランザクションのネスト回数
     */
    public function transactionCount()
    {
        return $this->tran_count;
    }
    /* ----------------------------------------- */


    /**
     * +-- コネクションを解放し、DBへの接続を切断します
     *
     * @access public
     * @return void
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
            $dsn_key = $dsn['phptype'].':dbname='.$dsn['database'].';host='.$dsn['hostspec'];
            if (isset($dsn['charset']) && strlen($dsn['charset'])) {
                $dsn_key .= ';charset='.$dsn['charset'];
            }
            if (isset($dsn['port']) && strlen($dsn['port'])) {
                $dsn_key .= ';port='.$dsn['port'];
            }
            $dsn = $dsn_key;
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
     * @access      public
     * @return      string
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
