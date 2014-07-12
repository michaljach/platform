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
        $q = Database::query("SELECT * FROM posts LIMIT " . $limit);
        return $q->fetchAll();
    }

    // Get one post by ID
    public static function getPost($id){
        $q = Database::query("SELECT * FROM posts WHERE id = $id");
        return $q->fetch();
    }
}
?>