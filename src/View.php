<?php

class View
{
        protected static $template_dir;
        
        protected $template;
        protected $data = array();

        public function __construct($template)
        {
            $this->template = $template;
        }

        public static function setTemplateDir($dir)
        {
            static::$template_dir = $dir;
        }
        
        public function set(array $data)
        {               
                $this->data = array_merge($this->data, $data);
        }

        public function parse()
        {
                ob_start();
                extract($this->data);
                include(static::$template_dir .'/'. $this->template);
                $out = ob_get_contents();
                ob_end_clean();
                return $out;
        }

        public function output()
        {
                echo $this->parse();
        }

}