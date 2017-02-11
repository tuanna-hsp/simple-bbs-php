<!DOCTYPE html>
<html>
    <?php
        include_once("database.php");
        
        $database = Database::getInstance();
        $is_logged_in = isset($_SESSION['user']);
        $post = NULL;
        $comments = NULL;
        
        $post_id = $_REQUEST['post_id'];
        if ($post_id != NULL) {
        
            $comment = $_REQUEST['comment'];
            if ($comment) {
                // User create comment but not logged in
                if (!$is_logged_in) {
                    header("Location: /login.php");
                    exit();
                }
                // Otherwise
                $database->createComment(
                    $_SESSION['user']['user_id'], $post_id, $comment);
            }
            
            $post = $database->getPost($post_id);
            $comments = $database->getComments($post_id);
        }
        else {
            // Redirect to index page because no post specified
            header("Location: /");
            exit();
        }
    ?>
    
    <body>
        <h2>Post</h2>
        <p><?php echo $post['content'] ?></p>
        <h2>Comments</h2>
        <form action = "<?php $_PHP_SELF ?>" method = "POST">
             <input type = "text" name = "comment" />
             <input type = "submit" value="Post"/>
             <input type = "hidden" name = "post_id" value="<?php echo $post_id ?>"/>
        </form>
        <br/><br/>
        <?php foreach ($comments as $comment) { ?>
            <p><?php echo $comment['content'] ?></p>
            <a href="/user.php?user_id=<?php echo $comment['user_id'] ?>">By <?php echo $comment['username'] ?></a><br>
        <?php } ?>
    </body>
</html>