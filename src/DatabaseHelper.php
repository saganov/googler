<?php

class DatabaseHelper
{
    protected $db;
    
    protected $header;

    public function __construct($file)
    {
        $this->db = fopen($file, "r");
        $this->header = fgetcsv($this->db);
    }

    protected function read()
    {
        if(FALSE !== $data = fgetcsv($this->db))
        {
            return array_combine($this->header, $data);
        }
        else
        {
            return FALSE;
        }
    }

    protected function reset()
    {
        fseek($this->db, 0);
    }

    public function select($where = array(), $from = 0, $limit = FALSE)
    {
        $res = array();
        $number = 0;
        while (($row = $this->read()) && (!$limit || count($res) < $limit))
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
                $number++;
                if($number >= $from)
                {
                    $res[] = $row;
                }
            }
        }

        $this->reset();
        return $res;
    }
}