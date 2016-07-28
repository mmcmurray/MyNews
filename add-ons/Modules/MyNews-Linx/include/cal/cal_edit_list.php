<?php
$TITLE =			$myNewsConf['default']['sitename'] . " Admin : Calendar : Modify : Event List";
$baseCalAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

include($myNewsConf['head']);
section_header('Admin','100%','other');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
		print "<p><b>You do not have permission to view this resource!</b>"; 

} else {

	//$query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_month = " . $_POST['month'] . " AND msg_year = " . $_POST['year'] . " ORDER BY msg_day";
    if ($_SESSION['status'] == 'Author'){
        $authclause = ' AND msg_poster_id = "' . $_SESSION['valid_user'] . '"';
    } else {
        $authclause = '';
    }

	if ($HTTP_SERVER_VARS['REQUEST_METHOD'] == 'GET'){
		$query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_month = " . $_GET['month'] . " AND msg_year = " . $_GET['year'] . ' ' . $authclause . ' ORDER BY msg_day';
	} else {
		$query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_month = " . $_POST['month'] . " AND msg_year = " . $_POST['year'] . ' ' . $authclause . ' ORDER BY msg_day';
	}
	$result = mysql_query($query);

	if( !$result ){
	  echo mysql_error() . ": " . mysql_errno();
	}
?>

		<p><b>Edit an Event</b><br>
<A HREF="<?php echo "$baseCalAdmin_URI"; ?>">Go back to admin</a>
<blockquote>
		<p>Click on the event below that you want to modify. If
a (&clubs;) appears after the event, this indicates that
it is part of a series of recurring events. Modifying a specific
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
		if( !$row['msg_recurring'] ){
?>
            <tr>
                <td>
<a href="<?=$baseCalAdmin_URI?>?mode=edit_item&id=<?=$row["msg_id"]?>&title=<?=urlencode($row["msg_title"])?>">(Edit)</a>
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
<a href="<?=$baseCalAdmin_URI?>?mode=edit_item&id=<?=$row["msg_id"]?>&recurring=<?=$row["msg_recurring"]?>&title=<?=urlencode($row["msg_title"])?>">(Edit)</a>
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

	mysql_free_result($result);
?>
		 </table>
</blockquote>

<?
} // End Login Check If()
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
