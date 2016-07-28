<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['portal']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Added : " . stripslashes($_POST['title']); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if($_POST['section'] == 'none'){
	$section = $_POST['new_section'];
	$insert = mysql_query("INSERT into " . $myNewsConf['db']['table']['sections'] . " values('NULL','" . addslashes($section) . "','0')") or die(mysql_error());
} else {
	$section = $_POST['section'];
}

$_POST['title'] = fixWordShit($_POST['title']);
$_POST['previewtext'] = fixWordShit($_POST['previewtext']);

$insert = "insert into " . $myNewsConf['db']['table']['linx'] . " values(
	'',
	'" . addslashes($_POST['title'])		. "',
	'" . addslashes($_POST['previewtext'])	. "',
	'',
	'" . addslashes($_POST['author'])		. "',
	'" . addslashes($_POST['email'])		. "',
	'" . date('Y-m-d H:i:s')						. "',
	'" . addslashes($section)						. "',
	'',
	'')";
$result = MYSQL_QUERY($insert);
$linkid = mysql_insert_id();

require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_rdf.inc");
?>
		<br />
		<br />
		The item <b><?php echo $_POST['title']?></b> has been added
		as item number: <b><?php echo $linkid?></b>.
	</body>
</html>
