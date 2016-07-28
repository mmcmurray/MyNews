<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=comments_edit_list&sid=" . $_GET['sid'];?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['cmtitle']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

$insert = "replace into " . $myNewsConf['db']['table']['comments'] . " values(
	'" . addslashes($_GET['cid'])			. "',
	'" . addslashes($_POST['pid'])			. "',
	'" . addslashes($_POST['cmtitle'])		. "',
	'" . addslashes($_POST['commenttext']) . "',
	'" . addslashes($_POST['cmauthor'])	. "',
	'" . addslashes($_POST['cmemail'])		. "',
	'" . addslashes($_POST['cmdate'])		. "',
	'" . addslashes($_POST['cmip'])		. "',
	'" . addslashes($_GET['sid'])			. "',
	'" . addslashes($_POST['type'])		. "')";

$result = MYSQL_QUERY($insert) or die(mysql_error());
?>
        <br />
        <br />
		The item <b><?php echo $_POST['cmtitle']?></b> has been updated as item number: <b><?php echo $_GET['cid']?></b>.
	</body>
</html>
