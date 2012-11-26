<?php

class SearchModel
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function select($table, $where = array(), $from = 0, $limit = FALSE, $order = array())
    {
        return $this->db->select($table, $where, $from, $limit, $order);
    }

    public function update($table, array $data, array $where = array(), $from = 0, $limit = FALSE)
    {
        return $this->db->update($table, $data, $where, $from, $limit);
    }

    public function countList($query)
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `search_item` "
            ."LEFT JOIN `query_phrase` ON `query_phrase`.`id`=`search_item`.`query_phrase` "
            ."WHERE `query_phrase`.`text`='$query' LIMIT 1";

        $statement = $this->db->query($sql);
        return $statement[0]['count'];
    }

    public function getList($query = NULL, $source = NULL, $from = 0, $limit = FALSE)
    {
        $sql = "SELECT * FROM `search_item` "
            ."LEFT JOIN `query_phrase` ON `query_phrase`.`id`=`search_item`.`query_phrase` "
            ."LEFT JOIN `source_domain` ON `source_domain`.`id`=`search_item`.`source_domain`";

        $where = array('text'=>$query);
        if(!empty($source))
        {
            $where['domain'] = $source;
        }

        $sql .= PdoEngine::makeClause($where) ." "
            . PdoEngine::makeOrder(array('fields'=>'`click`/`show`', 'direction'=>'DESC')) ." "
            . PdoEngine::makeLimit($from, $limit);
        return $this->db->query($sql);
    }
    
    public function insertList($query_phrase, array $data)
    {
        $this->db->query("INSERT INTO `query_phrase` SET `text`='$query_phrase'");
        $res = $this->db->query("SELECT `id` FROM `query_phrase` WHERE `text`='$query_phrase'");
        $query_phrase_id = $res[0]['id'];
        
        foreach($data as &$row)
        {
            $row['query_phrase'] = $query_phrase_id;
            $res = $this->db->query("SELECT `id` FROM `source_domain` WHERE `domain`='{$row['source_domain']}'");
            $row['source_domain'] = $res[0]['id'];
        }
        
        $this->db->insert('search_item', $data);
    }

    public function updateList($query = NULL, $source = NULL, $from = 0, $limit = FALSE)
    {
        $res = $this->db->query("SELECT `id` FROM `query_phrase` WHERE `text`='$query'");
        $clause = array('query_phrase'=>$res[0]['id']);
        if(!empty($source))
        {
            $clause['source_domain'] = $source;
        }
        $this->db->update('search_item', array('show'=>'`show`+1'), $clause, $from, $limit);
    }

}