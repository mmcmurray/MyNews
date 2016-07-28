<?
$baseAlbumAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];

$query  = mysql_query("select * from " . $myNewsConf['db']['table']['album'] . " where picid = " . $_GET['picid']) or die(mysql_error());
$row    = mysql_fetch_assoc($query);
	$filename   = $row['filename'];
	$text       = $row['text'];
	$album      = $row['album'];
	$author     = $row['author'];

?>

<meta http-equiv="Refresh" content="1; URL=<?php echo "$baseAlbumAdmin_URI?mode=album_edit_list&album=" . urlencode($album); ?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Album : Deleted : " . stripslashes($text); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>

<?

// Delete the image from the database.
$query = "DELETE FROM " . $myNewsConf['db']['table']['album'] . " WHERE picid = '" . $_GET['picid'] . "'";
$insert = MYSQL_QUERY($query) or die(mysql_error());

// Delete the comments attached to the image.
$query = "DELETE FROM " . $myNewsConf['db']['table']['comments'] . " WHERE artnr = " . $_GET['picid'];
$insert = MYSQL_QUERY($query) or die(mysql_error());

// Let everyone know we just deleted the image from the database.
?>

		<p>
		<b>"<?=$text?>"</b> has been deleted from the <?=$album?> album!
		  <br />
		  <br />

<?
$thumb = $myNewsConf['path']['sys']['album'] . '/' . $author . '/album/thumbnails/' . $filename;
$image = $myNewsConf['path']['sys']['album'] . '/' . $author . '/album/' . $filename;

$image_deleted = unlink($image);
	if ($image_deleted){
?>
		Image: <b><?=$image?></b> has been deleted.<br>
<?
	} else {
?>
		Image: <b><?=$image?></b> was not deleted.<br>
<?
	} //End If

$thumb_deleted = unlink($thumb);
	if ($thumb_deleted){
?>
		Thumbnail: <b><?=$thumb?></b> has been deleted.<br>
<?
	} else {
?>
		Thumbnail: <b><?=$thumb?></b> was not deleted.<br>
<?
	} //End If
?>
	</body>
</html>
