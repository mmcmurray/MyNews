<?
/*******************************************************************/
function osAdd($id, $pid){
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    $projArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project']);
    $project    = $projArray[$pid];

    // Build the page specific javascript.
    $jscript= <<<HTML
        <script language="JavaScript">
        <!--

        function isOK(){
          with(document.the_form){
            if(subject.value == ''){
              alert('You Must Provide an outage Subject');
              return false;
            }
            if(cpm.value == ''){
              alert('You Must Provide the number of Customers per Minute');
              return false;
            }
            if(root.value == ''){
              alert('You Must Provide a Root Cause');
              return false;
            }
            if(reso.value == ''){
              alert('You Must Provide a resolution');
              return false;
            }
          }
          return true;
        }

        //-->
        </script>
HTML;
    
    // Build the page/content title.
    $title  = 'Tickets: Outage Summary: <small>' . $project . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';


    // Here we check to see if a previous OS exists for this ticket.
    $prevOS = osGetPrevious($id);
    if(!is_array($prevOS)) $output = $prevOS;

    // Continue to build the page content.
    $output.= "\n\t" . '<form action="' . $baseTix_URI . '?mode=osAdded" method="post" name="the_form" onSubmit="return isOK()">';
    $output.= "\n\t" . '<table border=0>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>System:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . $project;
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="project" value="' . $project . '">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="pid" value="' . $pid . '">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="id" value="' . $id . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Subject:</b></td>';
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="subject" cols="30" rows="2">[OS] ' . base64_decode($_GET['desc']) . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Severity:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="sev">';

    $optArray   = array(1,2,3,4);
    $optCount   = count($optArray);
    for($i=0; $i < $optCount ; $i++){
        unset($extra);
        if($optArray[$i] == $prevOS['sev']) $extra = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $optArray[$i] . '"' . $extra . '>' . $optArray[$i] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Start Time:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . DateSelector('begin', time());
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>End Time:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . DateSelector('end', time());
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Customers Per Minute:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="cpm" size="3" value="' . $prevOS['cpm'] . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Root Cause:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="root" cols="50" rows="3">' . $prevOS['cause'] . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Resolution:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="reso" cols="50" rows="5">' . $prevOS['resolv'] . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Extra Information:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="xtra" cols="50" rows="3">' . $prevOS['xtra'] . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>';

    // Here we check to see if the default notify key is turned on, and
    // check the checkbox if so.
    $notify = tixGetProjInfo($_GET['project']);
    $notify = eregi_replace(' ','',$notify['notify']);

    if($notify['notify']){
        $output.= "\n\t\t\t\t\t" . 'Email Outage Summary to:';
        $output.= "\n\t\t\t\t\t" . '<blockquote>';

        $notKeys= explode(',',$notify);
        foreach($notKeys as $notKey){
            $output.= "\n\t\t\t\t\t" . '<input type="checkbox" name="notify[' . $notKey . ']" checked> ' . $notKey;
            $output.= "\n\t\t\t\t\t" . '<br />';
        }
        $output.= "\n\t\t\t\t\t" . '</blockquote>';
    }

    $output.= "\n\t\t\t\t\t" . 'Also Notify: <small>(comma delimited list of email addresses)</small>';
    $output.= "\n\t\t\t\t\t" . '<blockquote>';
    $output.= "\n\t\t\t\t\t" . '<input class="textbox" type="text" name="alsoNot" size="30">';
    $output.= "\n\t\t\t\t\t" . '</blockquote>';

    // Output the submit button.
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
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
function osAdded(){
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    // We need to convert the dates to epoch.
    $sEpoch = mktime($_POST['begin']['hour'], $_POST['begin']['minute'], 0, $_POST['begin']['month'], $_POST['begin']['day'],$_POST['begin']['year']);
    $eEpoch = mktime($_POST['end']['hour'], $_POST['end']['minute'], 0, $_POST['end']['month'], $_POST['end']['day'],$_POST['end']['year']);

    // We need to determine how many minutes between the start and end
    // times.
    $tsDiff = ($eEpoch - $sEpoch);
    $durat  = ($tsDiff / 60);

    // Now we determine our Customer Count
    $custCnt= round($durat * $_POST['cpm']);

    // Convert our epoch times to readable dates.
    $sDate  = date('Y-m-d H:i:s', $sEpoch);
    $eDate  = datE('Y-m-d H:i:s', $eEpoch);

    // Now we need to build the email template.
    $tmpHash= array(
        '%SYS%'     => $_POST['project'],
        '%SEV%'     => $_POST['sev'],
        '%STIM%'    => $sDate,
        '%ETIM%'    => $eDate,
        '%DURA%'    => $durat,
        '%ROOT%'    => $_POST['root'],
        '%RESO%'    => $_POST['reso'],
        '%CUST%'    => $_POST['cpm'],
        '%CCNT%'    => $custCnt,
        '%TID%'     => $_POST['id']);

    // Here we generate our recipients based on what POST data
    // was submitted.
    if($_POST['notify'] || $_POST['alsoNot']){
        // if $_POST['notify'] exists, we need to get it's keys.
        if($_POST['notify']) $notifyArray = array_keys($_POST['notify']);

        // if $_POST['alsoNot'] exists, we need to clean it up
        // and insert it's values into $notifyArray
        if($_POST['alsoNot']){
            $notify = eregi_replace(' ','',$_POST['alsoNot']);
            $alsoKeys= explode(',',$notify);
            foreach($alsoKeys as $alsoKey){
                $notifyArray[] = $alsoKey;
            }
        }
        $rcpt   = implode(',',$notifyArray);
    }

    $subj   = $_POST['subject'];
    $body   = parseEmailTemplate($myNewsModule['tix']['template']['os'], $tmpHash);

    if($rcpt && $subj && $body){
        // Send the email message.
        emailNotify($body,$subj,$rcpt);
    }

    // Update the tickets table with a new modified tstmp.
    $tixUpdate  = tixUpdateTstmp($_POST['id']);
    if(is_array($tixUpdate)) return $tixUpdate['error'];

    // Connect to the database.
    mynews_connect();
    
    // We need to insert the Outage Summary into the OS database.
    $insert = '
        INSERT into
            ' . $myNewsModule['db']['tbl']['os']['summary'] . "
        VALUES(
           '" . addslashes($_POST['id']) . "',
           NULL,
           '" . addslashes($_SESSION['uid'])    . "',
           '" . addslashes($_POST['project'])   . "',
           '" . addslashes($_POST['sev'])       . "',
           '" . addslashes($sDate)              . "',
           '" . addslashes($eDate)              . "',
           '" . addslashes($_POST['root'])      . "',
           '" . addslashes($_POST['reso'])      . "',
           '" . addslashes($_POST['cpm'])       . "',
           '" . addslashes($_POST['xtra'])      . "'
        )";

    $result = mysql_query($insert);
    $sqlErr = myNewsChkSqlErr($result, $insert);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseTix_URI . '">';

    // Build the page/content title.
    $title  = 'Tickets: Outage Summary: <small>' . $Sent . '</small>';

    // Build content output.
    $output = loginInfo();
    
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The following outage summary has been sent to: ';

    foreach($notifyArray as $value){
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;<small>' . $value . '</small>';
    }
    $output.= "\n\t" . '</blockquote>';
    $output.= "\n\t" . '<pre>';
    $output.= "\n" . 'Subject: ' . $subj;
    $output.= $body;
    $output.= "\n\t" . '</pre>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function osGet($id){
global $myNewsConf;
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            a.*,
            b.name,
            unix_timestamp(a.tstmp) as timestamp,
            unix_timestamp(a.stime) as stime,
            unix_timestamp(a.etime) as etime
        FROM
            ' . $myNewsModule['db']['tbl']['os']['summary'] . ' as a
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as b
        ON
            a.uid = b.uid
        WHERE
            tid = ' . $id . '
        ORDER by
            tstmp DESC';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // return with an error if we have one.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $output = "\n" . '<html>';
    $output.= "\n" . '<head>';
    $cssFile= $myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css';
    if(file_exists($cssFile)){
        $output.= "\n\t\t" . '<link rel="STYLESHEET" type="text/css" href="' . $myNewsConf['path']['web']['index'] . 'templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css">';
    }
    $output.= "\n" . '</head>';
    $output.= "\n" . '<body>';

    // If there are no updates, we return NULL.
    if(mysql_num_rows($result) == 0){
        $errorTxt   =   $output;
        $errorTxt  .=   "\n" . 'No Outage Summaries Found';
        $errorTxt  .=   "\n" . '</body>';
        $errorTxt  .=   "\n" . '</html>';
        
        $returnArray['content'] = $errorTxt;
        return $returnArray;
    }

    while($row = mysql_fetch_assoc($result)){
        extract($row);

        $duration   = (($etime - $stime) / 60);
        $ccount     = ($cpm * $duration);

        $output.= "\n\t" . '<small>&nbsp;&middot;&nbsp;<u>Outage Summary by ' . $name . ' on ' . date($myNewsConf['format']['date']['default'],$timestamp) . '</u></small>';
        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t\t" . 'System:'          . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $sys       . '<br />';
        $output.= "\n\t\t" . 'Severity:'        . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $sev  . '<br />';
        $output.= "\n\t\t" . 'Start Time:'      . '&nbsp;&nbsp;&nbsp;&nbsp;' . date('Y-m-d H:i',$stime) . '<br />';
        $output.= "\n\t\t" . 'End Time:'        . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . date('Y-m-d H:i',$etime) . '<br />';
        $output.= "\n\t\t" . 'Duration:'        . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $duration  . '<br />';
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . 'Root Cause:'      . '&nbsp;&nbsp;' . $cause     . '<br />';
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . 'Resolution:'      . '&nbsp;&nbsp;&nbsp;&nbsp;' . $resolv    . '<br />';
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . 'Cust. Per Min.:'  . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $cpm       . '<br />';
        $output.= "\n\t\t" . 'Customer Count:'  . '&nbsp;&nbsp;' . $ccount    . '<br />';
        $output.= "\n\t\t" . '<br />';
        $output.= "\n\t\t" . 'Notes: '          . $xtra;
        $output.= "\n\t" . '</blockquote>';
    }

    $output.= "\n" . '</body>';
    $output.= "\n" . '</html>';

    $returnArray['content'] = $output;
return $returnArray;
}
/*******************************************************************/
function osGetPrevious($id){
/**
 * This function checks to see if an outage summary has been previously
 * submitted for a given ticket and returns the proper data to 
 * pre-populated the OS form
 */
global $myNewsConf;
global $myNewsModule;

    // Build our query
    $query  = '
        SELECT
            sev,
            cpm,
            cause,
            resolv,
            xtra
        FROM
            ' . $myNewsModule['db']['tbl']['os']['summary'] . '
        WHERE
            tid = ' . $id . '
        ORDER by tstmp desc
        LIMIT 1';

    // Process the query
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return $sqlErr;

    $row    = mysql_fetch_assoc($result);

    return $row;
}
/*******************************************************************/
?>
