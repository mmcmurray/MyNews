<?
require($_SERVER['DOCUMENT_ROOT'] . "/include/config.inc.php");
include($myNewsConf['path']['sys']['admin'] . "/include/auth.inc");
include($myNewsConf['path']['sys']['index'] . "/include/libs/lib.inc.php");
include($myNewsConf['path']['sys']['admin'] . "/include/admin.lib.php");

if (!isset($_GET['mode'])){ $_GET['mode'] = "admin"; } //End If

mynews_connect($myNewsConf['db']['hostname'] , $myNewsConf['db']['dbUser'] , $myNewsConf['db']['dbPass'] , $myNewsConf['db']['dbName']);

if ($_GET['mode'] == "admin"):
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_admin.inc");

elseif ($_GET['mode'] ==  "links_add"):
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_add.inc");

elseif ($_GET['mode'] ==  "links_added"):
	require ($myNewsConf['path']['sys']['admin'] . "/include/links/links_added.inc");

elseif ($_GET['mode'] == "links_edit_list") :
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_edit_list.inc");
 
elseif ($_GET['mode'] ==  "links_edit"):
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_edit.inc");
 
elseif ($_GET['mode'] ==  "links_edited"):
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_edited.inc");

elseif ($_GET['mode'] ==  "links_deleted"):
	require($myNewsConf['path']['sys']['admin'] . "/include/links/links_deleted.inc");
endif;

MYSQL_CLOSE();
?>
