<!DOCTYPE html>
<?php
    include_once("database.php");
    
    if (isset($_REQUEST['logout'])) 
        $_SESSION['user'] = NULL;
    
    $database = Database::getInstance();
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = NULL;
    
    if ($username && $password) {
        if (isset($_POST['login'])) {
            $user = $database->authenticate($username, $password);
            if ($user == NULL) {
                die("Wrong username or password!");
            } 
        } 
        // User pressed register button, so create new user and 
        // redirect to bulletin
        else {
            if ($database->isUserExisted($username, $password)) {
                die("User already existed!");
            }
            
            $user = $database->register($username, $password);
        }
            
        $_SESSION['user'] = $user;
        header("Location: /");
        exit();
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="style.css" type="text/css" />
    </head>
    <body>
        <div class="login">
            <h1>Simple BBS demo</h1>
            
            <form action="<?php $_PHP_SELF ?>" method = "POST">
                 <input type="text" name="username" placeholder="Username" required="required"/>
                 <input type="password" name="password" placeholder="Password" required="required"/>
                 <input type="submit" name="login" value="Login" class="btn btn-primary btn-block btn-large"/>
                 <input type="submit" name="register" value="Register" class="btn btn-primary btn-block btn-large" id="register"/>
            </form>
        </div>
    </body>
</html>