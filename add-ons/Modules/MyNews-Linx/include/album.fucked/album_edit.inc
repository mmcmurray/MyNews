<?
$query  = mysql_query("select * from " . $myNewsConf['db']['table']['album'] . " where picid = " . $_GET['picid']) or die(mysql_error());
$row    = mysql_fetch_assoc($query);
	$picid      = $row['picid'];
	$filename   = $row['filename'];
	$album      = $row['album'];
	$text       = $row['text'];
	$extended   = $row['extended'];
	$date       = $row['date'];
	$author     = $row['author'];

	$image = $myNewsConf['path']['web']['album'] . $author . '/album/thumbnails/' . $filename;

$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Album : Edit : " . $text;
$baseAlbumAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];
$submit             = $myNewsConf['button']['submit'];
$now                = date($myNewsConf['format']['date']['nice']);

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

print <<<EOT

<p>
<b>Edit Photo:</b> <i>$text</i><br>
<A HREF="$baseAlbumAdmin_URI">Go back to admin</a>
<blockquote>
<table border=0 width=85%>
   <form action="$baseAlbumAdmin_URI?mode=album_edited&picid=$picid" method="post">
	<tr>
		<td valign="top">
			<b>Image:</b>
		</td>
		<td>
			<img src="$image" border="0">
		</td>
	</tr>
	<tr>
	  <td valign="top">
		<b>Title:</b>
	  </td>
	  <td>
			<input class=textbox type="text" name="text" size="40" value="$text">
		</td>
	</tr>
	<tr>
		<td valign="top">
			<b>Description:</b>
		</td>
		<td>
			<TEXTAREA class=textbox NAME="extended" COLS=51 ROWS=10 WRAP=VIRTUAL>$extended</TEXTAREA>
		</td>
	</tr>
			<input type="hidden" name="album" value="$album">
			<input type="hidden" name="date" value="$date">
			<input type="hidden" name="author" value="$author">
			<input type="hidden" name="filename" value="$filename">
	</tr>
		<td>
			&nbsp;
		</td>
		<td>
			$submit
		</td>
	</tr>

</form>
</table>
		<small><i>Today is: $now </i></small>
</blockquote>

EOT;

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
