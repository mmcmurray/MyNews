<?
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

$query  = mysql_query("select cmtitle,cmauthor from " . $myNewsConf['db']['table']['comments'] . " where cmtnr = " . $_GET['cid']) or die(mysql_error());
$row    = mysql_fetch_assoc($query);
	$title  = $row['cmtitle'];
	$author = $row['cmauthor'];
?>

<meta http-equiv="Refresh" content="1; URL=<?php echo "$baseNewsAdmin_URI?mode=comments_edit_list&sid=" . $_GET['sid']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Deleted : " . stripslashes($title); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>

<?
if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $author != $_SESSION['valid_user']){

	print "<p><b>You do not have permission to Delete this Comment!</b>";

} else {

	$child_query    = mysql_query("SELECT count(*) as count from " . $myNewsConf['db']['table']['comments'] . " where pid = " . $_GET['cid']);
	$row            = mysql_fetch_assoc($child_query);
		$child_count    = $row['count'];

	print "<p><b>\"$title\"</b> with item number <b>" . $_GET['cid'] . "</b> should now be deleted!";
    // Delete Children First!
	cmChildDelete($_GET['cid'],1);

    // Then Delete the parent
	$query = "DELETE FROM " . $myNewsConf['db']['table']['comments'] . " WHERE cmtnr = " . $_GET['cid'];
	$insert = MYSQL_QUERY($query);

} // End else()
?>
	</body>
</html>
