<?php
/* $Id: timer-foot.php 134 2003-02-09 08:19:58Z kekepower $ */
if ($myNewsConf['debug']['timer'] == 'on') {

	//footer (of) document
	$mtime      = microtime();
	$mtime      = explode(" ",$mtime);
	$mtime      = $mtime[1] + $mtime[0];
	$endtime    = $mtime;
	$totaltime  = ($endtime - $starttime);
	echo "<p align=\"right\"><small>Page created in ". round($totaltime,2) ." seconds.</small>"; 
}
?>
