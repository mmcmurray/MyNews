<?
$newpass =		$_POST['newpass'];
$confirmpass =	$_POST['confirmpass'];
$oldpass =		$_POST['oldpass'];

if ($_POST['newpass'] == $_POST['confirmpass']){
    if ($newpass == ''){ 
       $pass = $_POST['oldpass'];
    } else {
       $pass = crypt($_POST['newpass'], "lI");
    } // End If
} else {
?>
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename']?> ERROR!</title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body bgcolor="<?php echo "$bg_color"; ?>" text="<?php echo "$text_color"; ?>">
		<p>
		<b>Error:</b> passwords do not match.
	</body>
</html>
<?
die;
} //End If
?>

<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['preferences']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['name']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$insert = "replace into " . $myNewsConf['db']['table']['authors'] . " values(
	'" . $_GET['uid'] ."',
	'" . addslashes($_POST['name']) ."',
	'" . addslashes($_POST['bio']) ."',
	'" . $_POST['status'] ."',
	'" . $_POST['email'] ."',
	'" . $_POST['url'] ."',
	'" . $_POST['date'] ."',
	'" . $_POST['active'] ."',
	'" . $_POST['user'] ."',
	'" . $pass ."',
	'" . addslashes($_POST['listening']) ."',
	'" . addslashes($_POST['reading']) ."',
	'" . addslashes($_POST['thinking']) ."')";

$result = MYSQL_QUERY($insert);
?>
		<br />
		<br />
		<b><?php echo $_POST['name']?></b> has been updated as user number: <b><?php echo $_GET['uid']?></b>. 
	</body>
</html>
