<?php

class SearchView
{
    public static function header($header)
    {
        echo "\n";
        echo $header ."\n";
        echo "------------------------------------\n";
    }

    public static function output(array $data)
    {
        //echo "__________________________________________________\n";
        //echo "|". implode("\t||\t", array_keys($data[0])) ."|\n";
        foreach($data as $row)
        {
            echo "||". implode("\t||", $row) ."\t||\n";
        }
        echo "\n";
    }

}
