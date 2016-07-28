<?
require($_SERVER['DOCUMENT_ROOT'] . "/include/config.inc.php");
include($myNewsConf['path']['sys']['admin'] . "/include/auth.inc");
include($myNewsConf['path']['sys']['index'] . "/include/libs/lib.inc.php");
include($myNewsConf['path']['sys']['admin'] . "/include/admin.lib.php");

if (!isset($_GET['mode'])){ $_GET['mode'] = "admin"; } //End If

mynews_connect($myNewsConf['db']['hostname'] , $myNewsConf['db']['dbUser'] , $myNewsConf['db']['dbPass'] , $myNewsConf['db']['dbName']);

if ($_GET['mode'] == "admin"):
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_admin.inc");

elseif ($_GET['mode'] ==  "portal_add"):
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_add.inc");

elseif ($_GET['mode'] ==  "portal_added"):
	require ($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_added.inc");

elseif ($_GET['mode'] == "portal_edit_list") :
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_edit_list.inc");
 
elseif ($_GET['mode'] ==  "portal_edit"):
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_edit.inc");
 
elseif ($_GET['mode'] ==  "portal_edited"):
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_edited.inc");

elseif ($_GET['mode'] ==  "portal_deleted"):
	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_deleted.inc");

endif;

MYSQL_CLOSE();
?>
