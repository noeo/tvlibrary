<?php

function geturldata($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    
    curl_setopt($ch,CURLOPT_HTTPHEADER, array (
    "Accept-Charset:utf-8"
        ));
    
    
    curl_close($ch);
    
    return $content;
}