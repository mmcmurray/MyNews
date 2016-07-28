<?
/* $Id: logout.php 483 2005-08-18 20:15:49Z mmcmurr $ */

// We have to Start the Session before we can kill it

session_name('AdminSessID');
session_start();

// Now that we have started the Session, we go ahead
// and kill it.
session_unset();
session_destroy();

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/search.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

define('ADMIN_ROOT', $myNewsConf['path']['sys']['admin']);
require_once(ADMIN_ROOT . '/include/admin.lib.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$_title = 'Site Administration : Logout Successful';

$relogin= base64_encode($myNewsConf['path']['web']['admin']);

$_content  .= <<<EOT

    <blockquote>
        <p>
        Would you like to <a href="{$myNewsConf['path']['web']['admin']}{$myNewsConf['adminScripts']['login']}?return_to={$relogin}">login</a> again?
    </blockquote>

EOT;

// We only want to build the box if content exist
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error','');
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
