<?php

require_once dirname(__DIR__)."/vendor/phpQuery/phpQuery.php";

class GooglerModel
{
    protected $sources;
    protected $number;

    public function __construct(array $sources, $number = 10)
    {
        $this->sources = $sources;
        $this->number  = $number;
    }

    protected function search($query, $source)
    {
        $res = array();

        //$header[] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2) Gecko/20100115 Firefox/3.6 sputnik 2.3.0.70';
        //$header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        //$header[] = 'Accept-Language: ru,en-us;q=0.7,en;q=0.3';
        //$header[] = 'Accept-Encoding: gzip,deflate';
        //$header[] = 'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7';
        //$header[] = 'Keep-Alive: 115';
        //$header[] = 'Connection: keep-alive';
        

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
//            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_COOKIEFILE     => "cookie.txt",
            CURLOPT_COOKIEJAR      => "cookie.txt",
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3",
            CURLOPT_REFERER        => "http://www.google.com/",
                         ); 
        $google_search_url = "http://www.google.com/search?q=site:". urlencode($source .' '.$query);

        /** @todo: google search "site:source query" */
        $number = 0;
        $page = 0;

        while($page < 4)
        {
            $resource = curl_init($google_search_url/* .'&start='. $page++*/);
            curl_setopt($resource, CURLOPT_HTTPHEADER, $header); 
            curl_setopt($resource, CURLOPT_ENCODING, 'gzip,deflate'); 
            curl_setopt($resource, CURLOPT_RETURNTRANSFER, TRUE);

            //curl_setopt_array($resource,$options);
            $html = curl_exec($resource);

            if (200 != $code = curl_getinfo($resource, CURLINFO_HTTP_CODE))
            {
                throw new Exception('Google server returned an unsupported HTTP header: '. $code);
            }
            
            curl_close($resource); // close the connection 
            phpQuery::newDocument($html);
            // all LIs from last selected DOM
            foreach(pq('div#ires')->find('li.g') as $item)
            {
                $number++;
                if($number > $this->number)
                {
                    break 2;
                }
                
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