<!DOCTYPE html>
<?php
    include_once("database.php");
    
    $database = Database::getInstance();
    $user = NULL;
    $posts = NULL;
    
    $user_id = $_REQUEST['user_id'];
    if ($user_id != NULL) {
        $user = $database->getUser($user_id);
        $posts = $database->getUserPosts($user_id);
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
            <h2>Welcome to <?php echo $user['username'] ?>'s page</h2>
            <h2>All posts</h2>
            <div class="posts">
                <?php foreach ($posts as $post) if ($post) { ?>
                    <a class="post-link" href="/post.php?post_id=<?php echo $post['post_id'] ?>"><?php echo $post['content'] ?></a><br/>
                <?php } ?>
            </div>
        </div>
    </body>
</html>