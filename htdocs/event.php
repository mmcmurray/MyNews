<?php
/* $Id: event.php 487 2005-08-18 20:20:52Z mmcmurr $ */

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

$mode   = $_GET['mode'];
$title  = urldecode($_GET['title']);        
$eid    = $_GET['eid'];
$day    = $_GET['day'];
$month  = $_GET['month'];
$year   = $_GET['year'];

if (isset($myNewsConf['scripts']['portal'])) $basePortal_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['portal'];
$baseEvent_URI  = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['event'];

// We're going to go ahead and make our database connection
mynews_connect();

// Here we check to see if our event type, or mode matches zoom.
// If so, we'll continue in zoom mode where users can read/post comments
// on specific events.
if ($mode == 'zoom'){

    // Generate our query
    $query = ("
            SELECT  a.*,
                    b.name as name
            FROM " . $myNewsConf['db']['table']['calendar'] . " as a
            LEFT JOIN
                " . $myNewsConf['db']['table']['authors'] . " as b
            ON a.userid = b.user
            WHERE
                a.eid='$eid'
                AND
                a.active=1");

    // Run the query
    $result = mysql_query($query);

    // If there is syntactical error with the query, this will tell us.
    $_content.= myNewsChkSqlErr($result,$query);

    // Turn our results into a hash
    $row = mysql_fetch_assoc($result);

    // Create our var defs out of the $row hash
    extract($row);

    // We need to set the create timestamp if it does not exists or is default of '0000-00-00 00:00:00'
    if($ctstmp == '0000-00-00 00:00:00') $ctstmp = 'N/A';

    // Here we turn newlines in the mesg_text data
    // and turn it into HTML <br />'s
    $descrip        = nl2br($descrip);

    $mymonth        = new month($msg_month,$msg_year);
    $event_date     = "$mymonth->month_name $day , $mymonth->year";
    $_title  = $type . ' : ' . $title . ' : ' . $event_date;

    $cryptTitle = base64_encode($msg_title . ' : ' . $row[$msg_who]);

    if(!empty($recurring)) $isRecurring = '&nbsp;&nbsp;&nbsp;&nbsp;<small><i>* This is a recurring event</i></small>';

    $_content .= <<<EOT
    <blockquote>
        <table border="0" cellpadding="2">
            <tr>
                <td><b>Event Title:</b></td>
                <td>$title</td>
            </tr>
            <tr>
                <td><b>Date Added:</b></td>
                <td><small>$ctstmp</small></td>
            </tr>
            <tr>
                <td><b>Who:</b></td>
                <td>$name</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><b>Description:</b></td>
                <td>$descrip</td>
            </tr>
            <tr>
                <td span="2">$isRecurring</td>
            </tr>
        </table> 

        <br />
        <p align="left">
    <a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['comments']}/compose/$msg_id/cal/0/$cryptTitle">[Comment]</A>
        <br />

        <br />

EOT;
    // echo out the Comments at the bottom if there are any comments attached to the article.
    $cmquery = mysql_query("select count(*) as total from " . $myNewsConf['db']['table']['comments'] . " where artnr = " . $eid . " and type = 'cal'");  
    $crow = mysql_fetch_assoc($cmquery);
        $cmcount = $crow['total'];
    if($cmcount >= 1){
           comments($msg_id,0,'cal');
    }

// Otherwise we treat it as an event day, and view all events for that day.
} else {

    // We need to add a where clause in case the user wants to view all events for the day vs. particular event types.
    $wClause = '';
    if($title){
        $wClause= 'AND a.type = \'' . $title . '\'';
    } else {
        $title  = 'Events';
    }

    // Generate our query
    $query = ("
            SELECT
                a.*,
                b.name as name 
            FROM
                " . $myNewsConf['db']['table']['calendar'] . " as a
            LEFT JOIN
                " . $myNewsConf['db']['table']['authors'] . " as b
            ON
                a.userid = b.user
            WHERE
                a.day='$day'
                AND
                a.month='$month'
                AND
                a.year='$year'
                $wClause
                AND
                a.active=1");

    // Run the query
    $result = mysql_query($query);

    // If there is syntactical error with the query, this will tell us.
    $_content.= myNewsChkSqlErr($result,$query);

    $mymonth        = new month($month,$year);
    $event_date     = "$mymonth->month_name $day , $mymonth->year";
    $_title         = $title . ' : ' . $event_date;

    $_content.= <<<EOT
<p>
<ul>

EOT;

    while($row = mysql_fetch_assoc($result)){
        extract($row);

        // We need to set the create timestamp if it does not exists or is default of '0000-00-00 00:00:00'
        if($ctstmp == '0000-00-00 00:00:00') $ctstmp = 'N/A';

        if(isset($myNewsConf['scripts']['portal'])) extract($row);

        // Here we turn newlines in the mesg_text data
        // and turn it into HTML <br />'s
        $descrip    = nl2br($descrip);

        $_content.= '<li>';
        $_content.= '<b>' . $title . '</b>';
        $_content.= '<br /><small>Added: ' . $ctstmp . '</small>';
        $_content.= '<br /><small>By: ' . $name . '</small>';
        $_content.= '<br /><small><a href="' . $baseEvent_URI . '?mode=zoom&eid=' . $eid . '" title="Details/Comments">[more]</a></small>';

        //  If message text if not empty, echo it out
        if (!empty($descrip)) $_content.= '<ul><li>' . $descrip . '</li></ul>';

        $_content.= '<br />';
    }
    $_content.= '</ul>';

} // end if()

$_error     = $contentHash['error'];

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:', $_error, 'content');
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
