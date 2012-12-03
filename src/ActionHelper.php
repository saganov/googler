<?php

class ActionHelper
{
    
    protected static function decode(/*string*/$data)
    {
        return json_decode($data, TRUE);
        //return unserialize($data);
    }

    protected static function encode(array $data)
    {
        return json_encode($data);
        //return serialize($data);
    }

    public static function getShown()
    {
        return (isset($_COOKIE['show']) ? self::decode($_COOKIE['show']) : array());
    }

    public static function addShown(array $urls)
    {
        return self::setShown(array_merge($urls, self::getShown()));
    }

    public static function setShown(array $urls)
    {
        /** @todo: it's not a good solution that Model knows about View */
        return View::setcookie('show', self::encode($urls));
    }

    public static function getUnshown(array $urls)
    {
        return array_diff($urls, self::getShown());
    }

    public static function getClicked()
    {
        return (isset($_COOKIE['click']) ? self::decode($_COOKIE['click']) : array());
    }

    public static function isUniqueClick($url)
    {
        return (FALSE === array_search($url, self::getClicked()));
    }

    public static function addClicked($url)
    {
        $url = is_array($url) ? $url : array($url);
        return self::setClicked(array_merge($url, self::getClicked()));
    }

    public static function setClicked(array $urls)
    {
        /** @todo: it's not a good solution that Model knows about View */
        return View::setcookie('click', self::encode($urls));
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