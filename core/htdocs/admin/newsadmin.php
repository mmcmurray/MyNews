<?
/* $Id: newsadmin.php 444 2004-08-12 17:05:33Z alien $ */

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

define('ADMIN_ROOT', $myNewsConf['path']['sys']['admin']);
require_once(ADMIN_ROOT . '/include/admin.lib.php');

// Check to see if we are authenticated.
$auth   = isAuth();

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

/*************** Begin Code *****************************************/
require_once(ADMIN_ROOT . '/include/news.lib.php');
require_once(ADMIN_ROOT . '/include/comments.lib.php');
require_once(ADMIN_ROOT . '/include/subs.lib.php');
require_once(ADMIN_ROOT . '/include/sections.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if (!isset($_SESSION['valid_user'])) $_GET['mode'] = 'error';
if (!isset($_GET['mode'])) $_GET['mode'] = 'admin';

// initialize $contentHash
$contentHash    = array();

switch($_GET['mode']) {
	case 'admin':
        $contentHash    = newsAdmin();
		break;

    /*
     * News
     */
	case 'nAdd':
        $contentHash    = newsAdd();
		break;
	case 'nAdded':
        $contentHash    = newsAdded();
        genRdf();
		break;
	case 'nList':
        $contentHash    = newsList();
		break;
	case 'nEdit':
        $contentHash    = newsEdit($_GET['sid']);
		break;
	case 'nEdited':
        $contentHash    = newsEdited();
        genRdf();
		break;
	case 'nDeleted':
        $contentHash    = newsDeleted();
        genRdf();
		break;
    /*
     * Comments
     */
	case 'cmSelect':
        $contentHash    = cmSelect();
		break;
	case 'cmSelected':
        $contentHash    = cmSelected();
		break;
	case 'cmEdit':
        $contentHash    = cmEdit();
		break;
	case 'cmEdited':
        $contentHash    = cmEdited();
		break;
	case 'cmDeleted':
        $contentHash    = cmDeleted();
		break;

    /*
     * Submissions
     */
	case 'subList':
        $contentHash    = subList();
		break;
	case 'subEdit':
        $contentHash    = subEdit();
		break;
	case 'subPosted':
        $contentHash    = subPosted();
		break;
	case 'subDeleted':
        $contentHash    = subDeleted();
		break;

    /*
     * Sections
     */
	case 'secList':
        $contentHash    = secList();
		break;
	case 'secEdited':
        $contentHash    = secEdited();
		break;
    case 'error':
        break;
}


// build our content based on $contentHash
$_error     = $contentHash['error'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];
$_jscript   = $contentHash['jscript'];
$_meta      = $contentHash['meta'];

// We only want to build the box if content exist
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
