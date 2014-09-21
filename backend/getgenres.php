<?php
define("checkaccess", true);
require (dirname(__FILE__) . "/incl/init.php");
require (dirname(__FILE__) . "/incl/_header.php");



$getdata = query("SELECT
*
FROM tvevent
WHERE epgData_yes=1
    LIMIT 10000");

foreach ($getdata as $part_array) {
    
    $meta = json_decode($part_array["epgData"], true);
    
    // print_r($meta);
    $genres=search_in_array("genre", $meta);
    
    if(!empty($genres)) {
        print_r($genres);
        
        foreach ($genres AS $genre) {
            query("INSERT IGNORE INTO genres (genre,gtype) VALUES (".escapesql(utf8_decode($genre)).",'')");  
        
        
        $getgenre=query("SELECT genres_id FROM genres WHERE `genre` =" . escapesql(utf8_decode($genre)) . "");
        
    
        if(!empty($getgenre[0]["genres_id"])) {
            
            query("INSERT IGNORE INTO videos_genres (videos_id,genres_id) VALUES (".escapesql($part_array["videos_id"]).",".escapesql($getgenre[0]["genres_id"]).")");
        }
        }
        
        
        echo '<br>';
    }
}

require (dirname(__FILE__) . "/incl/_footer.php");
?>