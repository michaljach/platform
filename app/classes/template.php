<?php
/*
* includes/classes/template.php
* Template rendering class
* Display HTML output with variables
*/

class Template {
    protected $file;
    protected $values = array();
  
    public function __construct($file, $values = NULL, $app = false){
        $dir = $app ? "app/templates/" . $file : "templates/".App::$settings['0']['value']."/".$file;
        $this->file = $dir;
        $this->values = $values;
        echo $this->output();
    }

    public function output(){
        if (!file_exists($this->file)) {
            return "Error loading template file ($this->file).";
        }
        $output = file_get_contents($this->file);
        if($this->values != NULL){
            foreach ($this->values as $key => $value) {
                $tagToReplace = "@$key";
                $output = str_replace($tagToReplace, $value, $output);
            }
        }
        return $output;
    }
}
?>