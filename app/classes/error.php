<?php
/*
* includes/classes/error.php
* Error handling class
* Display and format errors
*/

class Error {
    public static function showError($text){
        $tpl = new Template("error.tpl", array("error"=>$text));
    }
}
?>