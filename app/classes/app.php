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

    // Secure session
    public function sessionStart(){
        $session_name = "sec_session_id";
        $secure = "SECURE";
        $httponly = true;
        if (ini_set("session.use_only_cookies", 1) === FALSE) {
            header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
            exit();
        }
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"],
            $cookieParams["path"], 
            $cookieParams["domain"], 
            $secure,
            $httponly);

        session_name($session_name);
        session_start();
        session_regenerate_id();
    }

    public function login($email, $password){
        if ($stmt = Database::$db->prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $username, $db_password, $salt);
            $stmt->fetch();
            $password = hash('sha512', $password . $salt);
            if ($stmt->num_rows == 1) {
                if ($db_password == $password) {
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/","", $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    public function checkLogin(){
        if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])){
            $user_id = $_SESSION["user_id"];
            $login_string = $_SESSION["login_string"];
            $username = $_SESSION["username"];
     
            $user_browser = $_SERVER["HTTP_USER_AGENT"];
     
            if ($stmt = $mysqli->prepare("SELECT password FROM members WHERE id = ? LIMIT 1")){
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->store_result();
     
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($password);
                    $stmt->fetch();
                    $login_check = hash('sha512', $password . $user_browser);
     
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
        if($this->getParam("id") != NULL){
            if($this->getParam("id") == "login"){
                $this->sessionStart();
                if (isset($_POST["email"], $_POST["password"])) {
                    $email = $_POST["email"];
                    $password = $_POST["password"];
                 
                    if ($this->login($email, $password) == true) {
                        header('Location: ../protected_page.php');
                    } else {
                        header('Location: ../index.php?error=1');
                    }
                } else {
                    if ($this->checkLogin() == true){

                    } else {
                        $tpl = new Template("admin_login.tpl", NULL, true);
                    }
                }
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