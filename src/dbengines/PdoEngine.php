<?php

class PdoEngine
{
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    
    protected $db;

    protected static $instance;

    public function __construct($database, $user='root', $password='root', $host='localhost')
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;

        /** @todo: it shoudn't be there */
        $this->connect();
        
        self::$instance = $this;
    }


    public function connect()
    {
        $this->db = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->password);
    }

    public static function getInstance()
    {
        if(!isset(self::$instance))
        {
            throw new Exception('Database not instantiated yet');
        }
        else
        {
            return self::$instance;
        }
    }

    public static function makeClause(array $clause)
    {
        $where = array();
        foreach($clause as $field=>$value)
        {
            $where[] = "`{$field}`='{$value}'";
        }
        
        return empty($where) ? '' : 'WHERE '. implode(' AND ', $where);
    }

    public static function makeLimit($from, $limit)
    {
        return ($limit ? "LIMIT {$from}, {$limit}" : "");
    }

    /**
     * $order array: 'fields'    => string: <field1>, <field2>, ...
     *               'direction' => string: ASC | DESC
     */
    public static function makeOrder($order = array())
    {
        if(empty($order) || !isset($order['fields']))
        {
            return '';
        }
        else
        {
            $direction = (empty($order['direction']) ? 'ASC' : $order['direction']);
            return "ORDER BY {$order['fields']} {$direction}";
        }
    }

    public function query($sql)
    {
        $statement = $this->db->query($sql);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select($table, $where = array(), $from = 0, $limit = FALSE, $order = array())
    {
        $sql = "SELECT * FROM `{$table}` "
            . self::makeClause($where) ." "
            . self::makeOrder($order) ." "
            . self::makeLimit($from, $limit);
        return $this->query($sql);
    }

    public function count($table, $where = array())
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `{$table}` "
            . self::makeClause($where);
        $statement = $this->db->query($sql);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function insert($table, array $data)
    {
        $sql ="INSERT INTO `{$table}` (`". implode('` , `', array_keys($data[0])) ."`) VALUES ";
        $values = array();
        foreach($data as $row)
        {
            $values[] = "(". implode(" , ", array_map(array($this->db, 'quote'), $row)) .")";
        }
        
        $sql .= implode(", ", $values);
        //echo $sql ."<br/>\n";
        //file_put_contents('insert.sql', $sql);
        $statement = $this->db->query($sql);
    }

    public function update($table, array $data, array $where = array(), $from = 0, $limit = FALSE, array $order = array())
    {
        $sql ="UPDATE `{$table}` SET ";
        $set = array();
        foreach($data as $label=>$value)
        {
            $set[] = "`{$label}`=$value";
        }
        $sql .= implode(', ', $set);
        
        $sql .= " ". self::makeClause($where);
        return $this->db->query($sql);
    }
}