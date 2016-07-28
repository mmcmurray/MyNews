<?
$TITLE = $myNewsConf['default']['sitename'] . " Admin : Calendar : Select Tool";
$baseAdmin_URI =	$myNewsConf['path']['web']['admin'];
$baseCalAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
	print "<p><b>You do not have permission to view this resource!</b>"; 

} else {
?>

    <p><b>What would you like to do?</b><br>
	<a href="<?=$baseAdmin_URI?>">Go back to <i>Main</i> admin</a>
     <ul>
      <li><a href="<?=$baseCalAdmin_URI?>?mode=post">Post an event.</a>
      <li><a href="<?=$baseCalAdmin_URI?>?mode=edit">Modify a currently posted event.</a>
      <li><a href="<?=$baseCalAdmin_URI?>?mode=delete">Delete a currently posted event.</a>
     </ul>

<?
}

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
