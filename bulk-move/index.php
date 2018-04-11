<?php

define('OFFSET_PATH', 3);
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/admin-globals.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/zp-core/template-functions.php');
admin_securityChecks(ALBUM_RIGHTS, currentRelativeURL());
require_once('functions.php');

if (isset($_GET["action"]))
{
	drawResults();
}
else if (isset($_POST["filenames"]))
{
	processRequest();
}
else
{
	include_once('form.php');
}
?>