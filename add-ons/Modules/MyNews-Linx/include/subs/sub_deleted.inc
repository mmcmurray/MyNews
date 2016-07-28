<?
$query = mysql_query("SELECT title from " . $myNewsConf['db']['table']['submissions'] . " where artnr = " . $_GET['sid']) or die (mysql_error());
$row = mysql_fetch_assoc($query);
	$title = $row["title"];
?>
<meta http-equiv="Refresh" content="1; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=sub_list"; ?>">
<html>
	<head>
		<title><?php echo "$sitename Admin : Submit : Deleted : " . stripslashes($title); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/". $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?
$delete = MYSQL_QUERY("DELETE FROM " . $myNewsConf['db']['table']['submissions'] . " WHERE artnr = " . $_GET['sid']) or die(mysql_error());
?>
		<br />
		<br />
		Submission <b>"<?php echo stripslashes($title); ?>"</b> 
		with item number: <b><?php echo $_GET['sid']?></b> has been deleted. 
	</body>
</html>
