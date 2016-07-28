<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'] . "?mode=links_edit_list"; ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : " . $_POST['title']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?

if($_POST['category'] == 'none'){
        $category = $_POST['new_category'];
} else {
	$category = $_POST['category'];
}

if($_POST['subcategory'] == 'none'){
	$subcategory = $_POST['new_subcategory'];
} else {
	$subcategory = $_POST['subcategory'];
} 

$insert = "replace into " . $myNewsConf['db']['table']['portal'] . " values(
	'" . addslashes($_GET['sid'])			. "',
	'" . addslashes($_POST['title'])		. "',
	'" . addslashes($_POST['description']) . "',
	'" . addslashes($_POST['url'])		. "',
	'" . addslashes($_POST['email'])		. "',
	'',
	'" . addslashes($category)		. "',
	'" . addslashes($subcategory)		. "',
	'',
	'" . addslashes($_POST['author'])	. "')";

$result = MYSQL_QUERY($insert);

require($myNewsConf['path']['sys']['admin'] . "/include/links/links_rdf.inc");
?>
        <br />
        <br />
		The item <b><?php echo $_POST['title']?></b> has been updated as item number: <b><?php echo $_GET['sid']?></b>.
	</body>
</html>
