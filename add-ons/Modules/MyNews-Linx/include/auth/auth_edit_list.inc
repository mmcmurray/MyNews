<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Authors : Edit List";
$baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

include($myNewsConf['head']);
?>

<script language="JavaScript">
<!--

function confirmDelete(URL,USER){
     where_to= confirm("Do you really want to delete the user:\n\n" + USER); 
    if (where_to== true) { 
        window.location=URL;
    } else {  
        window.location="<?=$baseAuthAdmin_URI?>?mode=edit_list"; 
    }
}

//-->
</script>

<?
section_header('Members','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>
<p>
<b>Edit an Author</b><br>
<a href="<?=$baseAuthAdmin_URI?>">Go back to admin</a>
<br>
<blockquote>
<table border="0" width="85%">
	<tr>
	   <td>&nbsp;</td>
	   <td><b>Name</b></td>
	   <td><b>User ID</b></td>
	</tr>
	<tr>
	   <td colspan="3">&nbsp;</td>
	</tr>
<?
$query  = mysql_query("SELECT artnr,name,user from " . $myNewsConf['db']['table']['authors'] . " order by user") or die (mysql_error());
WHILE ($row = mysql_fetch_assoc($query)){
	$uid    = $row['artnr'];
	$name   = $row['name'];
	$user   = $row['user'];
?>

	<tr>
	   <td>
			(<a href=<?=$baseAuthAdmin_URI?>?mode=edit&uid=<?=$uid?>>Edit</b></a>
			|
			<a href="javascript:confirmDelete('<?=$baseAuthAdmin_URI?>?mode=delete&uid=<?=$uid?>','<?=$user?>')">Delete</a>)
	   </td>
	   <td>
			<p>
			<?=$name?>
	   </td>
	   <td>
			<p>
			<?=$user?>
	   </td>
	</tr>

<?
} //End While
?>

</table>
</blockquote>

<?
section_footer('Members','content');
include($myNewsConf['foot']);
?>
