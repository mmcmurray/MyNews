<?php
/* $Id: test.php 506 2005-09-27 20:47:06Z alien $ */
/*
 * Require MyNews Config and Libraries.
 */
require($_SERVER['DOCUMENT_ROOT'] . '/include/config.inc.php');
require($myNewsConf['path']['sys']['index'] . '/include/libs/lib.inc.php');
require($myNewsConf['path']['sys']['index'] . '/include/libs/blocks.lib.php');
require($myNewsConf['path']['sys']['index'] . '/include/libs/timer/timer-head.php');

/*
 * Require Template Classes and functions
 */
require('include/classes/template.inc.class');
require('templates/template.functions.php');

/*
 * Define Vars, include title, content title, and Main Content Data
 */
$_title  = 'This is the Main Title';
$_title = 'This is the title';
$_content       =  <<<CON

Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,

[caption ALIGN="left"]
<center>
<img src="http://alienated.org/cpg/albums/uploads/HSN-West_Coast_Rally/Gonzo/thumb_22-TexasDelegation.JPG">
</center>
[/caption]

consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo

consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod
<a href="http://alienated.org">tempor incididunt ut labore et dolore magna aliqua.</a> Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse

[caption]
<center>
<img src="http://alienated.org/cpg/albums/uploads/Honeymoon/thumb_DSC00922.JPG">
</center>
[/caption]

cillum dolore eu fugiat nulla pariatur.Lorem ipsum dolor sit amet, consectetaur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur.

CON;

/*
 * Require the header
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

/********************************************************************/
/*
 * Here we look for [caption][/caption] tags to see whether we want to
 * Insert a table for the caption.
 */
$_content   = mynews_format($_content);
$_content   = preg_replace("/\[caption\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\1'),'right','')", $_content);
$_content   = preg_replace("/\[caption align=\"(.+?)\"\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\2'),'\\1','')", $_content);

$_error     = myNewsError('110','Test Error');

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:',$_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Insert our content into the template
 */
$tpl->set_var('error',$_error);
$tpl->set_var('main_content', $_content);
$tpl->set_var('comments','');
/********************************************************************/

/*
 * Require the footer
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
