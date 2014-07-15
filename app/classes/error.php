<?php
/*
* includes/classes/error.php
* Error handling class
* Display and format errors
*/

class Error {
    public static function showError($text){
        $tpl = new Template("admin_header.tpl", NULL, true);
        $tpl = new Template("error.tpl", array("error"=>$text), true);
        $tpl = new Template("admin_footer.tpl", NULL, true);
    }
}
?>