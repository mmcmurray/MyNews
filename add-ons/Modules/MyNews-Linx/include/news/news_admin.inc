<?
$TITLE=$myNewsConf['default']['sitename'] . " Admin : News : Select Tool";

include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
print ("
	<p><b>What would you like to do?</b><br>
<a href=\"" . $myNewsConf['path']['web']['admin'] . "\">Go back to <i>Main</i> admin</a>
	<ul>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=news_add\">Post an item.</a>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=news_edit_list\">Modify/Delete an item.</a>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=comments_select\">Administer Comments.</a>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=sub_list\">Post/Delete Submissions.</a>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=sec_list\">Modify Sections.</a>
	</ul>
    ");
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
