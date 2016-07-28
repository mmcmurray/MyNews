<?php
/* $Id: prefsadmin.php,v 4.17 2004/08/12 17:05:33 alien Exp $ */

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/search.lib.php');
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
require_once(MYNEWS_ROOT . '/modules/lib/tix.lib.php');
require_once(ADMIN_ROOT  . '/lib/tix.admin.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if(!isset($_SESSION['valid_user'])){
    $_GET['mode'] = 'error';
}

// We Need to make sure only allowed groups can view/edit the
// settings.
$errorArray = chkPerm('Admin:Editor');
if($errorArray) $_GET['mode'] = 'error';

if (!isset($_GET['mode'])) $_GET['mode'] = 'admin';

// Begin Tickets Admin includes
switch($_GET['mode']) {
	case 'admin':
        $contentHash    = tixAdmin();
		break;
	case 'pscpList':
        $contentHash    = tixListPSCP($_GET['view']);
		break;
	case 'pscpMod':
        $contentHash    = tixModPSCP($_POST['view']);
		break;
    case 'pscpEdit':
        $contentHash    = tixEditPSCP($_GET['id'], $_GET['view']);
        break;
    case 'pscpEdited':
        $contentHash    = tixEditedPSCP($_POST['id'], $_POST['view']);
        break;
    case 'groupList':
        $contentHash    = tixListGroup();
        break;
    case 'groupEdit':
        $contentHash    = tixEditGroup($_GET['id'], $_GET['name']);
        break;
    case 'groupEdited':
        $contentHash    = tixEditedGroup($_POST['id']);
        break;
    case 'aclList':
        $contentHash    = tixListACL();
        break;
    case 'aclAdd':
        $contentHash    = tixAddACL();
        break;
    case 'aclDelete':
        $contentHash    = tixDeleteACL($_GET['id']);
    case 'error':
        $contentHash    = $errorArray;
        break;
        
}

// Build our content base on $contentHash
$_meta      = $contentHash['meta'];
$_jscript   = $contentHash['jscript'];
$_error     = $contentHash['error'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];

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
