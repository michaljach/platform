<?php
/*
* includes/classes/app.php
* App main class
* Init start here.
*/

class App {
    // App settings array
    public static $settings = array();

    // Class constructor
    public function __construct(){
        require_once("config.php");
        spl_autoload_register(array($this, 'autoloader'));

        Database::connect();
        $this->getSettings();
        $this->renderApp();
    }

    // Dependency class loader
    public function autoloader($class) {
        require_once("app/classes/" . $class . ".php");
    }

    // Get settings data
    public function getSettings(){
        $q = Database::query("SELECT * FROM settings");
        self::$settings = $q->fetchAll();
        require_once("languages/".App::$settings['2']['value'].".php");
    }

    // GET params safe returner
    public function getParam($param){
        return isset($_GET[$param]) ? htmlspecialchars(trim($_GET[$param])) : NULL;
    }

    // Renders current page
    public function renderApp(){
        $tpl = new Template("header.tpl", array());
        if($this->getParam("post") != NULL){
            $post = Post::getPost(intval($this->getParam("post")));
            if(!$post){
                Error::showError(NO_POST);
            } else {
                $tpl = new Template("post.tpl", array("id"=>$post['id']));
            }
        } else if($this->getParam("page") != NULL){



        } else {
            foreach (Post::getPosts() as $post) {
                $tpl = new Template("post.tpl", array("id"=>$post['id']));
            }
        }
        $tpl = new Template("footer.tpl", array());
    }
}
?>