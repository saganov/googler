<?php

class DatabaseModel
{
    protected $db;
    protected $dbName;
    
    protected $header;

    protected $cache = array();

    public function __construct($file)
    {
        $this->dbName = $file;
        $db = fopen($file, "r");
        $this->header = fgetcsv($db);
        fclose($db);
    }

    public static function makeClause(array $clause)
    {
        return $clause;
    }

    protected function readLine($db)
    {
        if(FALSE !== $data = fgetcsv($db))
        {
            return array_combine($this->header, $data);
        }
        else
        {
            return FALSE;
        }
    }
    
    public function select($where = array(), $from = 0, $limit = FALSE)
    {
        $cache_key = serialize($where);
        $all_res = isset($this->cache[$cache_key]) ? unserialize($this->serialise($where)) : $this->doSelectDb($where);
        usort($all_res, array($this, 'sortByCtr'));
        $this->serialise[$cache_key] = serialize($all_res);
        
        $res = array();
        $number = 0;
        $exist = FALSE;
        $total = count($all_res);
        $i = 0;
        while (($i < $total) && (!$limit || count($res) < $limit))
        {
            $row = $all_res[$i];
            $i++;
            $match_number = 0;
            foreach($where as $label=>$value)
            {
                if(isset($row[$label]) && $row[$label] == $value)
                {
                    $match_number++;
                }
            }
            
            if($match_number == count($where))
            {
                $exist = TRUE;
                $number++;
                if($number >= $from)
                {
                    $res[] = $row;
                }
            }
        }

        if($exist && empty($res))
        {
            throw new Exception('Offset');
        }
        
        return $res;
    }

    private function sortByCtr($left, $right)
    {
        $left_ctr  = (((int)$left['show'] < 1) ? 0 : (int)$left['click']/(int)$left['show']);
        $right_ctr = (((int)$right['show'] < 1) ? 0 : (int)$right['click']/(int)$right['show']);
        if ($left_ctr == $right_ctr)
        {
            return 0;
        }
        return ($left_ctr > $right_ctr) ? -1 : 1;
    }

    public function doSelectDb($where = array(), $from = 0, $limit = FALSE)
    {
        $res = array();
        $number = 0;
        $exist = FALSE;
        $db = fopen($this->dbName, "r");
        while (($row = $this->readLine($db)) && (!$limit || count($res) < $limit))
        {
            $match_number = 0;
            foreach($where as $label=>$value)
            {
                if(isset($row[$label]) && $row[$label] == $value)
                {
                    $match_number++;
                }
            }
            
            if($match_number == count($where))
            {
                $exist = TRUE;
                $number++;
                if($number >= $from)
                {
                    $res[] = $row;
                }
            }
        }

        fclose($db);

        if($exist && empty($res))
        {
            throw new Exception('Offset');
        }

        return $res;
    }

    public function count($where = array())
    {
        $cache_key = serialize($where);
        $all_res = isset($this->cache[$cache_key]) ? unserialize($this->serialise($where)) : $this->doSelectDb($where);
        return (count($all_res));
    }

    public function insert(array $data)
    {
        $db = fopen($this->dbName, "a+");
        foreach ($data as $row)
        {
            foreach($this->header as $label)
            {
                if(!isset($row[$label]))
                {
                    $row[$label] = '';
                }
            }

            fputcsv($db, $row);
        }
        
        fclose($db);
    }

    public function update(array $data, array $where = array(), $from = 0, $limit = FALSE)
    {
        $tmp=$this->dbName.'.tmp';

        $res = array();
        $number = 0;
        $exist = FALSE;
        $db = fopen($this->dbName, "r");
        $db_tmp = fopen($tmp, 'w');
        while (($row = $this->readLine($db)) && (!$limit || count($res) < $limit))
        {
            $match_number = 0;
            foreach($where as $label=>$value)
            {
                if(isset($row[$label]) && $row[$label] == $value)
                {
                    $match_number++;
                    $exist = TRUE;
                }
            }
            
            if($match_number == count($where))
            {
                $number++;
                if($number >= $from)
                {
                    foreach($data as $key=>$val)
                    {
                        if(isset($row[$key]))
                        {
                            switch($val)
                            {
                                case '+1':
                                    $row[$key] = (int)$row[$key]+1;
                                    break;
                                default:
                                    $row[$key] = $val;
                            }
                        }
                    }
                }
            }
            
            fputcsv($db_tmp, $row);
        }

        fclose($db_tmp);
        fclose($db);
        
        // delete old source file
        unlink($this->dbName);
        // rename target file to source file
        rename($tmp, $this->dbName);
        
        $this->cache = array();
    }
}