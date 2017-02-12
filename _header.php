<div class="header">
    <div class="header-left">
        <a class="header-link" href="/">Home</a>
    </div>
    <div class="header-right">
        <?php
            if (isset($_SESSION['user'])) {
                echo "<a class=\"header-link\" href=\"/login.php?logout=yes\">Log out</a>";
            }
            else {
                echo "<a class=\"header-link\" href=\"/login.php\">Log in</a>";
            }
        ?>
    </div>
</div>