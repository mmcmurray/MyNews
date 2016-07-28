<?
$cat_list_query = mysql_query("select distinct(category) from " . $myNewsConf['db']['table']['portal']);

if (!$cat_list_query) {
	print "Sql Error!&nbsp;&nbsp;&nbsp;" . "<b>" . mysql_errno() . "</b>" . "\n\t<br />\n" . "<!--\n" .  mysql_error() . "\n-->\n\t<br />\n";
	return;
}

while($row = mysql_fetch_assoc($cat_list_query)){
     $category = $row["category"];

     $genre_list_query = mysql_query("select distinct(genre) from " . $myNewsConf['db']['table']['portal'] . " where category='" . $category . "'");

     if (!$genre_list_query) {
           print "Sql Error!&nbsp;&nbsp;&nbsp;" . "<b>" . mysql_errno() . "</b>" . "\n\t<br />\n" . "<!--\n" .  mysql_error() . "\n-->\n\t<br />\n";
	   return;
     }

?>
     <OPTGROUP label="<?=$category?>">
<?
     while($row = mysql_fetch_assoc($genre_list_query)){
	  $genre   = $row["genre"];
?>
     <option><?=$genre?></option>
<?
     }
?>
     </OPTGROUP>
<?
}
?>
