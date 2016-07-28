<?
$seclist_query = mysql_query("select distinct(section) from " . $myNewsConf['db']['table']['sections']);

if (!$seclist_query) { 
	print "Sql Error!&nbsp;&nbsp;&nbsp;" . "<b>" . mysql_errno() . "</b>" . "\n\t<br />\n" . "<!--\n" .  mysql_error() . "\n-->\n\t<br />\n";
	return; 
}

while($row = mysql_fetch_assoc($seclist_query)){
	$seclist    = $row["section"];
?>
	<option><?=$seclist?></option>
<?
}
?>
