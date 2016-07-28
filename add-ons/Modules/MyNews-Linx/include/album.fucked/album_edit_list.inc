<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Album : Edit Album : " . $_GET['album'];
$baseAlbumAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];

include($myNewsConf['head']);

$album = urlencode($_GET['album']);
?>

<script language="JavaScript">
<!--

function confirmDelete(URL,TITLE){
     where_to= confirm("Do you really want to delete the photo:\n\n" + TITLE); 
    if (where_to== true) { 
        window.location=URL;
    } else {  
        window.location="<?=$baseAlbumAdmin_URI?>?mode=album_edit_list&album=<?=$album?>"; 
    }
}

//-->
</script>

<?
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$album = urldecode($_GET['album']);
?>

<p>
<b>Edit/Delete a photo</b> for - <i><?=$album?></i><br>
<a href="<?=$baseAlbumAdmin_URI?>">Go back to admin</a>
<p>
<blockquote>
<table border=0 width="85%" cellpadding="2" cellspacing="2">
   <tr>
	<td>
	  <b>&nbsp;</b>
	</td>
	<td>
	  <b>&nbsp;</b>
	</td>

<?
if ($_SESSION['status'] == 'Admin'){
?>
	<td>
	  <b>Author</b>
	</td>
<?
}
?>

	<td>
	  <b>Date</b>
	</td>
   </tr>

<?
if (!isset($show)) $show=0;

if ($_SESSION['status'] == 'Admin'){
	$query = mysql_query("SELECT picid,filename,text,author,unix_timestamp(date) as timestamp from " . $myNewsConf['db']['table']['album'] . " where album='" . addslashes($_GET['album']) . "' order by date desc") or die (mysql_error());
} else {
	$query = mysql_query("SELECT picid,filename,text,author,unix_timestamp(date) as timestamp from " . $myNewsConf['db']['table']['album'] . " where album='" . addslashes($_GET['album']) . "' and author='" . $_SESSION['valid_user'] . "' order by date desc") or die (mysql_error());
} // End If

WHILE ($row = mysql_fetch_assoc($query)){
	$picid      = $row['picid'];
	$filename   = $row['filename'];
	$title      = $row['text'];
	$timestamp  = $row['timestamp'];
	$author     = $row['author'];

	$image  = $myNewsConf['path']['web']['album'] . $author . '/album/thumbnails/' . $filename;
	$date   = date($myNewsConf['format']['date']['default'],$timestamp);
?>

	<tr>
		<td valign="bottom" align="right" width="20%">
			&nbsp;&nbsp;
			(<a href="<?=$baseAlbumAdmin_URI?>?mode=album_edit&picid=<?=$picid?>">Edit</a>
			|
			<a href="javascript:confirmDelete('<?=$baseAlbumAdmin_URI?>?mode=album_deleted&picid=<?=$picid?>','<?=$title?>')">Delete</a>)
			<br />
		</td>
		<td valign="bottom" align="left" width="20%">
			<img src="<?=$image?>" border="0">
		</td>

<?
	if ($_SESSION['status'] == 'Admin'){
?>

		<td valign="bottom" width="20%">
			<i><?=$author?></i>
		</td>
<?
	}
?>

		<td valign="bottom">
			<i><?=$date?></i>
		</td>
	</tr>

<?
} // End While
?>

</table>
</blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
