<html><body>
<?php
    include_once("database.php");
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username && $password) {
        $connection = Database::getInstance()->createConnection();
        
        // Check if user exists (unsafe query)
        $sql = "SELECT * FROM user " .
                "WHERE username='$username' AND password='$password' LIMIT 1";
        $query_user_result = mysql_query($sql, $connection);
        if (!$query_user_result) {
            die("Couldn't get user data: " . mysql_error());
        }
        $user = mysql_fetch_assoc($query_user_result);
        
        if (isset($_POST['login'])) {
            if ($user == NULL) {
                die("Wrong username or password!");
            } 
            
            $_SESSION['user'] = $user;
            header("Location: /");
        } 
        // User pressed register button, so create new user and 
        // redirect to bulletin
        else {
            if ($user != NULL) {
                die("User already existed!");
            }
            
            $sql = "INSERT INTO user(username, password, join_date) " .
                    "VALUES('$username', '$password', NOW())";
            $create_user_result = mysql_query($sql, $connection);
            if ($create_user_result) {
                $_SESSION['user'] = $user;
                header("Location: /");
            }
            else {
                die("Couldn't register new user: " . mysql_error());
            }
        }
        
        exit();
    }
?>

<div style="text-align:center; margin-top:200px">
    <h1>Simple BBS demo</h1>
    <form action = "<?php $_PHP_SELF ?>" method = "POST">
         Username: <input type = "text" name = "username" /><br/><br/>
         Password: <input type = "text" name = "password" /><br/><br/>
         <input type = "submit" name="login" value="Login"/>
         <input type = "submit" name="register" value="Register"/>
    </form>
</div>

</body>
</html>