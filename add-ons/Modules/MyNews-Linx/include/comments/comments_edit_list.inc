<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Comments : Edit List";
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
?>

<script language="JavaScript">
<!--

function confirmParentDelete(URL,SID,TITLE){
     where_to= confirm("Do you really want to delete the item:\n\n" + TITLE + "\n\n Note:  This will delete the whole thread"); 
    if (where_to== true) { 
        window.location=URL;
    } else {  
        window.location="<?=$baseNewsAdmin_URI?>?mode=comments_edit_list&sid=" + SID; 
    }
}

function confirmChildDelete(URL,SID,TITLE){
     where_to= confirm("Do you really want to delete the item:\n\n" + TITLE); 
    if (where_to== true) { 
        window.location=URL;
    } else {  
        window.location="<?=$baseNewsAdmin_URI?>?mode=comments_edit_list&sid=" + SID; 
    }
}

//-->
</script>

<?
include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$query  = mysql_query("SELECT title from " . $myNewsConf['db']['table']['news'] . " where artnr = " . $_GET['sid']) or die (mysql_error());
$row    = mysql_fetch_assoc($query);
	$title  = $row['title'];
?>

<p>
<b>Edit/Delete a Comment</b> for - <i><?=$title?></i><br>
<a href="<?=$baseNewsAdmin_URI?>?mode=comments_select">Go back to admin</a>
<p align=center>
<br /> <b>Note:</b> Deleting a thread parent, deletes the whole thread
</p>

<?
cmEditList($_GET['sid']);

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
