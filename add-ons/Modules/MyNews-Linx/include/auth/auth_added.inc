<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors']?>">
	<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Added : " . $_POST['login']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
<?
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$cryptpass = crypt($_POST['newpass'], 'lI');
$insert = "insert into " . $myNewsConf['db']['table']['authors'] . " values(
	'',
	'" . $_POST['name']		. "',
	'" . $_POST['bio']			. "',
	'" . $_POST['auth_level']	. "',
	'" . $_POST['email']		. "',
	'" . $_POST['url']			. "',
	'" . date('Y-m-d H:i:s')			. "',
	'" . $_POST['active']		. "',
	'" . $_POST['login']		. "',
	'" . $cryptpass						. "',
	'',
	'',
	'')";
$result = MYSQL_QUERY($insert) or die(mysql_error());
?>
		<p>
		<b><?php echo $_POST['name']?></b> has been added as: 
		<blockquote>
		<p>
		<b>User:</b> <?php echo $_POST['login']?>
		<br>
		<b>Pass:</b> <?php echo $_POST['newpass']?>
		</blockquote>
	</body>
</html>
