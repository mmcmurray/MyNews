<?
if ($_POST['ednote'] == 'OLDEDNOTE'){
	$query  = mysql_query('select ednote from ' . $myNewsConf['db']['table']['news'] . ' where artnr=' . $_GET['sid']) or die(mysql_error());
	$row    = mysql_fetch_assoc($query);

	$ednote = stripslashes($row['ednote']);
} else {
	$ednote = $_POST['ednote'];
}
?>

<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'] . "?mode=news_edit_list"; ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['title']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

$insert = "replace into " . $myNewsConf['db']['table']['news'] . " values(
	'" . addslashes($_GET['sid'])			. "',
	'" . addslashes($_POST['title'])		. "',
	'" . addslashes($_POST['previewtext']) . "',
	'" . addslashes($ednote)						. "',
	'" . addslashes($_POST['author'])		. "',
	'" . addslashes($_POST['email'])		. "',
	'" . addslashes($_POST['date'])		. "',
	'" . addslashes($_POST['section'])		. "',
	'" . addslashes($_POST['active'])		. "',
	'" . addslashes($_POST['viewcount'])	. "')";

$result = MYSQL_QUERY($insert);

require($myNewsConf['path']['sys']['admin'] . "/include/news/news_rdf.inc");
?>
        <br />
        <br />
		The item <b><?php echo $_POST['title']?></b> has been updated as item number: <b><?php echo $_GET['sid']?></b>.
	</body>
</html>
