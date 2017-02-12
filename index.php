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
    <head>
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>
        <div class="container">
            <h1>Welcome '<?php echo $username ?>'</h1>
            <br/>
            <div class="post-form">
                <form action = "<?php $_PHP_SELF ?>" method = "POST">
                     <input type = "text" name = "post" placeholder="Write something" required="required" class="post-input"/>
                     <input type = "submit" value="Post" class="btn btn-primary btn-large post-button"/>
                </form>
            </div>
            <br/>
            <h1>Recent posts</h1>
            <div class="posts">
                <?php foreach ($posts as $post) : if ($post) { ?>
                    <a class="post-link" href="/post.php?post_id=<?php echo $post['post_id'] ?>"><?php echo $post['content'] ?></a>
                    <a class="user-link" href="/user.php?user_id=<?php echo $post['user_id'] ?>">By <?php echo $post['username'] ?></a><br> 
                <?php } endforeach; ?>
            </div>
        </div>
    </body>
</html>