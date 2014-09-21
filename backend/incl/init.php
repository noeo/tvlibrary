<?php
define("ROOTPATH", realpath(dirname(__FILE__) . "/../") . DIRECTORY_SEPARATOR);

require_once (dirname(__FILE__) . "/../conf/settings.php");
require_once (dirname(__FILE__) . "/../incl/texts.php");

if (file_exists(dirname(__FILE__) . "/../conf/customtexts.php")) {
    require_once (dirname(__FILE__) . "/../conf/customtexts.php");
}

define("FUNCTPATH", dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functs' . DIRECTORY_SEPARATOR);
require_once (dirname(__FILE__) . "/../incl/functions.php");
require_once (dirname(__FILE__) . "/../incl/functloader.php");

if (defined("checkaccess")) {
    require_once (dirname(__FILE__) . "/../incl/checkaccess.php");
}



header('Content-Type: text/html; charset=utf-8');

define("init_yes", true);