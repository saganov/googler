<?php

class GooglerHelper
{
    protected $sources;

    public function __class(array $sources)
    {
        $this->sources = $sources;
    }

    protected function search($query, $source)
    {
        $res = array();
        /** @todo: google search "site:source query" */
        return $res;
    }

    public function get($query)
    {
        $res = array();
        foreach($this->sources as $source)
        {
            $res = array_merge($res, $this->search($query, $source));
        }
        return $res;
    }
    

}