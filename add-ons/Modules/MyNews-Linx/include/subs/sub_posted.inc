<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=sub_list"; ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Added : " . $_POST['title']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$query = "INSERT into " . $myNewsConf['db']['table']['news'] . " values (
	'',
	'" . addslashes($_POST['title'])		. "',
	'" . addslashes($_POST['previewtext'])	. "',
	'',
	'" . addslashes($_POST['author'])		. "',
	'" . addslashes($_POST['email'])		. "',
	'" . date('Y-m-d H:i:s')						. "',
	'" . addslashes($_POST['section'])		. "',
	'',
	'')";

$insert = mysql_query($query) or die(mysql_error());

$sid = mysql_insert_id();

require($myNewsConf['path']['sys']['admin'] . "/include/news/news_rdf.inc");
?>
        <br />
        <br />
		The item <b><?php echo $_POST['title']?></b> has been added
		as item number: <b><?php echo $sid?></b>.
	</body>
</html>
