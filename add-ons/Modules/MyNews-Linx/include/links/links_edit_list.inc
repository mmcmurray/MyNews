<?
$TITLE =		$myNewsConf['default']['sitename'] . " Admin : Links : Edit List";
$baseLinksAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'];
$submit =		$myNewsConf['button']['submit'];

include($myNewsConf['head']);
?>

<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){
    if( sid.value == "" ){
      alert("To use this fuction, you have to input an item number.");
      return false;
    }
  }

  return true;
}

function confirmDelete(URL,SHOW,TITLE){
	 where_to= confirm("Do you really want to delete the item:\n\n" + TITLE);
	if (where_to== true) { 
		window.location=URL;
	} else { 
		window.location="<?=$baseLinksAdmin_URI?>?mode=links_edit_list&show=" + SHOW;
	}
}

//-->
</script>

<?
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p>
<b>Edit a Link</b><br>
<a href="<?=$baseLinksAdmin_URI?>">Go back to admin</a>
<p>
<form action="<?=$baseLinksAdmin_URI?>" name="the_form" method=GET onSubmit="return isOK()">
<table border=0>
	<tr>
	   <td>
<p>
Input Item Number to jump to an article:
	  </td>
	  <td valign="bottom">
<input type="hidden" name="mode" value="links_edit">
<input class="textbox" type=text size=3 name="sid">
	  </td>
	</tr>
</table>
</form>

<table border=0 width=95%>
   <tr>
	<td>
		&nbsp;
	</td>
	<td>
	  <b>Title:</b>
	</td>
	<td>
	  <b>Category:</b>
	</td>
	<td>
	  <b>URL:</b>
	</td>
   </tr>

<?
if (!$_GET['show']){$show = 0;} else { $show = $_GET['show'];}

    if ($_SESSION['status'] == 'Author'){
        $query = mysql_query("SELECT linkid,linktitle,linkhref,category,genre,postby from " . $myNewsConf['db']['table']['portal'] . " WHERE postby = '" . $_SESSION['valid_user'] . "' order by linkid desc LIMIT $show, " . $myNewsConf['default']['limit']) or die (mysql_error());
    } else {
        $query = mysql_query("SELECT linkid,linktitle,linkhref,category,genre from " . $myNewsConf['db']['table']['portal'] . " order by category, genre desc LIMIT $show, " . $myNewsConf['default']['limit']) or die (mysql_error());
    }
while ($row = mysql_fetch_assoc($query)){
	$sid    = $row['linkid'];
	$title  = $row['linktitle'];
	$url    = $row['linkhref'];
	$category = $row['category'];
	$genre	= $row['genre'];
	$nTitle = addslashes($title);
?>

	<tr>
	  <td width=80>
		(<a href="<?=$baseLinksAdmin_URI?>?mode=links_edit&sid=<?=$sid?>">Edit</a>
		|
		<a href="javascript:confirmDelete('<?=$baseLinksAdmin_URI?>?mode=links_deleted&sid=<?=$sid?>','<?=$show?>','<?=$nTitle?>')">Delete</a>)
	  </td>
	  <td>
		<p>
		<?=$title?>
	  </td>
	  <td>
	 	<i><?=$category?>&nbsp;/&nbsp;<?=$genre?></i>	
	  </td>
	  <td>
		<i><?=$url?></i>
	  </td>
	</tr>

<?
   } // End While
?>

</table>

<?
include($myNewsConf['path']['sys']['admin'] . "/include/links/links_edit_nav.inc");
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
