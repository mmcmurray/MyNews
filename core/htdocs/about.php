<?php
/* $Id: about.php 444 2004-08-12 17:05:33Z alien $ */

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

/*************** Begin Code *****************************************/
// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$mode   = $_GET['mode'];
$author = $_GET['author'];

if (!isset($mode) || empty($mode)) $mode = 'about';

switch($mode){
    case 'auth_list':
        $contentHash = authorList();
        break;
    case 'zoom':
        $contentHash = authorShow($author);
        break;
    case 'about':
        header('Location:' . $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'] . '?mode=storyView&sid=1');
        break;

}

// Build out content based on $contentHash
$_error     = $contentHash['error'];
$_meta      = $contentHash['meta'];
$_jscript   = $contentHash['jscript'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:',$_error,'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
