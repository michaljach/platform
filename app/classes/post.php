<?php
/*
* includes/classes/post.php
* Post class
* Get, create, delete posts
*/

class Post {
    // Get array with posts with limit
    public static function getPosts($limit = NULL){
        $limit = $limit == NULL ? App::$settings['1']['value'] : $limit;
        $q = Database::$db->prepare("SELECT * FROM posts LIMIT ?");
        $q->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll();
    }

    // Get one post by ID
    public static function getPost($id){
        $q = Database::$db->prepare("SELECT * FROM posts WHERE id = ?");
        $q->bindValue(1, $id);
        $q->execute();
        return $q->fetch();
    }
}
?>