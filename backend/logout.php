<?php
require (dirname(__FILE__) . "/incl/init.php");
require (dirname(__FILE__) . "/incl/session.php");


session_destroy();

if (isset($_COOKIE['user_al'])) {
    @setcookie("user_al", null, (time() - 1), getdomain(URL, 'path'), getdomain(URL));
}

header('Location: ' . URL);
?>
