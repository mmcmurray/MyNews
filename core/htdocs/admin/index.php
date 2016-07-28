<?php
/* $Id: index.php 507 2005-09-27 21:07:43Z alien $ */

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

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$_title = ' Site Administration';

$_content   = loginInfo();
$_content  .= <<<EOT

    <blockquote>
	<p>Please select the module you would like to adminstrate.

        <ul>

EOT;

    // Generate links for the builtin modules.
    $linkArray = explode(':',$myNewsConf['modules']['admin']);
    foreach($linkArray as $adminKey){
       $_content   .= "\n\t\t" . '<li><a href="' . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts'][$adminKey] . '">' . $myNewsConf['admin']['name'][$adminKey] . '</a>';
    }

    $_content   .= '<br /><br />';

    // Generate links for actual addon modules.
    if($myNewsModule['adminScripts']){
        $moduleKeys = array_keys($myNewsModule['adminScripts']);
        foreach($moduleKeys as $moduleKey){
            $_content  .= "\n\t\t" . '<li><a href="' . $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts'][$moduleKey] . '">' . $myNewsModule['admin']['name'][$moduleKey] . '</a>';
        }
    }

$_content  .= <<<EOT
        </ul>
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
