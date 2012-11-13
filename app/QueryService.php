<?php

class QueryService {
    private $queries = array(
        'test' => array(
            array(
                'source'      => 'domain.com',
                'url'         => 'domain.com/test',
                'title'       => 'Domain Com Test',
                'description' => 'Description ...',
                'date'        => '2012-11-12',
            ),
            array(
                'source'      => 'domain.com',
                'url'         => 'domain.com/test/test',
                'title'       => 'Domain Com Test Test',
                'description' => 'Description ...',
                'date'        => '2012-11-12',
            ),
            array(
                'source'      => 'domain2.com',
                'url'         => 'domain2.com/test',
                'title'       => 'Doamin2 Com Test',
                'description' => 'Description ...',
                'date'        => '2012-11-12',
            ),
        ),
        'test2' => array(
            array(
                'source'      => 'domain.com',
                'url'         => 'domain.com/test2',
                'title'       => 'Domain Com Test2',
                'description' => 'Description ...',
                'date'        => '2012-11-12',
                )
        ),
    );

    public function get($id){
        return $this->queris[$id];
    }

    public function getAll($query, $source=FALSE){
        if(isset($this->queries[$query]))
        {
                if(!$source)
                {
                        return $this->queries[$query];
                }
                else
                {
                        return array_filter($this->queries[$query], function($elm) use($source) {
                                                                                return $elm['source'] == $source;
                        });
                } 
        }
        else
        {
                // @todo: parse the google page
                return array();
        }
    }
}

