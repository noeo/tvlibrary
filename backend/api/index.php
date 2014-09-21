<?php
// define("checkaccess", true);
require (dirname(__FILE__) . "/../incl/init.php");
session_start();

header("Content-Type: application/json");

if (empty($_REQUEST["action"])) {
    
    $action = 'get';
} else {
    
    $action = $_REQUEST["action"];
}

if (empty($_REQUEST["limit"])) {
    
    $limit = 1;
} else {
    
    $limit = $_REQUEST["limit"];
    
    if (! is_numeric($limit)) {
        $limit = 1;
    }
}

if (empty($_REQUEST["user"])) {
    
    $return["result"] = 'error';
    $return["msg"] = 'user id is missing';
    
    die(json_encode($return));
}

$getusrid = query("SELECT userid FROM users WHERE `password` =" . escapesql($_REQUEST["user"]) . "");

if (empty($getusrid[0]["userid"])) {
    
    query(" INSERT IGNORE INTO `users` (
`name`,
`password`
)
VALUES (
" . escapesql($_REQUEST["user"]) . ",
" . escapesql($_REQUEST["user"]) . "
    )");
    
    $getusrid = query("SELECT userid FROM users WHERE `password` =" . escapesql($_REQUEST["user"]) . "");
    
    if (empty($getusrid[0]["userid"])) {
        
        $return["result"] = 'error';
        $return["msg"] = 'invalid usr id';
        
        die(json_encode($return));
    }
    
    $userid = $getusrid[0]["userid"];
} else {
    $userid = $getusrid[0]["userid"];
}

// ignore, next,

if ($action == 'ignore') {
    
    if (empty($_REQUEST["item"])) {
        
        $return["result"] = 'error';
        $return["msg"] = 'item is missing';
        
        die(json_encode($return));
    }
    
    $getvideoid = query("SELECT videos_id FROM videos WHERE `videos_id` = " . escapesql($_REQUEST["item"]) . "");
    
    if (empty($getvideoid[0]["videos_id"])) {
        
        $return["result"] = 'error';
        $return["msg"] = 'invalid value for item';
        
        die(json_encode($return));
    }
    
    
    
    query(" INSERT IGNORE INTO `ignorelist` (
`videos_id` ,
`users_id`
)
VALUES (
" . escapesql($getvideoid[0]["videos_id"]) . ",
" . escapesql($userid) . "
    )");
    
    $getgenres= query("SELECT genres_id FROM videos_genres WHERE `videos_id` = " . escapesql($_REQUEST["item"]) . "");
    
   
    if(is_array($getgenres)) {
        
        foreach ($getgenres AS $genre_array) {
            
            if(!empty($genre_array["genres_id"])) {
            query("INSERT IGNORE INTO ignorelist_genres (genres_id,users_id) VALUES (".escapesql(utf8_decode($genre_array["genres_id"])).",".escapesql($userid).")");
        }
        }
        
    }
    
    $return["result"] = 'success';
    
    die(json_encode($return));
}

if ($action == 'get') {
    
    if (! empty($_REQUEST["debug"])) {
        query("SET CHARACTER SET utf8;");
        $getdata = query("SELECT
`videos_id` AS item,
`showtitle` AS name,
`episodeTitle` AS episodeTitle,
`imageurl` AS image ,
`videosid` AS entityId,
`watchurl` AS url,
`description` ,
`duration` ,
`showtitle`  AS showTitle,
`episode` ,
`sources_id`,
broadcastTime, epgData
FROM tvevent
WHERE
   tvevent_id= 8462
    LIMIT 1");
    } else {
        
        query("SET CHARACTER SET utf8;");
        $getdata = query("SELECT 
`videos_id` AS item,
`showtitle` AS name,
`episodeTitle` AS episodeTitle,
`imageurl` AS image ,
`videosid` AS entityId,
`watchurl` AS url,
`description` ,
`duration` ,
`showtitle`  AS showTitle,
`episode` ,
tvevent.`sources_id`,
broadcastTime,
 source AS   broadcaster
FROM tvevent LEFT JOIN sources ON tvevent.sources_id=sources.sources_id
WHERE sources.`ignore`=0 AND fullVideo=1 AND premium=1 AND
    videos_id NOT IN (SELECT videos_id FROM skiplist WHERE users_id = " . escapesql($userid) . " AND datecreated>=".escapesql(date("Y-m-d H:i:s",time()-(60*60*12))).") AND
    videos_id NOT IN (SELECT videos_id FROM ignorelist WHERE users_id = " . escapesql($userid) . ") 
    GROUP BY videos_id
    ORDER BY broadcastTime DESC
    LIMIT " . trim($limit));
    }
    
    if (! empty($getdata[0]["entityId"])) {
        query(" INSERT IGNORE INTO `skiplist` (
`videosid` ,
`videos_id` ,
`users_id` ,
`session_id`,
`datecreated`
)
VALUES (
" . escapesql($getdata[0]["entityId"]) . ",
" . escapesql($getdata[0]["item"]) . ",
" . escapesql($userid) . ",
" . escapesql(session_id()) . ",
" . escapesql(date("Y-m-d H:i:s")) . "
    )");
    }
    
    $jsondata = array();
    
    foreach ($getdata as $list => $V_array) {
        
        foreach ($V_array as $K => $V) {
            if (! is_numeric($K)) {
                if ($K == 'epgData') {
                    $V = json_decode($V, true);
                }
                if ($K == 'broadcastTime') {
                    $V = date('d. F Y, H:i \U\h\r', strtotime($V));
                }
                
                if(empty($V)) $V='';
                $jsondata[$list][$K] = ($V);
            }
        }
    }
    
    if (! empty($_REQUEST["debug"])) {
        print_r($jsondata);
        exit();
    }
    
    echo json_encode($jsondata);
}

?>