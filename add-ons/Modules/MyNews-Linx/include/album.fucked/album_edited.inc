<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'] ."?mode=album_edit_list&album=" . urlencode($_POST['album']);?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Album : Updated : " . $_POST['text']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

$insert = "replace into " . $myNewsConf['db']['table']['album'] . " values
	('" . addslashes($_GET['picid'])		. "',
	 '" . addslashes($_POST['filename'])	. "',
	 '" . addslashes($_POST['author'])		. "',
	 '" . addslashes($_POST['album'])		. "',
	 '" . addslashes($_POST['text'])		. "',
	 '" . addslashes($_POST['extended'])	. "',
	 '" . addslashes($_POST['date'])		. "')";
$result = MYSQL_QUERY($insert) or die(mysql_error());

?>
		<br />
		<br />
		The item <b><?php echo $_POST['text']?></b> has been updated in album: <b><?php echo $_POST['album']?></b>.
	</body>
</html>
