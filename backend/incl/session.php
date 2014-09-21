<?php 

$lifetime = 1200;
session_name("tvhackday");
session_set_cookie_params($lifetime, getdomain(URL, 'path'), getdomain(URL));

session_start();
setcookie(session_name(), session_id(), time() + $lifetime, getdomain(URL, 'path'), getdomain(URL));
