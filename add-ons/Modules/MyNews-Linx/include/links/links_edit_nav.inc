<center>
<?
$baseLinksAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'];
$back_button        = $myNewsConf['button']['back'];
$next_button        = $myNewsConf['button']['next'];

if ($_SESSION['status'] == 'Author'){
    $query = mysql_query( "SELECT Count(*) as total FROM " . $myNewsConf['db']['table']['portal'] . " WHERE author = '" . $_SESSION['valid_user'] . "'") or die (mysql_error());
} else {
    $query = mysql_query( "SELECT Count(*) as total FROM " . $myNewsConf['db']['table']['portal']) or die (mysql_error());
}
$row = mysql_fetch_assoc($query);
	$total = $row['total'];

$vor    = $show + $myNewsConf['default']['limit'];
$back   = $show - $myNewsConf['default']['limit'];

if ($back >= 0){
?>
	<a href="<?=$baseLinksAdmin_URI?>?mode=links_edit_list&show=<?=$back?>"><?=$back_button?></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?
}

if ($vor < $total){
?>
	<a href="<?=$baseLinksAdmin_URI?>?mode=links_edit_list&show=<?=$vor?>"><?=$next_button?></a>
<?
}
?>
</center>
