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
        foreach($data as $label=>$row)
        {
            //echo "||". implode("\t||", $row) ."\t||\n";
            foreach($row as $label=>$value)
            {
                echo "{$label}:\t\t{$value}\n";
            }
            echo "\n";
        }
        echo "\n";
    }

    public static function error($message)
    {
        echo $message ."\n";
    }

}
