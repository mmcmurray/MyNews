<?
$TITLE=$myNewsConf['default']['sitename'] . " Admin : Links : Select Tool";

include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
print ("
	<p><b>What would you like to do?</b><br>
<a href=\"" . $myNewsConf['path']['web']['admin'] . "\">Go back to <i>Main</i> admin</a>
	<ul>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'] . "?mode=links_add\">Post a link.</a>
		<li><a href=\"" . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'] . "?mode=links_edit_list\">Modify/Delete a link.</a>
	</ul>
    ");
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
