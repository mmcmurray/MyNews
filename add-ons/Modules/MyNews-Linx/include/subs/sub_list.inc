<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Submissions : List";
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

include($myNewsConf['head']);
?>

<script language="JavaScript">
<!--

function confirmDelete(URL,TITLE){
     where_to= confirm("Do you really want to delete the submission:\n\n" + TITLE); 
    if (where_to== true) { 
        window.location=URL;
    } else {  
        window.location="<?=$baseNewsAdmin_URI?>?mode=sub_list"; 
    }
}

//-->
</script>

<?
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . '/include/login_check.inc');

if ($_SESSION['status'] == 'Author'){
	print "<p><b>You are not authorized to View this Resource</b>";
} else {

?>

	<p>
	<b>Submit an article</b><br>
	<a href="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
	<p>
	<blockquote>
	<table width="85%" border=0 cellpadding=2 cellspacing=1>
	   <tr>
		<td>&nbsp;</td>
		<td><b>Title:</b></td>
		<td><b>Author:</b></td>
	   </tr>

<?
    $query = mysql_query("SELECT artnr,title,author,email,date from " . $myNewsConf['db']['table']['submissions'] . " order by artnr desc") or die (mysql_error());
    WHILE ($row = mysql_fetch_assoc($query)){
		$sid    = $row['artnr'];
        $title  = $row['title'];
        $author = $row['author'];
        $email  = $row['email'];
        $date   = $row['date'];
        $ntitle = addslashes($title);
?>
	
	<tr>
	  <td>
		(<a href="<?=$baseNewsAdmin_URI?>?mode=sub_edit&sid=<?=$sid?>">Submit</a>
		|
		<A HREF="javascript:confirmDelete('<?=$baseNewsAdmin_URI?>?mode=sub_delete&sid=<?=$sid?>','<?=$ntitle?>')">Delete?</A>)
	  </td>
	  <td>
		<p>
		<?=$title?>
	  </td>
	  <td>
		<a href="mailto:<?=$email?>"><?=$author?></A>
	  </td>
	</tr>


<?
    } // End While
?>

</blockquote>
</table>

<?
} // End If
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
