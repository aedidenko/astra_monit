<?php

include 'XMPP.php';
$conn = new XMPPHP_XMPP ('195.82.154.2', 5222, 'ardee@195.82.154.2', '123QWEasd', 'xmpphp', '195.82.154.2', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
try
{
	$conn->connect();
	$conn->processUntil('session_start');
	$conn->presence();
	$conn->message('didenko@195.82.154.2', 'test2test');
	$conn->disconnect();
}
catch(XMPPHP_Exception $e)
{
	die($e->getMessage());
}
?>