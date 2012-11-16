<?php

require_once "phpQuery.php";

class GooglerModel
{
    protected $sources;

    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    protected function search($query, $source)
    {
        $res = array();
        /** @todo: google search "site:source query" */
        $resource = curl_init("https://www.google.com.ua/search?q=site:". urlencode($source .' '.$query));

        $header[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 sputnik 2.3.0.70';
        $header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $header[] = 'Accept-Language: ru,en-us;q=0.7,en;q=0.3';
        $header[] = 'Accept-Encoding: gzip,deflate';
        $header[] = 'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7';
        $header[] = 'Keep-Alive: 115';
        $header[] = 'Connection: keep-alive';
        
        curl_setopt($resource, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($resource, CURLOPT_ENCODING, 'gzip,deflate'); 
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, TRUE);
        $html = curl_exec($resource);
        //$html = iconv('windows-1251', 'UTF-8', $html);
        var_dump(curl_getinfo($resource, CURLINFO_CONTENT_TYPE));
        file_put_contents('page.html', $html);
        curl_close($resource); // close the connection 
        phpQuery::newDocument($html);

        // all LIs from last selected DOM
        foreach(pq('div#ires')->find('li.g') as $item)
        {
            $pq = pq($item);
            
            $title = $pq->find('h3.r > a')->html();
            $url   = $pq->find('h3.r > a')->attr('href');
            $desc  = $pq->find('div.s > span.st')->html();

            $res[] = array(
                'query_phrase'  => $query,
                'source_domain' => $source,
                'url'           => $url,
                'title'         => $title,
                'description'   => $desc,
                'date'          => gmdate('Y-m-d'));
        }
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