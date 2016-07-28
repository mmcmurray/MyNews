<?php
/* $Id: lastitems.php 403 2004-08-08 19:44:36Z alien $ */

$contentHash    = mynews_last(10);

$error  = $contentHash['error'];
$title  = $contentHash['title'];
$output = $contentHash['content'];
?>
