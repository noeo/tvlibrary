<?php

function autologin($usr = "", $pwd = "")
{
    
 
    if (empty($_COOKIE['user_al']) && empty($usr) && empty($pwd)) {
        return false;
    } else {
        
        if (! empty($usr) && ! empty($pwd)) {
            
            $dump = query("SELECT * FROM `" . LOGIN_TABLE . "` WHERE email LIKE " . escapesql($usr) . "");
            
            
            if(empty($dump[0]["password"])) {
                return -1;
            }
            if (md5($pwd) == $dump[0]["password"]) {
                $login = true;
            }
        } else {
            
            $dump = query("SELECT * FROM " . LOGIN_TABLE . " WHERE MD5(CONCAT(email,password)) = " . escapesql($_COOKIE['user_al']) . "");
            
            if (! empty($dump[0]["password"])) {
                $login = true;
                $autologin = true;
            }
        }
        if (isset($login)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $dump[0]["name"];
    
            $_SESSION['email'] = $dump[0]["email"];
         
            
            
            
            if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
                if (php_sapi_name() == 'cgi') {
                    header('Status: 303 See Other');
                } else {
                    header('HTTP/1.1 303 See Other');
                }
            }
            
            return 1;
        }
        
        return - 1;
    }
}