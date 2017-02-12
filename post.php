<!DOCTYPE html>
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
<html>
    <head>
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>
        <div class="container">
            <h2><?php echo "Posted by '".$post['username']."' at ".$post['created_at'] ?></h2>
            <div class="posts">
                <p class="post-content"><?php echo $post['content'] ?></p>
            </div>
            <h2>Comments</h2>
            <form action = "<?php $_PHP_SELF ?>" method = "POST">
                 <input type = "text" name = "comment" placeholder="Leave a comment" class="comment-input"/>
                 <input type = "submit" value="Post" class="btn btn-primary btn-large post-button"/>
                 <input type = "hidden" name = "post_id" value="<?php echo $post_id ?>"/>
            </form>
            <div class="comments">
                <?php foreach ($comments as $comment) if ($comment) { ?>
                    <div class="comment-line">
                        <span class="comment"><?php echo $comment['content'] ?></span>
                        <a class="comment-link" href="/user.php?user_id=<?php echo $comment['user_id'] ?>">
                            <?php echo "By '".$comment['username']."', ".$comment['created_at'] ?></a><br>
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>