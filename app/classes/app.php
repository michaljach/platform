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
        $q = Database::$db->prepare("SELECT * FROM settings");
        $q->execute();
        self::$settings = $q->fetchAll();
        require_once("languages/".App::$settings['2']['value'].".php");
    }

    // GET params safe returner
    public function getParam($param){
        return isset($_GET[$param]) ? htmlspecialchars(trim($_GET[$param])) : NULL;
    }

    // Login method
    public function login($login, $password){
        if ($stmt = Database::$db->prepare("SELECT * FROM users WHERE login = ? LIMIT 1")) {
            $stmt->bindValue(1, $login, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            $password = hash('sha512', $password);
            if ($stmt->rowCount() == 1) {
                if ($result['password'] == $password) {
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    $user_id = preg_replace("/[^0-9]+/", "", $result['id']);
                    $_SESSION['user_id'] = $user_id;
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/","", $result['login']);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    // Logout method
    public function logout(){
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(),'', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]); 
        session_destroy();
        $tpl = new Template("admin_login.tpl", NULL, true);
    }

    // Login check with session
    public function checkLogin(){
        if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])){
            $user_id = $_SESSION["user_id"];
            $login_string = $_SESSION["login_string"];
            $username = $_SESSION["username"];
     
            $user_browser = $_SERVER["HTTP_USER_AGENT"];
     
            if ($stmt = Database::$db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")){
                $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                if ($stmt->rowCount() == 1) {
                    $login_check = hash('sha512', $result['password'] . $user_browser);
                    if ($login_check == $login_string) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Renders current page
    public function renderApp(){
        session_start();
        if($this->getParam("id") != NULL){
            if($this->getParam("id") == "admin"){
                if (isset($_POST["login"], $_POST["password"])) {
                    $login = $_POST["login"];
                    $password = $_POST["password"];
                 
                    if ($this->login($login, $password) == true) {
                        $tpl = new Template("admin_index.tpl", NULL, true);
                    } else {
                        $tpl = new Template("admin_login.tpl", NULL, true);
                    }
                } else {
                    if ($this->checkLogin() == true){
                        $tpl = new Template("admin_index.tpl", NULL, true);
                    } else {
                        $tpl = new Template("admin_login.tpl", NULL, true);
                    }
                }
            } else if($this->getParam("id") == "logout"){
                $this->logout();
            } else {
                $tpl = new Template("header.tpl", array());
                $post = Post::getPost(intval($this->getParam("id")));
                if(!$post){
                    Error::showError(NO_POST);
                } else {
                    $tpl = new Template("post.tpl", array("id"=>$post['id']));
                }
                $tpl = new Template("footer.tpl", array());
            }
        } else {
            $tpl = new Template("header.tpl", array());
            foreach (Post::getPosts() as $post) {
                $tpl = new Template("post.tpl", array("id"=>$post['id']));
            }
            $tpl = new Template("footer.tpl", array());
        }
    }
}
?>