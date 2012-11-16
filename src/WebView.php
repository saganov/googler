<?php

class WebView /*implement View*/
{
    public static function header($header)
    {
        echo "<br />$header <br />\n";
        echo "<hr />\n";
    }

    public static function output(array $data)
    {
        foreach($data as $label=>$row)
        {
            foreach($row as $label=>$value)
            {
                echo "{$label}:". self::makeContent($value) ."<br />\n";
            }
            echo "<br />\n";
        }
        echo "<br />\n";
    }

    public static function error($message)
    {
        echo "$message<br/ >\n";
    }

    protected static function makeContent($string)
    {
        if(preg_match('/^http:\/\/.*$/', $string))
        {
            return "<a href='$string'>$string</a>";
        }
        else
        {
            return $string;
        }
    }

}
