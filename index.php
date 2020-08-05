<?php

header('refresh: 600');
require_once("includes/config.php");
require_once("includes/mysql.php");
require_once("includes/utils.php");
require_once("includes/templates.php");
require_once("includes/web_classes.php");
require_once("includes/channel_func.php");
require_once("includes/control_func.php");
require_once("includes/language.php");

session_start();
header('Content-type: text/html; charset=utf-8');

require_once("includes/auth.php");
require_once("includes/plugins.php");

$data['global_header']=template_parse('global_header.html',$data);
echo template_parse("main.html",$data);


?>
