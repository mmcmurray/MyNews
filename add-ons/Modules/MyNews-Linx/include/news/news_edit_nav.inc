<center>
<?
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
$back_button        = $myNewsConf['button']['back'];
$next_button        = $myNewsConf['button']['next'];

$query = mysql_query( "SELECT Count(*) as total FROM " . $myNewsConf['db']['table']['news']) or die (mysql_error());
$row = mysql_fetch_assoc($query);
	$total = $row['total'];

$vor    = $show + $myNewsConf['default']['limit'];
$back   = $show - $myNewsConf['default']['limit'];

if ($back >= 0){
?>
	<a href="<?=$baseNewsAdmin_URI?>?mode=news_edit_list&show=<?=$back?>"><?=$back_button?></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?
}

if ($vor < $total){
?>
	<a href="<?=$baseNewsAdmin_URI?>?mode=news_edit_list&show=<?=$vor?>"><?=$next_button?></a>
<?
}
?>
</center>
