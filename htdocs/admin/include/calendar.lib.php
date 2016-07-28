<?
/* $Id: calendar.lib.php 495 2005-09-16 14:12:09Z alien $ */

/*******************************************************************/
function calAdmin(){
/**
 * This function outputs the authors administration navigation.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : Calendar : Select Tool';
    $output = loginInfo();
    $output.= <<<EOT

    <ul>
        <li><a href="{$baseCalAdmin_URI}?mode=calAdd">New Event</a></li>
        <li><a href="{$baseCalAdmin_URI}?mode=calList">Modify/Delete an Event</a></li>
        <li><a href="{$baseCalAdmin_URI}?mode=calReport">Calendar Report utility</a></li>
    </ul>

EOT;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calAdd(){
/**
 * This function outputs a form to fill out for adding a new calendar
 * event
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    // Page specific javascript.
    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
            with(document.the_form){
                if( recurring[1].checked && duration.value == '' ){
                    alert('Please enter the duration for recurrence.');
                    return false;
                }
                if( type.value == 'none' && new_type.value == '' ){
                    alert('Please enter an Event Type.');
                    return false;
                }
                if( title.value == '' ){
                    alert('Please fill in the "Title" field.');
                    return false;
                }
            }
            return true;
        }

        //-->
        </script>
EOT;

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query  = '
        SELECT
            distinct(type) as type
        FROM
            ' . $myNewsConf['db']['table']['calendar'] . '
        ORDER by
            type';

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : New Event';

    // Generate out output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form method="post" action="' . $baseCalAdmin_URI . '?mode=calAdded" name="the_form" onSubmit="return isOK()">';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    // Date
    $output.= "\n\t\t\t" . '<td valign="top" width="35%">';
    $output.= "\n\t\t\t\t" . '<b>1)</b> Event Date:';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="month">';

    $month  = 1;
    // Loop through a possible 12 monthers and output them as options for a select list.
    while($month <= 12){
        $selected   = '';
        $monname    = date('M', mktime(0,0,0,$month,1,2000));
        if($monname == date('M')) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $month . '"' . $selected . '>' . $monname . '</option>';
        $month++;
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="day">';

    $day    = 1;
    // Loop through a possible 31 days and output them as options for a select list
    while($day <= 31){
        $selected   = '';
        if($day == date('d')) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $day . '"' . $selected . '>' . $day . '</option>';
        $day++;
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="year">';

    // Build an array of the previous,current, and next two years.
    $yArray = array(date('Y')-1, date('Y'), date('Y')+1, date('Y')+2);
    // Loop through the array and output the years as options for a select list.
    foreach($yArray as $year){
        $selected   = '';
        if($year == date('Y')) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $year . '"' . $selected . '>' . $year . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    // Type
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '<b>2)</b> Event Type:';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="type">';
    $output.= "\n\t\t\t\t\t" . '<option value="none">Create New Type</option>';

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $output.= "\n\t\t\t\t\t" . '<option value="' . $type . '">&nbsp;&nbsp;-&nbsp;' . $type . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<b>Note:</b> <small>Only fill out the following field if';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;';
    $output.= "\n\t\t\t\t" . 'if you are creating a new event type';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="new_type">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    // Title
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '<b>3)</b> Event Title: <small>(required)</small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="title">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td valign="top">&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    // Recurring
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '<b>4a)</b> Recurring:';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring" value="0" checked> No';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring" value="1"> Yes';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '<b>4b)</b> If it is recurring specify one of the following';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring_type" value="daily" checked> Daily';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring_type" value="weekly"> Weekly';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring_type" value="bi-weekly"> Bi-Weekly';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="recurring_type" value="monthly"> Monthly';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<b>4c)</b> Duration:';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="duration" size="2" maxlength="2"> Duration';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<b>Note:</b> <small>If you choose bi-weekly and specify';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;';
    $output.= "\n\t\t\t\t" . 'an odd number of weeks, the';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;';
    $output.= "\n\t\t\t\t" . 'computer will round down<small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    // Description
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="2" valign="top">';
    $output.= "\n\t\t\t\t" . '<b>5)</b> Description';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="descrip" rows="10" cols="50" wrap="virtual"></textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" colspan="2">';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . $myNewsConf['button']['submit'];
    $output.= "\n\t\t\t" .'</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calAdded(){
/**
 * This function takes the data submitted from calAdd() and inserts
 * it into the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    // Need to set the Event Type based on whether it exists, or it is new.
    if($_POST['type'] == 'none') $_POST['type'] = $_POST['new_type'];

    // Timestamp for event, or for first occurence of recurring events.
    $initTime   = mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);


    // We need to set a create date-time so it will be defined as $nowTime
    $nowTime    = date('Y-m-d H:i:s');

    // Connect to the database.
    mynews_connect();

    // If it is a recurring event, we need to calculate the dates to insert
    // into the database.
    if($_POST['recurring']){

        // Create a unique md5 sum for the series of events.
        $md5string  = $_POST['title'] . $initTime . $_SESSION['valid_user'] .mktime();
        $md5sum     = md5($md5string);

        switch($_POST['recurring_type']){
            case 'daily':
                for($i=0;$i < $_POST['duration']; $i++){
                    $insMonth   = date('m' , $initTime + $i*86400);
                    $insDay     = date('d' , $initTime + $i*86400);
                    $insYear    = date('Y' , $initTime + $i*86400);

                    // Build an array of dates so we can output them later.
                    $recArray[] = $initTime + $i*86400;

                    // Build and execute the query.
                    $query  = '
                    INSERT into
                        ' . $myNewsConf['db']['table']['calendar'] . "
                    VALUES( NULL,
                            '" . $nowTime                               . "',
                            '" . $insMonth                              . "',
                            '" . $insDay                                . "',
                            '" . $insYear                               . "',
                            '" . addslashes($_POST['type'])             . "',
                            '" . addslashes($_POST['title'])            . "',
                            '" . addslashes($_POST['descrip'])          . "',
                            '" . addslashes($_SESSION['valid_user'])    . "',
                            '" . $md5sum                                . "',
                            '1')";

                    $result = mysql_query($query);
                    $sqlErr.= myNewsChkSqlErr($result, $query);
                }
                break;
            case 'weekly':
                for($i=0;$i < $_POST['duration']; $i++){
                    $insMonth   = date('m', $initTime + $i*7*86400);
                    $insDay     = date('d', $initTime + $i*7*86400);
                    $insYear    = date('Y', $initTime + $i*7*86400);

                    // Build an array of dates so we can output them later.
                    $recArray[] = $initTime + $i*7*86400;

                    // Build and execute the query.
                    $query  = '
                    INSERT into
                        ' . $myNewsConf['db']['table']['calendar'] . "
                    VALUES( NULL,
                            '" . $nowTime                               . "',
                            '" . $insMonth                              . "',
                            '" . $insDay                                . "',
                            '" . $insYear                               . "',
                            '" . addslashes($_POST['type'])             . "',
                            '" . addslashes($_POST['title'])            . "',
                            '" . addslashes($_POST['descrip'])          . "',
                            '" . addslashes($_SESSION['valid_user'])    . "',
                            '" . $md5sum                                . "',
                            '1')";

                    $result = mysql_query($query);
                    $sqlErr.= myNewsChkSqlErr($result, $query);
                }
                break;
            case 'bi-weekly':
                for($i=0;$i < $_POST['duration']; $i++){
                    $insMonth = date('m' , $initTime + $i*2*7*86400);
                    $insDay   = date('d' , $initTime + $i*2*7*86400);
                    $insYear  = date('Y' , $initTime + $i*2*7*86400);

                    // Build an array of dates so we can output them later.
                    $recArray[] = $initTime + $i*2*7*86400;

                    // Build and execute the query.
                    $query  = '
                    INSERT into
                        ' . $myNewsConf['db']['table']['calendar'] . "
                    VALUES( NULL,
                            '" . $nowTime                               . "',
                            '" . $insMonth                              . "',
                            '" . $insDay                                . "',
                            '" . $insYear                               . "',
                            '" . addslashes($_POST['type'])             . "',
                            '" . addslashes($_POST['title'])            . "',
                            '" . addslashes($_POST['descrip'])          . "',
                            '" . addslashes($_SESSION['valid_user'])    . "',
                            '" . $md5sum                                . "',
                            '1')";

                    $result = mysql_query($query);
                    $sqlErr.= myNewsChkSqlErr($result, $query);
                }
                break;
            case 'monthly':
                for($i=0;$i < $_POST['duration']; $i++){
                    $insMonth   = date('m' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));
                    $insDay     = date('d' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));
                    $insYear    = date('Y' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));

                    // Build an array of dates so we can output them later.
                    $recArray[] = mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']);

                    // Build and execute the query.
                    $query  = '
                    INSERT into
                        ' . $myNewsConf['db']['table']['calendar'] . "
                    VALUES( NULL,
                            '" . $nowTime                               . "',
                            '" . $insMonth                              . "',
                            '" . $insDay                                . "',
                            '" . $insYear                               . "',
                            '" . addslashes($_POST['type'])             . "',
                            '" . addslashes($_POST['title'])            . "',
                            '" . addslashes($_POST['descrip'])          . "',
                            '" . addslashes($_SESSION['valid_user'])    . "',
                            '" . $md5sum                                . "',
                            '1')";

                    $result = mysql_query($query);
                    $sqlErr.= myNewsChkSqlErr($result, $query);
                }
                break;
        }
    } else {
        $insMonth   = date('m' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
        $insDay     = date('d' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
        $insYear    = date('Y' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));

        // Build an array of dates so we can output them later.
        $recArray[] = mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

        // Build and execute the query.
        $query  = '
        INSERT into
            ' . $myNewsConf['db']['table']['calendar'] . "
        VALUES( NULL,
                '" . $nowTime                               . "',
                '" . $insMonth                              . "',
                '" . $insDay                                . "',
                '" . $insYear                               . "',
                '" . addslashes($_POST['type'])             . "',
                '" . addslashes($_POST['title'])            . "',
                '" . addslashes($_POST['descrip'])          . "',
                '" . addslashes($_SESSION['valid_user'])    . "',
                '',
                '1')";

        $result = mysql_query($query);
        $sqlErr.= myNewsChkSqlErr($result, $query);
    }
    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Here we check to see if $_POST['type'] exists in the notification list
    // and if so, send an email to the owner of the list defined by ['admin']['cal']['mail']
    $notifyTypes= explode(':', strtolower($myNewsConf['admin']['cal']['list']));
    if(in_array(strtolower($_POST['type']),$notifyTypes)){
        // Let the submitter know that this is a notify event.
        $notify = "\n\t\t" . 'This event is in the notify list.  Sending notification to:';

        // Set the description field, if it is not already
        if(empty($_POST['descrip'])) $_POST['descrip'] = 'None Given';

        // Set the link URI
        $eventURI   = $myNewsConf['default']['siteurl'] . '/' . $myNewsConf['scripts']['event'] . '?title=' . urlencode($_POST['type']) . '&day=' . $_POST['day'] . '&month=' . $_POST['month'] . '&year=' . $_POST['year'];

        // We output the list of dates that were requested.
        foreach($recArray as $eventDate){
            $dList .= "\n\t" . date($myNewsConf['format']['date']['nice'], $eventDate);
        }

        //$header     = 'From: ' . $myNewsConf['default']['sitename'] . ' Admin <'. $myNewsConf['admin']['site']['mail'] . ">\nX-Mailer: PHP/" . phpversion();  
        $header     = 'From: ' . $myNewsConf['default']['sitename'] . ' Admin <'. $_SESSION['email'] . ">\nX-Mailer: PHP/" . phpversion();  
        $subject    = "New " . $_POST['type'] . " event.";
        $body       = "\n" . 'There is a new ' . $event_title . ' event on the ' . $myNewsConf['default']['sitename'] . ' website.';
        $body      .= "\n" . 'Submitted by: ' . $_SESSION['fullname'] . ' (' . $_SESSION['valid_user'] . ')';
        $body      .= "\n\n" . 'Date Submitted: ' . date($myNewsConf['format']['date']['nice']);
        $body      .= "\n" . 'Date(s) Requested:';
        $body      .= $dList;
        $body      .= "\n\n" . 'Reason: ' . $_POST['descrip'];
        $body      .= "\n\n" . 'You can view this event at:';
        $body      .= "\n\t" . $eventURI;
    
        // Buil the list of recipients.
        $recipients = $myNewsConf['admin']['cal']['mail'] . ',' . $_SESSION['email'];
        // Send the message.
        mail($recipients, $subject, $body, $header);
    }

    // We want our success page to refresh back to the News admin page.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseCalAdmin_URI . '?mode=admin">';

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : Added : <small>' . $_POST['type'] . '</small> : <small>' . $_POST['title'] . '</small>';

    // Generate the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The <b>' . $_POST['type'] . '</b> event has been added to the database.';
    if($_POST['recurring']){
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;';
        $output.= "\n\t\t" . '<b>Note:</b> <small>It is a recurring event which will appear <i>' . $_POST['recurring_type'] . '</i></small>';
    }
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calList(){
/**
 * This function outputs a list of events for a particular user to 
 * either edit or delete.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    // Connect to the database.
    mynews_connect();

    // If we didn't pass $_GET['show'] in the URI, we need to set it here.
    if(!$_GET['show']) $_GET['show'] = 0;

    // Build our where clause based on whether we are an admin or not.
    $wClause    = '';
    if($_SESSION['status'] != 'Admin') $wClause = "WHERE userid = '" . $_SESSION['valid_user'] . "'";

    // Build and execute the query base on the users status.
    $query  = "
        SELECT
            eid,
            type,
            title,
            userid,
            recurring,
            UNIX_TIMESTAMP(concat(year,'-',month,'-',day)) as date
        FROM
            " . $myNewsConf['db']['table']['calendar'] . '
        ' . $wClause . '
        ORDER by
            date desc
        LIMIT
            ' . $_GET['show'] . ',
            ' . $myNewsConf['default']['limit'];

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript= <<<EOT
        <script language="JavaScript">
        <!--

        function confirmDelete(ID,SHOW,TYPE,TITLE,REC,USERID){
            if(REC == 0){
                where_to= confirm("Do you really want to delete the event:\\n\\n" + TITLE);
                if (where_to== true) { 
                    window.location="{$baseCalAdmin_URI}?mode=calDeleted&eid=" + ID + "&title=" + TYPE + "&userid=" + USERID;
                } else {  
                    window.location="{$baseCalAdmin_URI}?mode=calList&show=" + SHOW; 
                }
            } else {
                where_to= confirm("Do you really want to delete the recurring event:\\n\\n" + TITLE); 
                if (where_to== true) { 
                    window.location="{$baseCalAdmin_URI}?mode=calDeleted&eid=" + ID + "&recurring=" + REC + "&title=" + TYPE + "&userid=" + USERID;
                } else {  
                    window.location="{$baseCalAdmin_URI}?mode=calList&show=" + SHOW; 
                }
            }
        }

        //-->
        </script>
EOT;

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t\t" . '<b>Note:</b> <small>Select an for the event you would like to modify.  If a</small> (&clubs;) <small>appears in front of the event, this indicates it is part of a series of recurring events.  Modifying a recurring the event will alter all of it\'s associated events.<small>';
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t\t" . '<tr>';
    $output.= "\n\t\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t\t" . '<td><b>Type:</b></td>';
    $output.= "\n\t\t\t\t" . '<td><b>Title:</b></td>';
    if($_SESSION['status'] == 'Admin'){
        $output.= "\n\t\t\t\t" . '<td><b>Owner:</b></td>';
    }
    $output.= "\n\t\t\t\t" . '<td><b>Date:</b></td>';
    $output.= "\n\t\t\t" . '</tr>';

    while($row = mysql_fetch_assoc($result)){
        extract($row);

        $recurr = '';
        $recset = '0';
        if($recurring) $recurr  = '&clubs;';
        if($recurring) $recset  = $recurring;

        $date   = date($myNewsConf['format']['date']['nice'], $date);
    
        $output.= "\n\t\t\t" . '<tr>';
        $output.= "\n\t\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t\t" . $recurr;
        $output.= "\n\t\t\t\t" . '</td>';
        $output.= "\n\t\t\t\t" . '<td width="15%" nowrap>';
        $output.= "\n\t\t\t\t\t" . '( <a href="' . $baseCalAdmin_URI . '?mode=calEdit&eid=' . $eid . '&title=' . base64_encode($type) . '&userid=' . $userid . '">Edit</a>';
        $output.= "\n\t\t\t\t\t" . ' | ';
        $output.= "\n\t\t\t\t\t" . ' <a href="JavaScript:confirmDelete(\'' . $eid . "','" . $_GET['show'] . "','" . urlencode($type) . "','" . addslashes($title) . "','" . $recset . "','" . $userid . '\')">Delete</a>)';
        $output.= "\n\t\t\t\t" . '</td>';
        $output.= "\n\t\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t\t" . $type;
        $output.= "\n\t\t\t\t" . '</td>';
        $output.= "\n\t\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t\t" . $title;
        $output.= "\n\t\t\t\t" . '</td>';
        if($_SESSION['status'] == 'Admin'){
            $output.= "\n\t\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t\t" . $userid;
            $output.= "\n\t\t\t\t" . '</td>';
        }
        $output.= "\n\t\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t\t" . '<small>' . $date . '</small>';
        $output.= "\n\t\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '</tr>';
    }

    $output.= "\n\t\t" . '</table>';
    $output.= "\n\t\t" . '</blockquote>';

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : Modify';

    // Build our navigation.

    // Query the database and determine how many records we are dealing
    // with.
    $query  = '
        SELECT
            count(*) as total
        FROM
            ' . $myNewsConf['db']['table']['calendar'] . '
        ' . $wClause;
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
     extract($row);

    $next   = $_GET['show'] + $myNewsConf['default']['limit'];
    $back   = $_GET['show'] - $myNewsConf['default']['limit'];

    $output.= "\n\t\t" . '<p align="center">';


    // If $_GET['show'] is zero, we need to change it to a one, for
    // readability.
    if($_GET['show'] == 0) $_GET['show'] = 1;

    // $vor is $total by default.
    $vor    = $total;

    // If we are not on the last page, set $vor to $next.
    if($next < $total) $vor = $next;
    
    // Output where we are in english.
    $output .= '<small>[' . $_GET['show'] . ' - ' . $vor . ' of ' .  $total . ']</small> <br />';

    if($back >= 0) $output.= "\n\t\t" . '<a href="' . $baseCalAdmin_URI . '?mode=calList&show=' . $back . '">' . $myNewsConf['button']['back'] . '</a>';

    if($back >= 0 && $next < $total) $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    if($next < $total) $output.= "\n\t\t" . '<a href="' . $baseCalAdmin_URI . '?mode=calList&show=' . $next . '">' . $myNewsConf['button']['next'] . '</a>';
    

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calDeleted(){
/**
 * This function deletes the selected calendar event.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$_GET['userid']);
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    // Build the WHERE clause, based on whether this is a recurring event
    $wClause    = 'WHERE eid = ' . $_GET['eid'];
    if($_GET['recurring']) $wClause = "WHERE recurring = '" . $_GET['recurring'] . "'";

    // Build and excute the query.
    $query  = '
        DELETE
        FROM
            ' . $myNewsConf['db']['table']['calendar'] . '
            ' . $wClause . "
        AND
            userid = '" . $_GET['userid'] . "'";

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // We want our success page to refresh back to the News admin page.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseCalAdmin_URI . '?mode=calList">';

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : Deleted : <small>' . $_GET['type'] . '</small>';

    // Generate the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The';
    if($_GET['recurring']) $output.= ' recurring ';
    $output.= 'event: <small><b>' . $_GET['title'] . '</b></small> has been removed from the database.';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calEdit(){
/**
 * This function populates and outputs a  form with data stored
 * about a particular event in the calendar table of the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$_GET['userid']);
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    // Build and execute the query
    $query  = '
        SELECT
            *
        FROM
            ' . $myNewsConf['db']['table']['calendar'] . '
        WHERE
            eid = ' . $_GET['eid'];
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
    extract($row);

    // Build our event "type" query.
    $query  = '
        SELECT
            distinct(type) as types
        FROM
            ' . $myNewsConf['db']['table']['calendar'];
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript= <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
            with(document.the_form){
                if( type.value == 'none' && new_type.value == '' ){
                    alert('Please enter an Event Type.');
                    return false;
                }
                if( title.value == '' ){
                    alert('Please fill in the "Title" field.');
                    return false;
                }
            }
            return true;
        }

        //-->
        </script>
EOT;

    // Generate our content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form method="post" action="' . $baseCalAdmin_URI . '?mode=calEdited" name="the_form" onSubmit="return isOK">';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>1)</b> Event Type:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="type">';
    $output.= "\n\t\t\t\t\t" . '<option value="none">&nbsp;&nbsp;-&nbsp; Create New Type</option>';
    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $selected = '';
        if($type == $types) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $types . '"' . $selected . '>' . $types . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<b>Note:</b> <small>Only fill out the following field if';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '&nbsp;';
    $output.= "\n\t\t\t\t" . 'you are creating a new event type</small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="new_type">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>2)</b> Event Title:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="title" value="' . $title . '" size="' . $tWidth . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>3)</b> Description:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="descrip" cols="50" rows="10">' . $descrip . '</textarea>';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="recurring" value="' . $recurring . '">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="userid" value="' . $userid . '">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="eid" value="' . $eid . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '</blockquote>';

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : <a href="' . $baseCalAdmin_URI . '?mode=calList">Modify</a> : Edit : <small>' . $title . '</small>';

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calEdited(){
/**
 * This function takes the data submitted from calEdit() and updates
 * the calendar table with the appropriate values.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$_POST['userid']);
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    // If $_POST['type'] is set to none, we need to set it with new_type's value
    if($_POST['type'] == 'none') $_POST['type'] = $_POST['new_type'];

    // Generate our where clause based on whether this is a recurring event.
    $wClause    = 'WHERE eid = ' . addslashes($_POST['eid']);
    if($_POST['recurring']) $wClause = "WHERE recurring = '" . addslashes($_POST['recurring']) . "'";

    // Generate and execute our query.
    $query  = '
        UPDATE
            ' . $myNewsConf['db']['table']['calendar'] . "
        SET
            type    = '" . addslashes($_POST['type'])   . "',
            title   = '" . addslashes($_POST['title'])  . "',
            descrip = '" . addslashes($_POST['descrip']). "'
        " . $wClause . "
        AND
            userid  = '" . addslashes($_POST['userid']) . "'";
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // We want our success page to refresh back to the News admin page.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseCalAdmin_URI . '?mode=calList">';

    // Generate the page and content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseCalAdmin_URI . '?mode=admin">Calendar</a> : <a href="' . $baseCalAdmin_URI . '?mode=calList">Modify</a> : Edited : <small>' . $_POST['title'] . '</small>';

    // Generate our content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The';
    if($_POST['recurring']) $output.= ' <i>recurring</i>';
    $output.= ' event: <small>' . $_POST['title'] . '</small> has been updated in the database.';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calReport(){
global $myNewsConf;

    if(!$_POST['begin'] && !$_POST['end']){
        $title  = 'Report Date Selector:';

        // Get a list of current users so we can build a select box for them.
        $userArray  = calgetUserIDs();

        $output.= "\n\t" . '<form method="post" action="' . $baseCalAdmin_URI . '?mode=calReport">';
        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t" . '<table>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . 'From:';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>';

        $output.= "\n\t\t" . DateSelector('begin', time(), false);

        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . 'To:';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>';

        $output.= "\n\t\t" . DateSelector('end', time(), false);

        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . 'User Filter:';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>';

        $output.= "\n\t\t" . '<select name="userid">';
        $output.= "\n\t\t\t" . '<option value="all" selected>All</option>';
        $userKeys   = array_keys($userArray);
        foreach($userKeys as $userKey){
            $output.= "\n\t\t\t" . '<option value="' . $userKey . '">' . $userArray[$userKey] . '</option>';
        }
        $output.= "\n\t\t" . '</select>';

        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t" . '<td>';
        $output.= "\n\t\t" . '</td>';
        $output.= "\n\t\t" . '<td>';
        $output.= "\n\t\t" . $myNewsConf['button']['submit'];
        $output.= "\n\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t" . '</table>';
        $output.= "\n\t" . '</blockquote>';
        $output.= "\n\t" . '</form>';
    } else {
        $title  = 'Calendar Report:';


        $sDate  = mktime(0,0,0,$_POST['begin']['month'],$_POST['begin']['day'],$_POST['begin']['year']);
        $eDate  = mktime(0,0,0,$_POST['end']['month'],$_POST['end']['day'],$_POST['end']['year']);

        // Connect to the database.
        mynews_connect();

        // If they supply a userid, we need to adjust the query properly.
        if($_POST['userid'] != 'all'){
            $wClause = "WHERE userid = '" . addslashes($_POST['userid']) . "'";
        }

        // Build and execute the query base on the users status.
        $query  = "
            SELECT
                a.eid,
                a.type,
                a.title as etitle,
                a.userid,
                b.name,
                a.ctstmp,
                UNIX_TIMESTAMP(concat(year,'-',month,'-',day)) as vdate
            FROM
                " . $myNewsConf['db']['table']['calendar'] . ' as a
            LEFT JOIN
                ' . $myNewsConf['db']['table']['authors'] . ' as b
            ON a.userid = b.user
            ' . $wClause . '
            HAVING vdate between ' . $sDate . ' AND ' . $eDate . '
            ORDER by vdate';

        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        // Return with an error if it exists.
        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        // Loop through our results and stuff each entry into a hash.
        while($row = mysql_fetch_assoc($result)){
            extract($row);
            $eventArray[$vdate][$type] = array(
                'userid'    => $userid,
                'name'      => $name,
                'ctstmp'    => $ctstmp,
                'title'     => $etitle);
        }

        // Now we need to format the data into a readable format
        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t" . '<pre>';

        $eventKeys  = array_keys($eventArray);
        foreach($eventKeys as $eventKey){
            $output.= "\n" . date($myNewsConf['format']['date']['nice'],$eventKey);
            $nameKeys = array_keys($eventArray[$eventKey]);
            foreach($nameKeys as $nameKey){
                if($eventArray[$eventKey][$nameKey]['ctstmp'] == '0000-00-00 00:00:00'){
                    $ctstmp = 'N/A';
                } else {
                    $ctstmp = $eventArray[$eventKey][$nameKey]['ctstmp'];
                }
                $output.= "\n\t" . $nameKey;
                $output.= "\n\t\t" . 'Name:' . "\t\t" . $eventArray[$eventKey][$nameKey]['name'];
                $output.= "\n\t\t" . 'Title:' . "\t\t" . $eventArray[$eventKey][$nameKey]['title'];
                $output.= "\n\t\t" . 'Submitted:' . "\t" . $ctstmp;
            }
            $output.= "\n";
        }
        $output.= "\n\t" . '</pre>';
        $output.= "\n\t" . '</blockquote>';
    }

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function calGetUserIDs(){
global $myNewsConf;

    // Connect to the database.
    mynews_connect();

    // Build and execute our query
    $query  = '
        SELECT
            name,
            user
        FROM
            ' . $myNewsConf['db']['table']['authors'];

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $userArray[$user] = $name;
    }

    return $userArray;
}
?>
