<?php
define("checkaccess", true);
require (dirname(__FILE__) . "/incl/init.php");
require (dirname(__FILE__) . "/incl/_header.php");

$json = file_get_contents("http://tvhackday.mixd.tv/sources/?api_key=" . apikey . "");

print_r($json);
$json_array = json_decode($json, true);

  foreach ($json_array AS $k=>$v_array) {
 
 
  if(is_array($v_array)) {
  foreach ($v_array AS $kk) {
 

 
 // query("INSERT IGNORE INTO sources (source,entityid) VALUES (".escapesql($kk["name"]).",".escapesql($kk["entityId"]).")");
 
  
  
  }
  }
  }
$getsources = query("SELECT * FROM sources WHERE use_yes=1 AND `ignore`=0 LIMIT 10");

foreach ($getsources as $k => $row_array) {
    
    
    
   //$row_array["entityid"]='dca4e533-6bf5-58e1-972c-ad7f1651ddb7';
    
    $url = "http://tvhackday.mixd.tv/video-list/" . $row_array["entityid"] . "/?api_key=" . apikey . "&format=json";
    
    $json = loadf("geturldata", $url);
    
    $json_array = json_decode($json, true);
    
    $json_array = $json_array["msg"]["data"];
    
   // print_r($json_array);exit;
    
    if (! is_array($json_array)) {
        echo 'error:' . $row_array["source"];
    } else {
        
        query("UPDATE sources SET use_yes=1 WHERE sources_id=".$row_array["sources_id"]);
        
       foreach ($json_array as $k => $v_array) {
            
            if (is_array($v_array)) {
                
                $epgData='[]';
                
                foreach ($v_array as $v => $kk) {
                    
                    if(!is_array($kk)) {
                         $$v = utf8_decode($kk);
                    } else {
                         $$v = utf8_decode(json_encode($kk));
                    }
                }
                
                if($epgData) {
                    $epgData_yes=1;
                } else {
                    $epgData_yes=0;
                }
                
                
                    
                    if (!$showTitle) {
     
                        $showTitle = $name;
                    }
                        
                        query(" INSERT IGNORE INTO `videos` (
`videotitle` ,
`vtype` 
)
VALUES (
" . trim(escapesql(trim($showTitle))) . ",
'tvshow'
    )");
                        
                    
                    
                    $getvideoid = query("SELECT videos_id FROM videos WHERE `videotitle` LIKE " . trim(escapesql(trim($showTitle))) . "");
                    
                    $videos_id = $getvideoid[0]["videos_id"];
                    
                    if(!$broadcastTime) {
                        $broadcastTime=$publicationTime;
                    }
                    
                    if(!$broadcastTime) {
                        $broadcastTime=time();
                    }
                    
                    query("INSERT IGNORE INTO `tvevent` (
`videotitle` ,
`imageurl` ,
`videosid` ,
`watchurl` ,
`description` ,
`duration` ,
`showtitle` ,
`episode` ,
`videos_id` ,
`type` ,
`magnetId` ,
`publicationTime` ,
`fullVideo` ,
`season` ,
`episodeTitle` ,
`preview` ,
`premium` ,
`price` ,
`fileLocations` ,
`broadcaster` ,
`broadcastId` ,
`mobileLicense` ,
`ageRestriction` ,
`sources_id`,
epgData,
epgData_yes,
broadcastTime
)
VALUES (
" . escapesql($name) . ",
" . escapesql($image) . ",
" . escapesql($entityId) . ",
" . escapesql($url) . ",
" . escapesql($description) . ",
" . escapesql($duration) . ",
" . escapesql($showTitle) . ",
" . escapesql($episode) . ",
" . escapesql($videos_id) . ",
" . escapesql($type) . ",
" . escapesql($magnetId) . ",
" . escapesql($publicationTime) . ",
" . escapesql($fullVideo) . ",
" . escapesql($season) . ",
" . escapesql($episodeTitle) . ",
" . escapesql($preview) . ",
" . escapesql($premium) . ",
" . escapesql($price) . ",
" . escapesql($fileLocations) . ",
" . escapesql($broadcaster) . ",
" . escapesql($broadcastId) . ",
" . escapesql($mobileLicense) . ",
" . escapesql($ageRestriction) . ", 
" . escapesql($row_array["sources_id"]) . ",
" . escapesql($epgData) . ",
" . escapesql($epgData_yes) . ",
".  escapesql(date("Y-m-d H:i:s",$broadcastTime)).")"
    );
                }
            }
            
        
        // $jsonbc=file_get_contents("http://tvhackday.mixd.tv/video-list/".$row_array["entityid"]."/?api_key=e9ec497a-553c-11c8-abf2-82462f16f1f8");
    }
}

require (dirname(__FILE__) . "/incl/_footer.php");
?>