<?php
/* $Id: timer-head.php 134 2003-02-09 08:19:58Z kekepower $ */
/*
 * Here we test to see if the timer debug is on.  If so
 * we will time how long it takes to generate this page.
 */ 

if ($myNewsConf['debug']['timer'] == 'on') {
	//header (of) document
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
}
?>
