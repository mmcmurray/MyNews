<?
$news_query = mysql_query("select title,author from " . $myNewsConf['db']['table']['news'] . " where artnr=" . $_GET['sid']) or die(mysql_error());
$row        = mysql_fetch_assoc($news_query);
        $title  = $row['title'];
        $author = $row['author'];
?>

<meta http-equiv="Refresh" content="1; URL=<?=$myNewsConf['path']['web']['admin']?><?=$myNewsConf['adminScripts']['news']?>?mode=news_edit_list">
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

	$query =	"DELETE FROM " . $myNewsConf['db']['table']['news'] . " WHERE artnr=" . $_GET['sid'];
	$delete =	MYSQL_QUERY("$query") or die(mysql_error());

	cmChildDelete($_GET['sid']);

	require($myNewsConf['path']['sys']['admin'] . "/include/news/news_rdf.inc");

	print "<p><b>\"$title\"</b> with item number <b>" . $_GET['sid'] . "</b> should now be deleted!";
}
?>
	</body>
</html>
