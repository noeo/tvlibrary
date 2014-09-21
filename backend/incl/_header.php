<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?=calltext("DASHBOARD_TITLE")?></title>

<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
<link href="css/sb-admin.css" rel="stylesheet" />


<?php if(file_exists(dirname(__FILE__) . "/../custom/css/theme.css")) {?>
<link href="custom/css/theme.css" rel="stylesheet" />
<?php }?>


<?php if(defined("session_yes")) {?>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/amcharts.js"></script>
<script type="text/javascript" src="js/serial.js"></script>
<script type="text/javascript" src="js/pie.js"></script>
<script type="text/javascript" src="js/themes/light.js"></script>
<script type="text/javascript" src="js/exporting/amexport.js"></script>
<script type="text/javascript" src="js/exporting/rgbcolor.js"></script>
<script type="text/javascript" src="js/exporting/canvg.js"></script>
<script type="text/javascript" src="js/exporting/filesaver.js"></script>
<script type="text/javascript" src="js/exporting/jspdf.js"></script>
<script type="text/javascript"
	src="js/exporting/jspdf.plugin.addimage.js"></script>
<script type="text/javascript" src="js/xlsx.js"></script>
<?php }?>
</head>

<body>