<?php

require_once 'ViewHelper.php';

class View
{
        protected static $template_dir;
        protected static $cookie = array();
        protected static $debug = array();
        
        protected $template;
        protected $data = array();

        public function __construct($template)
        {
            $this->template = $template;
        }

        public static function setTemplateDir($dir)
        {
            self::$template_dir = $dir;
        }

        public static function setcookie($name, $value)
        {
            self::$cookie[$name] = $value;
        }

        public static function debug($key, $data)
        {
            self::$debug[$key] = $data;
        }
        
        
        public function set(array $data)
        {               
            $this->data = array_merge($this->data, $data);
        }

        public function parse()
        {
            ob_start();
            extract($this->data);
            include(self::$template_dir .'/'. $this->template);
            $out = ob_get_contents();
            ob_end_clean();
            return $out;
        }

        public function output()
        {
            foreach(self::$cookie as $name=>$value)
            {
                setcookie($name, $value);
            }

            foreach(self::$debug as $key=>$data)
            {
                echo "<pre>$key: <br/>". var_export($data, TRUE) ."</pre>\n";
            }

            echo $this->parse();
            self::$cookie = array();
            self::$debug = array();
        }

}