<?php
/**
 * MyPDO  预定义链接PDO，自家用
 *
 * @desc $Id$
 *
 * @author Michael Song 2015-09-28
 */
class MyPDO extends PDO
{
    private $_host      = '';
    private $_port      = '';
    private $_user      = '';
    private $_pass      = '';
    private $_database  = '';
    private $_conn      = '';
    private $_table     = '';
    private $_query     = '';
    private $_select    = null;
    private $_where     = null;
    private $_order     = null;
    private $_update    = null;
    private $_join      = null;
    private $_method    = '';
    private $_params    = null;

    function __construct()
    {
        $config = \Yaf\Application::app()->getConfig()->database['mysql'];
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        parent::__construct($dsn, $config['user'], $config['pass'],  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES'utf8';"));
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    /**
     * Attribute : _table
     */
    public function getTable()
    {
        return $this->_table;
    }

    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    public function table($table)
    {
        return $this->setTable($table);
    }

    /**
     * @desc    数据库插入操作
     * @param   插入数据array
     *          array(
     *              'id' => 1,
     *              'name' => foo,
     *              'pass' => 123123
     *          )
     * @return  bool
     */
    public function insert($params) 
    {
        if (empty($this->_table)) {
            throw new Exception('none table name been set!');
        }
        if (!empty($params) && is_array($params)) {
            $columns = array_keys($params);
            $columns = implode(',', $columns);
            $values  = array_fill(0, count($params), '?');
            $values  = implode(',', $values);
        } else {
            throw new Exception('params empty!');
        }
        
        $sql = "INSERT INTO {$this->_table} ({$columns}) values ({$values});";

        $this->_prepare = $this->prepare($sql);
        $index = 1;
        foreach ($params as $value) {
            $this->_prepare->bindParam($index, $value);
        }
    }

    /**
     * @desc    设置select方法所提取的字段
     * @param   array() columns
     * @return  $this
     */
    public function select($columns) 
    {
        $this->_select = "select {$columns} from {$this->_table}";
        $this->_method = 'select';
        return $this;
    }

    /**
     * @desc   设置链表语句
     * @param  table_name , on
     * @return  
     */
    public function innerJoin($tableName, $on, $params) 
    {
        $this->_join[] = array("inner join {$tableName} on {$on}", $params);
        return $this;
    }

    public function leftJoin($tableName, $on, $params) 
    {
        $this->_join[] = array("left join {$tableName} on {$on}", $params);
        return $this;
    }

    public function rightJoin($tableName, $on, $params) 
    {
        $this->_join[] = array("right join {$tableName} on {$on}", $params);
        return $this;
    }

    /**
     * @desc    设置where查询条件
     * @param  
     * @return  
     */
    public function where($where, $params = array()) 
    {
        $this->_where['query']  = "where {$where}";
        $this->_where['params'] = $params;
        return $this;
    }

    /**
     * @desc  
     * @param  
     * @return  
     */
    public function orderBy($order, $params = array()) 
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * @desc  
     * @param  
     * @return  
     */
    public function doSelect() 
    {
        $sql = "{$this->_select} {$this->_table}";
        empty($this->_params) && $this->_params = array();

        if (!empty($this->_where) && is_array($this->_where)) {
            $queryParams = $this->_where['query'];
            $whereParams = $this->_where['params'];
            $sql         = "{$sql} {$queryParams}";
            if (!empty($whereParams)) {
                $this->_params = $this->_params + $whereParams;
            }
        }

        if (!empty($this->_order)) {
            $sql = "{$sql} {$this->_order}";
        }

        $this->_query = $this->prepare($sql);
        $index = 1;
        foreach ($this->_params as $value) {
            if (is_int($value)) {
                $this->_query->bindParam($index, $value, self::PARAM_INT);
            } else {
                $this->_query->bindParam($index, $value);
            }
            $index++;
        }
        if ($this->_query->execute()) {
            //$result = $this->_query->fetchAll(self::FETCH_ASSOC);
            $result = $this->_query->fetchAll(self::FETCH_CLASS | self::FETCH_PROPS_LATE, "FilmModel");
            return $result;
        }
        return null;
    }

    /**
     * @desc  
     * @param  
     * @return  
     */
    public function execute() 
    {
        switch ($this->_method) {
            case 'select':
                return $this->doSelect();
            default:
                break;
        }
    }

    /**
     * @desc  
     * @param  
     * @return  
     */
    public function sql() 
    {
        return $this->_query;
    }
}
