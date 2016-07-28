<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Sections : List";
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
$submit             = $myNewsConf['button']['submit'];

include($myNewsConf['head']);

section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin'){
	print "<p><b>You must be and Admin to View this Resource</b>";
} else {
?>

	<p>
	<b>Select Frontpage Sections</b><br>
	<a href="<?=$baseNewsAdmin_URI?>?mode=admin">Go back to admin</a>
	<p>
<blockquote>
<form method="post" action="<?=$baseNewsAdmin_URI?>?mode=sec_edited">
<table width="85%" border=0 cellpadding=2 cellspacing=1>
	<tr>
		<td><b>Section:</b></td>
		<td>&nbsp;</td>
	</tr>
	<tr>

<?
	$query = mysql_query("SELECT secid,section,front from " . $myNewsConf['db']['table']['sections'] . " order by section") or die (mysql_error());
	while($row = mysql_fetch_assoc($query)){
		$sid        = $row['secid'];
		$section    = $row['section'];
		$front      = $row['front'];

	unset($checked);
	if ($front == 1){
		$checked = "checked";
	}
	
	echo row_place($i);
?>

		<td valign="top" align="left">
			<p>
			<input class="textbox" type="checkbox" name="<?=$sid?>" <?=$checked?>> <?=$section?>
		</td>

<?
$i++;
} // End While
?>

	</tr>
</table>
    <?=$submit?>
</form>
</blockquote>

<?
} // End If

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
