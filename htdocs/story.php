<?
/* $Id: story.php 444 2004-08-12 17:05:33Z alien $ */

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
$sid    = $_GET['sid'];
$page   = $_GET['page'];

if (!isset($page) || empty($page)) $page = '1';
if (!isset($sid) || empty($sid)) $sid = '1';

switch($mode){
    case 'storyPrint':
        storyPrint($sid);
        return;
        break;
    case 'storyForm':
        $contentHash    = storyForm($sid);
        break;
    case 'storyMail':
        $contentHash    = storyMail($sid);
        break;
    case 'storyView':
        $contentHash    = storyView($sid,$page);
        $commentHash    = comments($sid);
        break;
        
}

// Build out content based on $contentHash
$_error     = $contentHash['error'] . $commentHash['error'];
$_meta      = $contentHash['meta'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];
$_comment   = $commentHash['content'];

// We only want to build the content box if content exist
if($_error) $_error = makebox('Error:',$_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');
if($_comment) $_comment = makebox($commentHash['title'],'<blockquote>' . $_comment . '</blockquote>','content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', $_comment);

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
