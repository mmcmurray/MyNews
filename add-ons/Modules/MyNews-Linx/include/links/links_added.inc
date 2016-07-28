<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Added : " . stripslashes($_POST['title']); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if($_POST['category'] == 'none'){
	$category = $_POST['new_category'];
} else {
	$category = $_POST['category'];
}

if($_POST['genre'] == 'none'){
	$genre = $_POST['new_genre'];
} else {
	$genre = $_POST['genre'];
}

$_POST['title'] = fixWordShit($_POST['title']);
$_POST['description'] = fixWordShit($_POST['description']);
$category = fixWordShit($category);
$genre = fixWordShit($genre);

$insert = "insert into " . $myNewsConf['db']['table']['portal'] . " values(
	'',
	'" . addslashes($_POST['title'])		. "',
	'" . addslashes($_POST['description'])		. "',
	'" . addslashes($_POST['url'])			. "',
	'" . addslashes($_POST['email'])		. "',
	'',
	'" . addslashes($category)             . "',
	'" . addslashes($genre)             	. "',
	'',
	'" . addslashes($_POST['author'])		. "')";
$result = MYSQL_QUERY($insert);
$sid = mysql_insert_id();
?>
		<br />
		<br />
		The item <b><?php echo $_POST['title']?></b> has been added
		as item number: <b><?php echo $sid?></b>.
	</body>
</html>
