<?
$TITLE =				$myNewsConf['default']['sitename'] . " Admin : Authors : Select Tool";
$baseAdmin_URI =		$myNewsConf['path']['web']['admin'];
$baseAuthAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

include($myNewsConf['head']);
section_header('Members','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin'){
	print "<p><b>You are not authorized to View this Resource</b>";
} else {
print <<<EOT
		<p>
	<b>What would you like to do?:</b><br>
	<A HREF=$baseAdmin_URI>Go back to <i>Main</i> admin</a>
	   <ul>
		<li><a href=$baseAuthAdmin_URI?mode=add>Add an Author</a><br>
		<li><a href=$baseAuthAdmin_URI?mode=edit_list>Modify/Delete Author</a><br>
	   </ul>
EOT;
}

section_footer('Members','content');
include($myNewsConf['foot']);
?>
