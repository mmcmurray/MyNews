<?
$TITLE =				$myNewsConf['default']['sitename'] . " Admin : Portal : Edit List";
$baseNewsAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['portal'];
$submit =				$myNewsConf['button']['submit'];

include($myNewsConf['head']);
?>

<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){
    if( linkid.value == "" ){
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
		window.location="<?=$baseNewsAdmin_URI?>?mode=portal_edit_list&show=" + SHOW;
	}
}

//-->
</script>

<?
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p>
<b>Edit an Article</b><br>
<a href="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
<p>
<form action="<?=$baseNewsAdmin_URI?>" name="the_form" method=GET onSubmit="return isOK()">
<table border=0>
	<tr>
	   <td>
<p>
Input Item Number to jump to an article:
	  </td>
	  <td valign="bottom">
<input type="hidden" name="mode" value="portal_edit">
<input class="textbox" type=text size=3 name="linkid">
	  </td>
	</tr>
</table>
</form>

<table border=0 width=85%>
   <tr>
	<td>
		&nbsp;
	</td>
	<td>
	  <b>Title:</b>
	</td>
	<td>
	  <b>Author:</b>
	</td>
   </tr>

<?
if (!$_GET['show']){$show = 0;} else { $show = $_GET['show'];}

$query = mysql_query("SELECT artnr,title,author from " . $myNewsConf['db']['table']['linx'] . " order by artnr desc LIMIT $show, " . $myNewsConf['default']['limit']) or die (mysql_error());
while ($row = mysql_fetch_assoc($query)){
	$linkid    = $row['artnr'];
	$title  = $row['title'];
	$author = $row['author'];
	$nTitle = addslashes($title);
?>

	<tr>
	  <td>
		(<a href="<?=$baseNewsAdmin_URI?>?mode=portal_edit&linkid=<?=$linkid?>">Edit</a>
		|
		<a href="javascript:confirmDelete('<?=$baseNewsAdmin_URI?>?mode=portal_deleted&linkid=<?=$linkid?>','<?=$show?>','<?=$nTitle?>')">Delete</a>)
	  </td>
	  <td>
		<p>
		<?=$title?>
	  </td>
	  <td>
		<i><?=$author?></i>
	  </td>
	</tr>

<?
   } // End While
?>

</table>

<?
include($myNewsConf['path']['sys']['admin'] . "/include/portal/portal_edit_nav.inc");
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
