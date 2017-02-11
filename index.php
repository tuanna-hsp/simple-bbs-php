<!DOCTYPE html>
<?php
    include_once("database.php");
    
    $database = Database::getInstance();
    
    $username = 'guest';
    $is_logged_in = isset($_SESSION['user']);
    if ($is_logged_in) {
        $username = $_SESSION['user']['username'];
    }
    
    // Check if user has created a new post
    if (isset($_POST) && $_POST['post']) {
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
        
        <ul>
        <?php foreach ($posts as $post) { ?>
            <li><?php echo $post['content'] ?></li>
        <?php } ?>
        </ul>
    </body>
</html>