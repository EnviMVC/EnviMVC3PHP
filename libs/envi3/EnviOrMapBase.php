<?php
/**
 * アクティブレコードベース
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
 * @since      File available since Release 1.0.0
 */


/**
 * アクティブレコードベース
 *
 * @abstract
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
abstract class EnviOrMapBase
{
    protected $_from_hydrate, $to_save;
    protected $_is_modify  = true;
    protected $_is_hydrate = false;
    protected $suffix = '';
    protected $table_name,$pkeys;

    protected $insert_date;
    protected $update_date;

    protected $default_instance_name = 'default_master';

    /**
     * +-- insertならtrue,updateならfalseを返す
     *
     * @access      public
     * @return      boolean
     */
    public function isNew()
    {
        return $this->_is_hydrate === false;
    }
    /* ----------------------------------------- */


    /**
     * +-- insertならfalse,updateならtrueを返す
     *
     * @access      public
     * @return      boolean
     */
    public function isUpdate()
    {
        return $this->_is_hydrate;
    }
    /* ----------------------------------------- */

    /**
     * +-- テーブル名のサフィックスをセットする
     *
     * @access      public
     * @param       string $val
     * @return      void
     */
    public function setSuffix($val)
    {
        $this->suffix = '_'.$val;
    }
    /* ----------------------------------------- */

    /**
     * +-- テーブル名を取得する
     *
     * @access      public
     * @return      string テーブル名
     */
    public function getTableName()
    {
        return $this->table_name.$this->suffix;
    }
    /* ----------------------------------------- */

    /**
     * +-- 配列から、オブジェクトにセットする
     *
     * @access      public
     * @param       array $arr
     * @return      void
     */
    public function setByArray(array $arr)
    {
        foreach ($arr as $method => $val) {
            $method = 'set'.$this->pascalize($method);
            $this->$method($arr);
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- DBからセレクトしたデータをオブジェクトにセットする
     *
     * このMethodを使用してセットすると、saveがInsertではなくUpdateになります。
     *
     * @access public
     * @param array $arr
     * @return void
     */
    public function hydrate(array $arr)
    {
        $this->_is_modify = false;
        $this->_is_hydrate = true;
        $this->_from_hydrate = $arr;
        $this->to_save       = $arr;
    }
    /* ----------------------------------------- */

    /**
     * +-- データを配列にして返します
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return $this->to_save;
    }
    /* ----------------------------------------- */

    /**
     * +-- DBに保存します
     *
     * @access public
     * @param EnviDBIBase $con OPTIONAL:NULL
     * @return void
     */
    public function save(EnviDBIBase $con = NULL)
    {
        $table_name = $this->getTableName();
        $pkeys      = $this->pkeys;
        $dbi = $con ? $con : extension()->DBI()->getInstance($this->default_instance_name);

        if (!$this->_is_hydrate) {
            if ($this->insert_date) {
                $this->to_save[$this->insert_date] = date('Y-m-d H:i:s');
            }

            if ($this->update_date) {
                $this->to_save[$this->update_date] = date('Y-m-d H:i:s');
            }

            $dbi->autoExecute($table_name, $this->to_save, EnviDB::AUTOQUERY_INSERT);
            if (!isset($this->to_save[$pkeys[0]])) {
                $this->to_save[$pkeys[0]] = $dbi->lastInsertId();
            }
            $this->_from_hydrate = $this->to_save;
            $this->_is_modify = false;
            return true;
        }
        if (!$this->_is_modify) {
            return;
        }

        $and = '';
        $sql = '';
        foreach ($pkeys as $v) {
            $sql .= " {$and} {$v}=".$dbi->quoteSmart($this->_from_hydrate[$v]);
            $and = ' AND ';
        }
        if ($this->update_date) {
            $this->to_save[$this->update_date] = date('Y-m-d H:i:s');
        }

        $dbi->autoExecute($table_name, $this->to_save, EnviDB::AUTOQUERY_UPDATE, $sql);
        $this->_from_hydrate = $this->to_save;
        $this->_is_modify    = false;
    }
    /* ----------------------------------------- */

    /**
     * +-- データを削除する
     *
     * @access public
     * @param EnviDBIBase $con OPTIONAL:NULL
     * @return void
     */
    public function delete(EnviDBIBase $con = NULL)
    {
        $arr = array();
        $sql = "DELETE FROM {$this->getTableName()} WHERE ";
        $and = '';
        foreach ($this->pkeys as $pkey) {
            $sql .= $and.$pkey.' = ?';
            $and = ' AND ';
            if (!isset($this->_from_hydrate[$pkey])) {
                return false;
            }
            $arr[] = $this->_from_hydrate[$pkey];
        }
        $dbi = $con ? $con : extension()->DBI()->getInstance($this->default_instance_name);
        $dbi->query($sql, $arr);
        $this->_is_modify = false;
    }
    /* ----------------------------------------- */


    /**
     * +-- マジックメソッド
     *
     * @access public
     * @param  $name
     * @param  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_is_modify = true;
        $this->to_save[$name] = $value;
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access public
     * @param  $name
     * @return integer|string
     */
    public function __get($name)
    {
        return isset($this->to_save[$name]) ? $this->to_save[$name] : NULL;
    }
    /* ----------------------------------------- */

    /**
     * +-- マジックメソッド
     *
     * @access public
     * @param  $name
     * @param  $arguments
     * @return mixed
     */
    public function __call ($name , $arguments)
    {
        if (strPos($name, 'get') === 0) {
            $name = strtolower(substr(preg_replace('/([A-Z])/', '_\1', $name), 4));
            return isset($this->to_save[$name]) ? $this->to_save[$name] : NULL;
        } elseif (strPos($name, 'set') === 0) {
            $name = strtolower(substr(preg_replace('/([A-Z])/', '_\1', $name), 4));
            $this->to_save[$name] = $arguments[0];
            $this->_is_modify = true;
            return;
        }
        trigger_error('undefined method:'.$name);
    }
    /* ----------------------------------------- */

    /**
     * +-- パスカライズする
     *
     * @access      protected
     * @param       string $snake_case
     * @return      string
     */
    protected function pascalize($snake_case)
    {
        $pascal_case = strtolower($snake_case);
        $pascal_case = str_replace('_', ' ', $pascal_case);
        $pascal_case = ucwords($pascal_case);
        $pascal_case = str_replace(' ', '', $pascal_case);
        return $pascal_case;
    }
    /* ----------------------------------------- */


}