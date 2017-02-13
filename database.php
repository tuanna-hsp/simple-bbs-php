<?php
    
    /*
    ** Singleton class that handle all database interactions
    */
    class Database {
        
        const DB_HOST = "localhost:3306";
        const DB_USER = "tuanna_hsp";
        const DB_PASS = "";
        
        private static $database;
        
        private $connection;
        
        private function __construct() {}
        private function __clone() {}
        private function __wakeup() {}
        
        static function getInstance() {
            if (self::$database == NULL) {
                self::$database = new Database();
                self::$database->createConnection();
            }
            return self::$database;
        }
    
        function createConnection() {
            if ($this->connection != NULL) {
                return $this->connection;
            }
            
            $this->connection = mysql_connect(self::DB_HOST, self::DB_USER, self::DB_PASS);
            if (!$this->connection) {
                die("Could not connect: " . mysql_error());
            }
            
            // Create database if needed, then select it
            $sql = "CREATE DATABASE IF NOT EXISTS bbs_db";
            $create_db_result = mysql_query($sql, $this->connection);
            if (!$create_db_result) {
                die("Couldn't create database: " . mysql_error());
            }
            mysql_select_db('bbs_db');
            
            // Create all required tables
            
            $sql =  "CREATE TABLE IF NOT EXISTS user(" .
                    "user_id INT NOT NULL AUTO_INCREMENT, " .
                    "username VARCHAR(50) NOT NULL, " .
                    "password VARCHAR(50) NOT NULL, " .
                    "join_date TIMESTAMP NOT NULL, " .
                    "PRIMARY KEY (user_id))";
            $create_table_result = mysql_query($sql, $this->connection);
            if (!$create_table_result) {
                die("Couldn't create table: " . mysql_error());
            }
            
            $sql =  "CREATE TABLE IF NOT EXISTS post(" .
                    "post_id INT NOT NULL AUTO_INCREMENT, " .
                    "content VARCHAR(500) NOT NULL, " .
                    "user_id INT NOT NULL, " .
                    "view_count INT DEFAULT 0, " .
                    "comment_count INT DEFAULT 0, " .
                    "created_at TIMESTAMP NOT NULL, " .
                    "PRIMARY KEY (post_id))";
            $create_table_result = mysql_query($sql, $this->connection);
            if (!$create_table_result) {
                die("Couldn't create table: " . mysql_error());
            }
            
            $sql =  "CREATE TABLE IF NOT EXISTS comment(" .
                    "comment_id INT NOT NULL AUTO_INCREMENT, " .
                    "content VARCHAR(500) NOT NULL, " .
                    "user_id INT NOT NULL, " .
                    "post_id INT NOT NULL, " .
                    "created_at TIMESTAMP NOT NULL, " .
                    "PRIMARY KEY (comment_id))";
            $create_table_result = mysql_query($sql, $this->connection);
            if (!$create_table_result) {
                die("Couldn't create table: " . mysql_error());
            }
            
            return $this->connection;
        }
        
        function close() {
            mysql_close($this->connection);
        }
        
        function getUser($user_id) {
            $sql = "SELECT * FROM user WHERE user_id=$user_id";
            $result = mysql_query($sql, $this->connection);
            if ($result) {
                $user = mysql_fetch_assoc($result);
                // Should not return user password
                $user['password'] = "";
                return $user;
            }
            else {
                die("Couldn't find user: " . mysql_error());
            }
        }
        
        function authenticate($username, $password) {
            // Check if user exists (unsafe query)
            $sql = "SELECT * FROM user " .
                    "WHERE username='$username' AND password='$password' LIMIT 1";
            $query_user_result = mysql_query($sql, $this->connection);
            if ($query_user_result) {
                $user = mysql_fetch_assoc($query_user_result);
                $user['password'] = "";
                return $user;
            }
            else {
                die("Couldn't authenticate: " . mysql_error());
            }
        }
        
        function isUserExisted($username, $password) {
            $sql = "SELECT count(user_id) AS total FROM user " .
                    "WHERE username='$username' AND password='$password' LIMIT 1";
            $result = mysql_query($sql, $this->connection);
            if ($result) {
                $user_count = mysql_fetch_assoc($result)['total'];
                return $user_count;
            }
            else {
                die("Error: " . mysql_error());
            }
        }
        
        function register($username, $password) {
            $sql = "INSERT INTO user(username, password, join_date) " .
                    "VALUES('$username', '$password', NOW())";
            $create_user_result = mysql_query($sql, $this->connection);
            if ($create_user_result) {
                return $this->authenticate($username, $password);
            }
            else {
                die("Couldn't register new user: " . mysql_error());
            }
        }
        
        function getPosts() {
            $sql = "SELECT post.*, user.username " . 
                    "FROM post LEFT OUTER JOIN user ON post.user_id = user.user_id ";
            $result = mysql_query($sql, $this->connection);
            $posts = array();
            $i = 0;
            while ($posts[$i++] = mysql_fetch_assoc($result));
            
            return $posts;
        }
        
        function getUserPosts($user_id) {
            $sql = "SELECT * FROM post WHERE user_id=$user_id";
            $result = mysql_query($sql, $this->connection);
            $posts = array();
            $i = 0;
            while ($posts[$i++] = mysql_fetch_assoc($result));
            
            return $posts;
        }
        
        function getPost($post_id, $update_view_count = FALSE) {
            $sql = "SELECT post.*, user.username " .
                    " FROM post LEFT OUTER JOIN user ON post.user_id=user.user_id" . 
                    " WHERE post_id=$post_id";
            $result = mysql_query($sql, $this->connection);
            if (!$result) {
                die("Couldn't find post: " . mysql_error());
            }
            $post = mysql_fetch_assoc($result);
            
            if ($update_view_count) {
                $sql = "UPDATE post SET view_count=(view_count+1) WHERE post_id=$post_id";
                mysql_query($sql, $this->connection);
            }
            
            return $post;
        }
        
        function createPost($user_id, $content) {
            $sql = "INSERT INTO post(content, user_id, created_at) " .
                    "VALUES('$content', $user_id, NOW())";
            $result = mysql_query($sql, $this->connection);
            if (!$result) {
                die("Couldn't create post: " . mysql_error());
            }
        }
        
        function getComments($post_id) {
            $sql = "SELECT comment.*, user.username " .
                    "FROM comment LEFT OUTER JOIN user ON comment.user_id = user.user_id " . 
                    "WHERE post_id=$post_id ORDER BY created_at DESC";
            $result = mysql_query($sql, $this->connection);
            $comments = array();
            $i = 0;
            while ($comments[$i++] = mysql_fetch_assoc($result));
            
            return $comments;
        }
        
        function createComment($user_id, $post_id, $content) {
            $sql = "INSERT INTO comment(content, user_id, post_id, created_at) " .
                    "VALUES('$content', $user_id, $post_id, NOW())";
            $result = mysql_query($sql, $this->connection);
            if (!$result) {
                die("Couldn't create comment: " . mysql_error());
            }
            
            // Update comment count for post
            $sql = "UPDATE post SET comment_count=(comment_count+1) WHERE post_id=$post_id";
            mysql_query($sql, $this->connection);
        }
    }
?>