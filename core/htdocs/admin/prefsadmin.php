<?php
/* $Id: prefsadmin.php 497 2005-09-20 04:30:46Z alien $ */

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
require_once(ADMIN_ROOT . '/include/prefs.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if (!isset($_SESSION['valid_user'])) $_GET['mode'] = 'error';
if (!isset($_GET['mode'])) $_GET['mode'] = 'prefs';

// Begin Preferences includes

switch($_GET['mode']) {
	case prefs:
        $contentHash    = getPrefs();
		break;
	case edit:
        $contentHash    = changePrefs();
		break;
    case error:
        $_content       = 'Error';
        break;
        
}

// Build our content base on $contentHash
$_error     = $contentHash['error'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];
$_jscript   = $contentHash['jscript'];

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
