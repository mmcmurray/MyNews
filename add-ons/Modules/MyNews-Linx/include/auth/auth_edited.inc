<?
if (!isset($_POST['newpass']) || $_POST['newpass'] == ''){
	$newpass = $_POST['oldpass'];
} else {
	$newpass = crypt($_POST['newpass'], 'lI');
} // End If
?>

<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['name']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

$insert = "replace into " . $myNewsConf['db']['table']['authors'] . " values(
	'" . addslashes($_GET['uid'])			. "',
	'" . addslashes($_POST['name'])		. "',
	'" . addslashes($_POST['bio'])			. "',
	'" . addslashes($_POST['auth_level'])	. "',
	'" . addslashes($_POST['email'])		. "',
	'" . addslashes($_POST['url'])			. "',
	'" . addslashes($_POST['date'])		. "',
	'" . addslashes($_POST['active'])		. "',
	'" . addslashes($_POST['login'])		. "',
	'" . $newpass . "',
	'" . addslashes($_POST['listening'])	. "',
	'" . addslashes($_POST['reading'])		. "',
	'" . addslashes($_POST['thinking'])	. "')";
$result = MYSQL_QUERY($insert) or die(mysql_error());
?>
		<br />
		<br />
		<b><?php echo $_POST['name']?></b> (<i><?php echo $_GET['uid']?></i>)
		   has been updated.
	</body>
</html>
