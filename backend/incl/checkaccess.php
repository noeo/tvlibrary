<?php
require (dirname(__FILE__) . "/session.php");

if (! isset($_SESSION['loggedin']) || empty($_SESSION['loggedin'])) {
    
    if (loadf("autologin") != 1) {
        header('Location: ' . URL . 'login.php');
        exit();
    } else {
        define("session_yes", true);
    }
} else {
    
   
    
    define("session_yes", true);
}
