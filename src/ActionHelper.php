<?php

class ActionHelper
{
    protected $client;

    protected $db;

    public function __construct()
    {
        /** @todo: this code shouldn't be there */
        //$this->db = new PdoEngine('googler', 'root', 'root');
        $this->db =  PdoEngine::getInstance();

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
        $sql  = "SELECT `item_id` FROM `statistic` WHERE `client`='{$this->client}' AND `table`='search_item'";
        $shown = $this->db->query($sql);
        $res = array();
        foreach($shown as $item)
        {
            $res[] = $item['item_id'];
        }
        
        $unshown = array();
        $unshown['search'] = array_diff($ids['search'], $res);

        $sql  = "SELECT `item_id` FROM `statistic` WHERE `client`='{$this->client}' AND `table`='news_item'";
        $shown = $this->db->query($sql);
        $res = array();
        foreach($shown as $item)
        {
            $res[] = $item['item_id'];
        }
        
        $unshown['news'] = array_diff($ids['news'], $res);

        $sql  = "SELECT `item_id` FROM `statistic` WHERE `client`='{$this->client}' AND `table`='youtube_item'";
        $shown = $this->db->query($sql);
        $res = array();
        foreach($shown as $item)
        {
            $res[] = $item['item_id'];
        }
        
        $unshown['youtube'] = array_diff($ids['youtube'], $res);

        return $unshown;
    }
    
    public function addShown(array $ids)
    {
        foreach($ids['search'] as $id)
        {
            $this->db->query("INSERT INTO `statistic` SET `table`='search_item', `client`='{$this->client}', `item_id`={$id}");
        }
        
        foreach($ids['news'] as $id)
        {
            $this->db->query("INSERT INTO `statistic` SET `table`='news_item', `client`='{$this->client}', `item_id`={$id}");
        }

        foreach($ids['youtube'] as $id)
        {
            $this->db->query("INSERT INTO `statistic` SET `table`='youtube_item', `client`='{$this->client}', `item_id`={$id}");
        }
    }
    

    public function isUniqueClick($url, $table)
    {
        $sql = "SELECT COUNT(*) AS `count` FROM `statistic`"
            ." LEFT JOIN `{$table}` ON (`statistic`.`item_id`=`{$table}`.`id`)"
            ." WHERE `client`='{$this->client}'"
            ." AND `table` = '{$table}'"
            ." AND `url`='{$url}' AND `click`<>0";
        $clicked = $this->db->query($sql);
        return ($clicked[0]['count'] == 0);
    }

    public function addClicked($url, $table)
    {
        $sql = "UPDATE `statistic` SET `clicked`=now()"
            ." WHERE `table`='{$table}' AND `item_id`=(SELECT `id` FROM `{$table}` WHERE `url`='{$url}') LIMIT 1";
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