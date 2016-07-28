<center>
<?
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
$back_button        = $myNewsConf['button']['back'];
$next_button        = $myNewsConf['button']['next'];

$query  = mysql_query( "SELECT Count(distinct artnr) as total FROM " . $myNewsConf['db']['table']['comments']) or die (mysql_error());
$row    = mysql_fetch_assoc($query);
    $total  = $row["total"];

$vor =	$show + $myNewsConf['default']['limit'];
$back =	$show - $myNewsConf['default']['limit'];

if ($back >= 0){
?>
	<a href="<?=$baseNewsAdmin_URI?>?mode=comments_select&show=<?=$back?>"><?=$back_button?></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?
}

if ($vor < $total){
?>
	<a href="<?=$baseNewsAdmin_URI?>?mode=comments_select&show=<?=$vor?>"><?=$next_button?></a>
<?
}
?>
</center>
