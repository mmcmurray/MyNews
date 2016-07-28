<?php
$TITLE =			$myNewsConf['default']['sitename'] . " Admin : Calendar : Delete : Event List";
$baseCalAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

include($myNewsConf['head']);
?>

<script language="JavaScript">
<!--

function confirmDelete(ID,YEAR,MONTH,TITLE,WHO,REC){
	if (REC== 0){
		where_to= confirm("Do you really want to delete the event:\n\n" + WHO); 
		if (where_to== true) { 
			window.location="<?=$baseCalAdmin_URI?>?mode=deleted&id=" + ID + "&title=" + TITLE + "&year=" + YEAR + "&month=" + MONTH;
		} else {  
			window.location="<?=$baseCalAdmin_URI?>?mode=delete_list&year=" + YEAR + "&month=" + MONTH; 
		}
	} else {
		where_to= confirm("Do you really want to delete the recurring event:\n\n" + WHO); 
		if (where_to== true) { 
			window.location="<?=$baseCalAdmin_URI?>?mode=deleted&id=" + ID + "&recurring=" + REC + "&title=" + TITLE + "&year=" + YEAR + "&month=" + MONTH;
		} else {  
			window.location="<?=$baseCalAdmin_URI?>?mode=delete_list&year=" + YEAR + "&month=" + MONTH; 
		}
    }
}

//-->
</script>

<?
section_header('Admin','100%','other');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
        print "<p><b>You do not have permission to view this resource!</b>"; 

} else {

    if ($_SESSION['status'] == 'Author'){
        $authclause = ' AND msg_poster_id = "' . $_SESSION['valid_user'] . '"';
    } else {
        $authclause = '';
    }


    if ($HTTP_SERVER_VARS['REQUEST_METHOD'] == 'GET'){ 
        $query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_month = " . $_GET['month'] . " AND msg_year = " . $_GET['year'] . ' ' . $authclause . " ORDER BY msg_day";
    } else {
        $query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_month = " . $_POST['month'] . " AND msg_year = " . $_POST['year'] . ' ' . $authclause . " ORDER BY msg_day";
    }

	$result = mysql_query($query);

	if( !$result ){
	  echo mysql_error() . ": " . mysql_errno();
	}
?>

<p><b>Delete an Event</b><br>
<A HREF="<?php echo "$baseCalAdmin_URI"; ?>">Go back to admin</a>
<blockquote>
    <p>Click on the event below that you want to delete. If
a (&clubs;) appears after the event, this indicates that
it is part of a series of recurring events. Deleting a specific
recurring event will alter all of its associated events.</p>
        <table border="0" cellpadding="2" cellspacing="2">
            <tr>
                <td>&nbsp;</td>
                <td><b>Type:</b></td>
                <td><b>Title:</b></td>
                <td><b>Owner:</b></td>
                <td><b>Date:</b></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
<?php
	while( $row = mysql_fetch_assoc($result) ){
		if(!$row['msg_recurring']){
?>
            <tr>
                <td>
<a href="JavaScript:confirmDelete('<?=$row["msg_id"]?>','<?=urlencode($row["msg_year"])?>','<?=urlencode($row["msg_month"])?>','<?=$row["msg_title"]?>','<?=$row['msg_who']?>','0')">(Delete)</a>
                </td>
                <td>
                    <?=$row['msg_title']?>
                </td>
                <td>
                    <?=$row['msg_who']?>
                </td>
                <td>
                    <?=$row['msg_poster_id']?>
                </td>
                <td>
                    <i><?=$row["msg_month"]?>/<?=$row["msg_day"]?>/<?=$row["msg_year"]?></i>
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>

<?
		} else {
?>
            <tr>
                <td>
<a href="JavaScript:confirmDelete('<?=$row["msg_id"]?>','<?=urlencode($row["msg_year"])?>','<?=urlencode($row["msg_month"])?>','<?=$row["msg_title"]?>','<?=$row['msg_who']?>','<?=$row['msg_recurring']?>')">(Delete)</a>
                </td>
                <td>
                    <?=$row['msg_title']?>
                </td>
                <td>
                    <?=$row['msg_who']?>
                </td>
                <td>
                    <?=$row['msg_poster_id']?>
                </td>
                <td>
                    <i><?=$row["msg_month"]?>/<?=$row["msg_day"]?>/<?=$row["msg_year"]?></i>
                </td>
                <td>
                    &clubs;
                </td>
            </tr>
<?
		}
	}
?>
        </table>
</blockquote>

<?
} // End Login Check If()
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
