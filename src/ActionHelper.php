<?php

class ActionHelper
{
    protected $client;

    protected $db;

    public function __construct()
    {
        /** @todo: this code shouldn't be there */
        $this->db = new PdoEngine('googler', 'root', 'root');

        if(isset($_COOKIE['client']))
        {
            $this->client = $_COOKIE['client'];
        }
        else
        {
            $this->client = uniqid();
            View::setcookie('client', $this->client);
        }
    }
    
    public function getUnshown(array $ids)
    {
        $sql  = "SELECT `search_item_id` FROM `statistic` WHERE `client`='{$this->client}'";
        
        $shown = $this->db->query($sql);
        $res = array();
        foreach($shown as $item)
        {
            $res[] = $item['search_item_id'];
        }
        
        return array_diff($ids, $res);
    }
    
    public function addShown($ids)
    {
        $ids =(array)$ids;

        foreach($ids as $id)
        {
            $this->db->query("INSERT INTO `statistic` SET `client`='{$this->client}', `search_item_id`={$id}");
        }
    }
    

    public function isUniqueClick($url)
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `statistic`"
            ." LEFT JOIN `search_item` ON (`statistic`.`search_item_id`=`search_item`.`id`)"
            ." WHERE `client`='{$this->client}'"
            ." AND `url`='{$url}' AND `clicked`<>0";
        $clicked = $this->db->query($sql);
        return ($clicked[0]['count'] == 0);
    }

    public function addClicked($url)
    {
        $sql = "UPDATE `statistic` SET `clicked`=now()"
            ." WHERE `search_item_id`=(SELECT `id` FROM `search_item` WHERE `url`='{$url}') LIMIT 1";
        $this->db->query($sql);
    }
   
    protected static function dump()
    {
        $dump = '';
        foreach(func_get_args() as $arg)
        {
            $dump .= var_export($arg, TRUE) ."\n";
        }
        file_put_contents('debug', $dump);
    }
}