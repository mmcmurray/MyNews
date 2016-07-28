<?
$TITLE =				$myNewsConf['default']['sitename'] . " Admin : Album : Add";
$baseAlbumAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];
$submit =				$myNewsConf['button']['submit'];
$page = 'upload';

require $myNewsConf['head'];
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>
	<p>
	<b>Add a new Photo.</b>
	  <br>
	<A HREF="<?php echo "$baseAlbumAdmin_URI"; ?>">Go back to admin</a>

<form action="<?php echo "$baseAlbumAdmin_URI?mode=album_added"; ?>" enctype="multipart/form-data" method=POST>
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
<table class="border" border=0 CELLPADDING=2 CELLSPACING=2>
	<tr>
  	  <th align="right"><b>Album:<b></th>
	    <td>
	      <select class="textbox" name="album" OnChange="if(this.value) { this.form.new_album.value = ''} else { this.select(); }">
              <option value="none">Create new album
<?
    $albums = mysql_query("select distinct album from " . $myNewsConf['db']['table']['album'] . " where author='" . $_SESSION['valid_user'] . "' order by album");
    while($row = mysql_fetch_row($albums)) 
    {
	echo "\t\t\t<option>".stripslashes($row[0])."\n";
    }
    mysql_free_result($albums);
?>
              </select> 
	    </td>
        </tr>
	<tr>
	  <th align="right">
	 	<p>&nbsp;
	  </th>
	    <td>
			<input class="textbox" type="text" name="new_album" value="<new album name>" OnFocus="if(this.value && this.value == '<new album name>') { this.value = ''; } else { this.select(); }">
		</td>
	</tr>
	<tr>
		<th align="right">
			<b>Filename:</b>
		</th>
		<td>
			<input class="textbox" type="file" name="file" size=36>
		</td>
	</tr>
	<tr>
		<th align="right">
			<b>Title:</b>
		</th>
		<td>
			<input class="textbox" type="text" name="desc" size=36>
		</td>
	</tr>
	<tr>
		<th valign="top" align="right">
			<b>Description:</b>
		</th>
		<td>
			<textarea class="textbox" name="extended" rows=10 cols=36></textarea>
		</td>
	</tr>
	<tr>
		<th valign="top" align="right">
			<p>&nbsp;
		</th>
		<td>
			<input type="hidden" name="author" value="<?php echo $_SESSION['valid_user']; ?>">
			<?=$submit?>
		</td>
	</tr>
</table>
</form>

<?
	section_footer('Admin','content');
	include $myNewsConf['foot'];
?>
