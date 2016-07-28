<?
$categorylist_query = mysql_query("select distinct(category) from " . $myNewsConf['db']['table']['portal'] . " order by category");

if (!$categorylist_query) { 
	print "Sql Error!&nbsp;&nbsp;&nbsp;" . "<b>" . mysql_errno() . "</b>" . "\n\t<br />\n" . "<!--\n" .  mysql_error() . "\n-->\n\t<br />\n";
	return; 
}

while($row = mysql_fetch_assoc($categorylist_query)){
	$catlist    = $row["category"];
?>
	<option><?=$catlist?></option>
<?
}
?>
