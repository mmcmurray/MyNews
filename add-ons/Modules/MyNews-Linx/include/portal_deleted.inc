<?
$portal_query = mysql_query("select title,author from " . $myNewsConf['db']['table']['linx'] . " where artnr=" . $_GET['linkid']) or die(mysql_error());
$row        = mysql_fetch_assoc($portal_query);
        $title  = $row['title'];
        $author = $row['author'];
?>

<meta http-equiv="Refresh" content="1; URL=<?=$myNewsConf['path']['web']['admin']?><?=$myNewsConf['adminScripts']['portal']?>?mode=portal_edit_list">
<html>
	<head>
		<title><?=$myNewsConf['default']['sitename']?> Admin : Deleted : <?=$title?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
<p>
<?
if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $author != $_SESSION['valid_user']){
	print "<p><b>You do not have permission to Delete this article!</b>"; 

} else {

	$query =	"DELETE FROM " . $myNewsConf['db']['table']['linx'] . " WHERE artnr=" . $_GET['linkid'];
	$delete =	MYSQL_QUERY("$query") or die(mysql_error());

	cmChildDelete($_GET['linkid']);

	require($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_rdf.inc");

	print "<p><b>\"$title\"</b> with item number <b>" . $_GET['linkid'] . "</b> should now be deleted!";
}
?>
	</body>
</html>
