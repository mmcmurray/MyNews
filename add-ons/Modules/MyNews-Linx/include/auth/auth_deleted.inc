<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'] . "?mode=edit_list"; ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Deleted";?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
<?
$query  = mysql_query("SELECT name from " . $myNewsConf['db']['table']['authors'] . " where artnr = " . $_GET['uid']) or die (mysql_error());
$row    = mysql_fetch_assoc($query);
$name   = stripslashes($row['name']);

$query = "DELETE FROM " . $myNewsConf['db']['table']['authors'] . " WHERE artnr = " . $_GET['uid'];
$insert = MYSQL_QUERY($query);
?>
		<p>
		<b><?php echo stripslashes($name); ?></b> (<i><?php echo $_GET['uid']?></i>)
		  has been deleted.
	</body>
</html>
