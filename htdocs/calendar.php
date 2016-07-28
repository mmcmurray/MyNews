<?php
/* $Id: calendar.php 496 2005-09-20 04:29:40Z alien $ */

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
// Require Calendar Classes
include($myNewsConf['path']['sys']['index'] . '/include/classes/cal.api.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$m  = $_GET['month'];
$y  = $_GET['year'];

$baseCalendar_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['calendar'];

if( !$y ) $y = date('Y');
if( !$m ) $m = date('m');

$mymonth    = new month($m,$y);

$_title     = ' Calendar : ' . $mymonth->month_name . ', ' . $mymonth->year;

$_content   = "\n\t" . '<br />';
$_content  .= "\n\t" . '<table align="center" width="100%" cellpadding="0" cellspacing="0">';
$_content  .= "\n\t\t" . '<tr>';
$_content  .= "\n\t\t\t" . '<td colspan="2" class="cal_header">';

$_content  .= $mymonth->draw(array(
            'table_width'   => '100',
            'table_height'  => '350px' ,
            'row_align'     => 'align="left"',
            'row_valign'    => 'valign="top"',
            'table_border'  =>  0));

$_content  .= "\n\t\t\t" . '</td>';
$_content  .= "\n\t\t" . '</tr>';
$_content  .= "\n\t\t" . '<tr>';
$_content  .= "\n\t\t\t" . '<td colspan="2">&nbsp;</td>';
$_content  .= "\n\t\t" . '</tr>';
$_content  .= "\n\t\t" . '<tr>';
$_content  .= "\n\t\t\t" . '<td align="left">';
$_content  .= "\n\t\t\t\t" . '<a href="' . $baseCalendar_URI . '?month=' . $mymonth->prevmonth . '&year=' . $mymonth->prevyear . '">' . $myNewsConf['button']['back'] . '</a>';
$_content  .= "\n\t\t\t" . '</td>';
$_content  .= "\n\t\t\t" . '<td align="right">';
$_content  .= "\n\t\t\t\t" . '<a href="' . $baseCalendar_URI . '?month=' . $mymonth->nextmonth . '&year=' . $mymonth->nextyear . '">' . $myNewsConf['button']['next'] . '</a>';
$_content  .= "\n\t\t\t" . '</td>';
$_content  .= "\n\t\t" . '<tr>';
$_content  .= "\n\t" . '</table>';

$_error     = $contentHash['error'];

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:', $_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
