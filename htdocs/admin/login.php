<?php
/* $Id: login.php 514 2005-09-29 20:12:09Z alien $ */

session_name('AdminSessID');
session_start();

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/search.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if (isset($_POST['userid']) && isset($_POST['password'])) {
    // The user has just attempted to login

    mynews_connect();
    $pass   = md5($_POST['password']);
    $query  = "
        SELECT
            uid,
            user,
            status,
            name,
            email
        FROM
            " . $myNewsConf['db']['table']['authors'] . "
        WHERE
            user='" . $_POST['userid'] . "'
        AND
            password='" . $pass . "'";

    $result = mysql_query($query);
    $r = mysql_fetch_assoc($result);

    if (mysql_num_rows($result) > 0) {
         extract($r);
        // if the user account exists, register the user id
        $_SESSION['valid_user'] = $user;
        $_SESSION['status']     = $status;
        $_SESSION['fullname']   = $name;
        $_SESSION['email']      = $email;
        $_SESSION['uid']        = $uid;
    }
}

if (isset($_SESSION['valid_user'])) {

    header('Location: ' . base64_decode($_GET['return_to']));

} else {
    if (isset($_POST['userid'])){
        $_title     = 'Site Administration : Login: Please Try Again';
        // They have tried, and failed, to login
        $_content  .= '<blockquote>';
        $_content  .= '<p>';
        $_content  .= 'The system could not log you in:  <i>Invalid Username/Password combination</i>';
        $_content  .= '<br />';
        $_content  .= '<br />';
        $_content  .= print_login_form();
        $_content  .= '</blockquote>';

    } else {
      // Either they have logged out, or have not logged in yet
        $_title     = 'Site Administration : Login';
        $_content  .= '<blockquote>' . "\n";
        $_content  .= print_login_form();
        $_content  .= '</blockquote>' . "\n";
    }
    
}

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


/*******************************************************************/
function print_login_form(){
global $myNewsConf;

    $output = <<<EOT
        <form method="POST" action="{$_SERVER['PHP_SELF']}?return_to={$_GET['return_to']}">
    <table border="0" align="left">
        <tr>
       <td>
        <b>Userid:</b>
       </td>
           <td>
        <input class=textbox type="text" name="userid">
       </td>
    </tr>
        <tr>
       <td>
        <b>Password:</b>
       </td>
           <td>
        <input class=textbox type="password" name="password">
       </td>
    </tr>
    <tr>
       <td>
        &nbsp;
       </td>
       <td>
        &nbsp;
       </td>
    </tr>
        <tr>
           <td>
        &nbsp;
           </td>
           <td>
        {$myNewsConf['button']['submit']}
       </td>
    </tr>
    </table>
    </form>

EOT;

    return $output;
} //End print_login_form()
?>
