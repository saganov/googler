<?php

require_once "DatabaseHelper.php";
require_once "GooglerHelper.php";

class SearchModel
{
    protected $db;
    protected $googler;

    protected $itemsPerPage;

    public function __construct($itemsPerPage = 10)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->db = new DatabaseHelper('DB.csv');
        $this->googler = new GooglerHelper(array('super.domain.com', 'super.domain2.com', 'super.domain3.com'));
    }
    
    public function get($query, $source = NULL, $page = 0)
    {
        $where = array('query_phrase'=>$query);
        if(!is_null($source))
        {
            $where['source_domain'] = $source;
        }
        
        $res = $this->db->select(
            $where,                      // where clause
            $this->itemsPerPage * $page, // from line
            $this->itemsPerPage);        // limit

        if(empty($res))
        {
            $this->db->insert($this->googler->get($query));
            /** @todo: think - how to improve here */
            $res = $this->db->select(
                $where,                      // where clause
                $this->itemsPerPage * $page, // from line
                $this->itemsPerPage);        // limit
        }

        return $res;
    }

}