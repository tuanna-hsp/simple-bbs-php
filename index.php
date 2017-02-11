<!DOCTYPE html>
<?php
    include_once("database.php");
    
    $database = Database::getInstance();
    $is_logged_in = isset($_SESSION['user']);
    
    $username = 'guest';
    if ($is_logged_in) {
        $username = $_SESSION['user']['username'];
    }
    
    // Check if user has created a new post
    if ($_POST['post']) {
        if ($is_logged_in) {
            $database->createPost($_SESSION['user']['user_id'], $_POST['post']);
        }
        else {
            // Redirect to login page
            header("Location: /login.php");
            exit();
        }
    }
    
    $posts = $database->getPosts();
?>
<html>
    <body>
        <h1>Welcome <?php echo $username ?></h1>
        <br/>
        <form action = "<?php $_PHP_SELF ?>" method = "POST">
             <input type = "text" name = "post" />
             <input type = "submit" value="Post"/>
        </form>
        <br/><br/>
        
        <?php foreach ($posts as $post) { ?>
            <a href="/post.php?post_id=<?php echo $post['post_id'] ?>"><?php echo $post['content'] ?></a>
            <a href="/user.php?user_id=<?php echo $post['user_id'] ?>">By <?php echo $post['username'] ?></a><br>
        <?php } ?>
    </body>
</html>