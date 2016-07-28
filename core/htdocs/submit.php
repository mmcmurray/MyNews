<?php
/* $Id: submit.php 515 2005-09-30 18:19:46Z alien $ */

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
// Require the search library
require_once($myNewsConf['path']['sys']['index'] . '/include/libs/sub.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if(empty($_GET['mode'])) $_GET['mode'] = 'add';

switch($_GET['mode']){
    case 'add':
        $contentHash    = subAdd();
        break;
    case 'added':
        $contentHash    = subAdded();
        break;
    default:
        $contentHash['title']   = ' Error!';
        $contentHash['content'] = myNewsError(110);
        break;
}

$_title     = $contentHash['title'];
$_content   = $contentHash['content'];
$_error     = $contentHash['error'];

// We only want to build the content box if content exist
if($_error) $_error = makebox('Error:',$_error, 'content');
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
