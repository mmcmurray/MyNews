<?
if ($_POST['ednote'] == 'OLDEDNOTE'){
	$query  = mysql_query('select ednote from ' . $myNewsConf['db']['table']['linx'] . ' where artnr=' . $_GET['linkid']) or die(mysql_error());
	$row    = mysql_fetch_assoc($query);

	$ednote = stripslashes($row['ednote']);
} else {
	$ednote = $_POST['ednote'];
}
?>

<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['portal'] . "?mode=portal_edit_list"; ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['title']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

$insert = "replace into " . $myNewsConf['db']['table']['linx'] . " values(
	'" . addslashes($_GET['linkid'])			. "',
	'" . addslashes($_POST['linktitle'])		. "',
	'" . addslashes($_POST['linkdescrip']) . "',
	'" . addslashes($_POST['linkhref']) . "',
	'" . addslashes($_POST['linkemail'])		. "',
	'" . addslashes($_POST['region'])		. "',
	'" . addslashes($_POST['category'])		. "',
	'" . addslashes($_POST['genre'])		. "',
	'" . addslashes($_POST['genre2'])		. "',
	'" . addslashes($_POST['postby'])	. "')";

$result = MYSQL_QUERY($insert);

require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_rdf.inc");
?>
        <br />
        <br />
		The item <b><?php echo $_POST['title']?></b> has been updated as item number: <b><?php echo $_GET['linkid']?></b>.
	</body>
</html>
