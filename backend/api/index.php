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
    
    $getgenres = query("SELECT genres_id FROM videos_genres WHERE `videos_id` = " . escapesql($_REQUEST["item"]) . "");
    
    if (is_array($getgenres)) {
        
        foreach ($getgenres as $genre_array) {
            
            if (! empty($genre_array["genres_id"])) {
                query("INSERT IGNORE INTO ignorelist_genres (genres_id,users_id,icount) VALUES (" . escapesql(utf8_decode($genre_array["genres_id"])) . "," . escapesql($userid) . ",0)");
                query("UPDATE ignorelist_genres SET icount=icount+1 WHERE genres_id=" . escapesql(utf8_decode($genre_array["genres_id"])) . " AND users_id=" . escapesql($userid) . "");
            }
        }
    }
    
    $return["result"] = 'success';
    
    die(json_encode($return));
}

if ($action == 'get') {
    
    if (empty($_SESSION["lastitem"])) {
        $_SESSION["lastitem"] = 0;
    }
    
    query("SET CHARACTER SET utf8;");
    $getdata = query("SELECT
tvevent.`videos_id` AS item,
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
 source AS   broadcaster, (SELECT icount FROM ignorelist_genres INNER JOIN videos_genres ON ignorelist_genres.genres_id=videos_genres.genres_id WHERE videos_genres.videos_id=tvevent.`videos_id` AND users_id = " . escapesql($userid) . " LIMIT 1) AS sortorder
FROM tvevent LEFT JOIN sources ON tvevent.sources_id=sources.sources_id
INNER JOIN videos ON tvevent.videos_id=videos.videos_id
INNER JOIN videos_genres ON videos.videos_id=videos_genres.videos_id
WHERE 
videos_genres.genres_id NOT IN (SELECT genres_id FROM ignorelist_genres WHERE icount>3 AND users_id = " . escapesql($userid) . " ) AND
tvevent.`videos_id` NOT IN (" . escapesql($_SESSION["lastitem"]) . ") AND sources.`ignore`=0 AND fullVideo=1 AND premium=1 AND
videos_genres.genres_id NOT IN (SELECT genres_id FROM skiplist_genres WHERE users_id = " . escapesql($userid) . " AND datecreated>=" . escapesql(date("Y-m-d H:i:s", time() - (60 * 60 * 12))) . ") AND
tvevent.videos_id NOT IN (SELECT skiplist.videos_id FROM skiplist WHERE users_id = " . escapesql($userid) . " AND datecreated>=" . escapesql(date("Y-m-d H:i:s", time() - (60 * 60 * 12))) . ") AND
tvevent.videos_id NOT IN (SELECT ignorelist.videos_id FROM ignorelist WHERE users_id = " . escapesql($userid) . ")
    GROUP BY tvevent.videos_id
    ORDER BY sortorder,broadcastTime DESC
    LIMIT " . trim($limit));
    
    if (empty($getdata)) {
        
        query("DELETE FROM skiplist_genres WHERE users_id=" . escapesql($userid));
        query("SET CHARACTER SET utf8;");
        $getdata = query("SELECT 
tvevent.`videos_id` AS item,
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
 source AS   broadcaster, (SELECT icount FROM ignorelist_genres INNER JOIN videos_genres ON ignorelist_genres.genres_id=videos_genres.genres_id WHERE videos_genres.videos_id=tvevent.`videos_id` AND users_id = " . escapesql($userid) . " LIMIT 1) AS sortorder
FROM tvevent LEFT JOIN sources ON tvevent.sources_id=sources.sources_id
INNER JOIN videos ON tvevent.videos_id=videos.videos_id
INNER JOIN videos_genres ON videos.videos_id=videos_genres.videos_id
WHERE videos_genres.genres_id NOT IN (SELECT genres_id FROM ignorelist_genres WHERE icount>3 AND users_id = " . escapesql($userid) . " ) AND
    tvevent.`videos_id` NOT IN (" . escapesql($_SESSION["lastitem"]) . ") AND sources.`ignore`=0 AND fullVideo=1 AND premium=1 AND
    tvevent.videos_id NOT IN (SELECT skiplist.videos_id FROM skiplist WHERE users_id = " . escapesql($userid) . " AND datecreated>=" . escapesql(date("Y-m-d H:i:s", time() - (60 * 60 * 12))) . ") AND
    tvevent.videos_id NOT IN (SELECT ignorelist.videos_id FROM ignorelist WHERE users_id = " . escapesql($userid) . ") 
    GROUP BY tvevent.videos_id
    ORDER BY sortorder,broadcastTime DESC
    LIMIT " . trim($limit));
    }
    
    if (empty($getdata)) {
        query("SET CHARACTER SET utf8;");
        $getdata = query("SELECT
tvevent.`videos_id` AS item,
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
 source AS   broadcaster, (SELECT icount FROM ignorelist_genres INNER JOIN videos_genres ON ignorelist_genres.genres_id=videos_genres.genres_id WHERE videos_genres.videos_id=tvevent.`videos_id` AND users_id = " . escapesql($userid) . " LIMIT 1) AS sortorder
FROM tvevent LEFT JOIN sources ON tvevent.sources_id=sources.sources_id
INNER JOIN videos ON tvevent.videos_id=videos.videos_id
INNER JOIN videos_genres ON videos.videos_id=videos_genres.videos_id
WHERE videos_genres.genres_id NOT IN (SELECT genres_id FROM ignorelist_genres WHERE icount>3 AND users_id = " . escapesql($userid) . " ) AND
            tvevent.`videos_id` NOT IN (" . escapesql($_SESSION["lastitem"]) . ") AND sources.`ignore`=0 AND 
    tvevent.videos_id NOT IN (SELECT skiplist.videos_id FROM skiplist WHERE users_id = " . escapesql($userid) . " AND datecreated>=" . escapesql(date("Y-m-d H:i:s", time() - (60 * 60 * 12))) . ") AND
    tvevent.videos_id NOT IN (SELECT ignorelist.videos_id FROM ignorelist WHERE users_id = " . escapesql($userid) . ")
    GROUP BY tvevent.videos_id
    ORDER BY sortorder,broadcastTime DESC
    LIMIT " . trim($limit));
    }
    
    if (empty($getdata)) {
        
        query("DELETE FROM skiplist WHERE users_id=" . escapesql($userid));
        query("SET CHARACTER SET utf8;");
        $getdata = query("SELECT 
tvevent.`videos_id` AS item,
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
 source AS   broadcaster, (SELECT icount FROM ignorelist_genres INNER JOIN videos_genres ON ignorelist_genres.genres_id=videos_genres.genres_id WHERE videos_genres.videos_id=tvevent.`videos_id` AND users_id = " . escapesql($userid) . " LIMIT 1) AS sortorder
FROM tvevent LEFT JOIN sources ON tvevent.sources_id=sources.sources_id
INNER JOIN videos ON tvevent.videos_id=videos.videos_id
INNER JOIN videos_genres ON videos.videos_id=videos_genres.videos_id
WHERE tvevent.`videos_id` NOT IN (" . escapesql($_SESSION["lastitem"]) . ") AND sources.`ignore`=0 AND fullVideo=1 AND premium=1 AND
    tvevent.videos_id NOT IN (SELECT skiplist.videos_id FROM skiplist WHERE users_id = " . escapesql($userid) . " AND datecreated>=" . escapesql(date("Y-m-d H:i:s", time() - (60 * 60 * 12))) . ") AND
    tvevent.videos_id NOT IN (SELECT ignorelist.videos_id FROM ignorelist WHERE users_id = " . escapesql($userid) . ") 
    GROUP BY tvevent.videos_id
    ORDER BY sortorder,broadcastTime DESC
    LIMIT " . trim($limit));
    }
    
    if (! empty($getdata[0]["item"])) {
        $_SESSION["lastitem"] = $getdata[0]["item"];
    }
    
    if (! empty($getdata[0]["entityId"])) {
        
        foreach ($getdata as $list => $V_array) {
            
            foreach ($V_array as $K => $V) {
                if (! is_numeric($K)) {
                    query(" INSERT IGNORE INTO `skiplist` (
`videosid` ,
`videos_id` ,
`users_id` ,
`session_id`,
`datecreated`
)
VALUES (
" . escapesql($V_array["entityId"]) . ",
" . escapesql($V_array["item"]) . ",
" . escapesql($userid) . ",
" . escapesql(session_id()) . ",
" . escapesql(date("Y-m-d H:i:s")) . "
    )");
                    
                    $getgenres = query("SELECT genres_id FROM videos_genres WHERE `videos_id` = " . escapesql($V_array["item"]) . "");
                    
                    if (is_array($getgenres)) {
                        
                        foreach ($getgenres as $genre_array) {
                            
                            if (! empty($genre_array["genres_id"])) {
                                query(" INSERT IGNORE INTO `skiplist_genres` (
`users_id` ,
`genres_id`,
`session_id`,
`datecreated`
)
VALUES (
" . escapesql($userid) . ",
" . escapesql($genre_array["genres_id"]) . ",
" . escapesql(session_id()) . ",
" . escapesql(date("Y-m-d H:i:s")) . "
    )");
                            }
                        }
                    }
                }
            }
        }
    }
    
    $jsondata = array();
    
    foreach ($getdata as $list => $V_array) {
        
        foreach ($V_array as $K => $V) {
            if (! is_numeric($K)) {
                if ($K == 'epgData') {
                    $V = json_decode($V, true);
                }
                if ($K != 'sortorder1') {
                    if ($K == 'broadcastTime') {
                        
                        if (empty($V) || $V == '0000-00-00 00:00:00') {
                            $V = date('Y-m-d H:i:s', time() - (60 * 60 * 14));
                        }
                        
                        if (! empty($months[(date('n', strtotime($V)) - 1)])) {
                            $tmonth = utf8_encode($months[(date('n', strtotime($V)) - 1)]);
                        } else {
                            $tmonth = date('F', strtotime($V));
                        }
                        $date = date('d. ', strtotime($V));
                        $date .= $tmonth;
                        $date .= date(' Y, H:i \U\h\r', strtotime($V));
                        
                        $V = $date;
                    }
                    
                    if (empty($V))
                        $V = '';
                    $jsondata[$list][$K] = ($V);
                }
            }
        }
        
        $genre_array = array();
        if (! empty($getdata[0]["entityId"])) {
            $getgenres = query("SELECT genres.genre FROM genres INNER JOIN videos_genres ON genres.genres_id=videos_genres.genres_id
            WHERE `videos_id` = " . escapesql($getdata[0]["item"]) . " ORDER BY gtype LIMIT 1");
            
            if (is_array($getgenres)) {
                
                foreach ($getgenres as $genre_array) {
                    
                    if (! empty($genre_array["genre"])) {
                        $genre_array[] = $genre_array["genre"];
                    }
                }
            }
        }
        
        $jsondata[$list]['genre'] = implode(", ", array_unique($genre_array));
    }
    
    if (! empty($_REQUEST["debug"])) {
        print_r($jsondata);
        exit();
    }
    
    echo json_encode($jsondata);
}

?>