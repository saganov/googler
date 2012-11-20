<?php

class ViewHelper
{
    public static function crop($str, $len = 80)
    {
        if (strlen($str) <= $len)
        {
            return $str;
        }
        else
        {
            return substr($str, 0, $len/2 - 2) .' ... '. substr($str, -($len/2 - 2));
        }

    }

    public static function ctr($click, $show)
    {
        if($click < 1) return 0;
        return round(100*(int)$click/(int)$show, 1);
    }

    public static function pager($total, $current=0, $url='/')
    {
        $pager = '';
        
        if($current > 2)
        {
            $pager .= "<a href=\"$url&p=0\">&lt;&lt;</a>&nbsp;";
        }
        
        if($current > 1)
        {
            $pager .= "<a href=\"$url&p=". ($current-2) ."\">&lt;</a>&nbsp;";
        }
        
        foreach(range(1, $total) as $page)
        {
            if($page == $current)
            {
                $pager .= "<span>$page</span>";
            }
            else
            {
                $pager .= "<a href=\"$url&p=". ($page-1) ."\">$page</a>";
            }
            $pager .= '&nbsp;';
        }
        
        if($current < $total)
        {
            $pager .= "<a href=\"$url&p=". ($current) ."\">&gt;</a>&nbsp;";
        }
        
        if($current < $total-1)
        {
            $pager .= "<a href=\"$url&p=". ($total-1) ."\">&gt;&gt;</a>";
        }
        return $pager;
    }
}