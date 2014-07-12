<?php
/*
* includes/classes/database.php
* Database manipulation class
* Connect, select, insert, delete SQL
*/

class Database {
    // Database handle
    public static $db;
    // Database connection
    public static function connect(){
        try {
            self::$db = new PDO("mysql:host=".DBHOST.";port=8889;dbname=".DBNAME, DBUSER, DBPASS);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //Database query execute
    public static function query($query){
        try {
            $result = self::$db->query($query);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        return $result;
    }
}
?>