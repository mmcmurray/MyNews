<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Album : Select Album";
$baseAlbumAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p>
<b>Select Album to Modify</b><br>
<a href="<?=$baseAlbumAdmin_URI?>">Go back to admin</a>
<p>
<blockquote>
<table border=0 width="85%">
	<tr>
		<td>
			&nbsp;
		</td>
		<td width="80%">
			<b>Album:</b>
		</td>
		<td>
			<b>Items:</b>
		</td>
	</tr>

<?
if (!isset($show)) $show=0;

if ($_SESSION['status'] == 'Admin'){
	$query = mysql_query("SELECT album,count(*) as num_albums from " . $myNewsConf['db']['table']['album'] . " group by album") or die (mysql_error());
} else {
	$query = mysql_query("SELECT album,count(*) as num_albums from " . $myNewsConf['db']['table']['album'] . " where author='" . $_SESSION['valid_user'] . "' group by album") or die (mysql_error());
} // End If

while($row = mysql_fetch_assoc($query)){
	$album      = $row['album'];
	$count      = $row['num_albums'];
	$album_link = urlencode($album);
?>

	<tr>
		<td>
			(<a href="<?=$baseAlbumAdmin_URI?>?mode=album_edit_list&album=<?=$album_link?>">View</a>)
		</td>
		<td>
			<p>
			<?=$album?>
		</td>
		<td>
			<i><?=$count?></i>
		</td>
	</tr>

<?
} // End While
?>

</table>
<blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
