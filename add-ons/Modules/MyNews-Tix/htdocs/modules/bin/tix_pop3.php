#!/usr/local/php5/bin/php -q
<?php

define('APP_ROOT', dirname(__FILE__));
include('Net/POP3.php');
include('Mail/mimeDecode.php');
include(APP_ROOT . '/../../include/adodb/adodb.inc.php');

/**
 * POP3 Server configs
 */
$cfg['host']    = 'pop.managedmail.com';
$cfg['port']    = '110';
$cfg['user']    = 'tierIItixdev_managedmail.com';
$cfg['pass']    = 'qwerty1';
$cfg['dele']    = false;

/**
 * Filesystem definitions
 */
$cfg['attach_dir']      = APP_ROOT . '/attachments/';
$cfg['max_attch_size']  = '2'; // In Megabytes

/**
 * Database configs
 */
$cfg['db']['host']  = 'localhost';  // The host you database resides on.
$cfg['db']['user']  = 'root';       // Your database user.
$cfg['db']['pass']  = 'noo6Kax';    // Your database user's password.
$cfg['db']['name']  = 'tierIItixdev';// The name of the database we're using.

/**
 * Set some defaults
 */
$cfg['default']['priority'] = 4; // Sets the default priority if a newly inserted
                                 // ticket doesn't match anything in the database.
$cfg['default']['service']  = 1; // Sets the default service if a newly inserted
                                 // ticket doesn't match anything in the database.

/**
 * Database table definitions
 */
$cfg['db']['tbl']['modifications']  = 'modifications';
$cfg['db']['tbl']['tickets']        = 'tickets';
$cfg['db']['tbl']['email_users']    = 'email_users';
$cfg['db']['tbl']['projects']       = 'project';
$cfg['db']['tbl']['priorities']     = 'priority';

/**
 * Misc configs
 */
$cfg['admin']   = 'mike.mcmurray@core.verizon.com';
                        // Email address of your ticket/site admin.
$cfg['name']    = 'TierII Tickets Admin';
                        // The Full name of the person you want email notifcations to come from.
$cfg['email']   = 'tierIItixdev@managedmail.com';
                        // The email address you want ticket notifications to come from.
$cfg['tktuid']  = '1';  // This is the uid of the user you want all 
                        // new tickets and ticket comments posted as
                        // It is preferred to create and "email" user
                        // in the system for this.

$cfg['template']['error']   = <<<EOT
%DATE%
-------------------------------------------------------------------
Error:

%ERR%


What you Submitted:

    Subject: %SUBJ%

    Body:

%BODY%

-------------------------------------------------------------------
EOT;

$cfg['template']['newtkt']  = <<<EOT
%DATE%
-------------------------------------------------------------------
Action: Ticket #%NUM% has been %ACTION%.

Title: %TITLE%


Description:
%DESC%

-------------------------------------------------------------------
Note:  In order to update this ticket, any further response must
       include [#%NUM%] in the subject line.
EOT;

/*** Code       *****************************************************/

// Get a hash of the messages in the mailbox
$msgArray   = popMbox();

// Die with an error if there is nothing to parse.
if(!$msgArray) die('No messages to parse' . "\n");

// We need to loop through each message and parse it accordingly
$msgCount   = count($msgArray);
for($i=1;$i<=$msgCount;$i++){
    $emailAddr  = prsEmailAddr($msgArray[$i]['From']);

    // Parse the email as a MIME message.
    $mimeHash   = prsEmailMIME($msgArray[$i]['Raw']);
    $msgBody    = $mimeHash['body'];
    /*
    // For Debugging purposes
    echo '-----------------------------------------------------' . "\n";
    echo $msgBody;
    continue;
    */

    // Sanity Check:  We can't continue processing the message if there
    // is not a valid sender.  We'll use the verifyEmailAddr() function
    // to see if they exist in the `email_users` table, and if not, add
    // them to it.  If we can't add them for some reason we'll return
    // false and email the administrator with a copy of the message
    $mailUserID = vrfyEmailAddr($emailAddr);
    if(!$mailUserID){
        mailAdminErr($msgArray[$i]);
        echo $i . ': email not valid, going onto next msg' . "\n";
        continue;
    }

    // Here we need to see if the subject indicates an existing ticket
    // If so, we treat it differently
    $emailSubj  = $msgArray[$i]['Subj'];
    $tktExists  = prsEmailSubj($emailSubj);
    if(!$tktExists){

        // If the ticket is considered "new", we need to parse it accordingly
        $bodyHash   = prsNewTkt($msgBody);

        // Check to see if we have all returned values, and if not, email an
        // an error to the sender.
        if(!$bodyHash['priority'] || !$bodyHash['service'] || !$bodyHash['comment']){

            // Build our "replace" string hash so we can build our email
            // out of the defined template.
            $tmplHash   = array(
                    '%DATE%'    => date('l F, dS Y H:i:s A [T]'),
                    '%ERR%'     => genErrMsg(1),
                    '%SUBJ%'    => $emailSubj,
                    '%BODY%'    => indentStr(1,$bodyHash['comment']));

            $body   = parseEmailTemplate($cfg['template']['error'], $tmplHash);
            $subj   = 'Trouble Tickets:  Error in submission';

        // Otherwise we are going to insert the ticket into the database, get the
        // ticket id and email the sender with a response telling them that their
        // ticket has been opened in the system.
        } else {

            // We need to determine which 'priority' id we need to assign the ticket
            $priority   = getPriority($bodyHash['priority']);

            // We need to determine which 'service' id we need to assign the ticket
            $service    = getService($bodyHash['service']);

            // We need to build of hash out of the data we're inserting into the database
            $tktHash= array('creator'       => $mailUserID,
                            'priority'      => $priority,
                            'service'       => $service,
                            'short_desc'    => $emailSubj,
                            'long_desc'     => cleanupBody($bodyHash['comment']));

            // Insert the email data into the database as a 'new' ticket
            $tktNum = dbInsertTkt('new',$tktHash);

            // If for some reason dbInsertTkt() breaks, we need to get out of this loop
            // and let the Admin know.
            if(!$tktNum){
                mailAdminErr($msgArray[$i]);
                echo $i . ': dbInsertTkt returned false, going onto the next msg' . "\n";
                continue;
            }

            // Let's go ahead and parse the attachments and place them on the filesystem
            // if $mimeHash['attch'] == true;
            if($mimeHash['attch']) handleAttachments($msgArray[$i]['Raw'],$tktNum);

            // Build our "replace" string hash so we can build our email
            // out of the defined template.
            $tmplHash   = array(
                    '%DATE%'    => date('l F, dS Y H:i:s A [T]'),
                    '%NUM%'     => $tktNum,
                    '%ACTION%'  => 'Created',
                    '%TITLE%'   => $emailSubj,
                    '%DESC%'    => indentStr(1,$bodyHash['comment']));

            $body   = parseEmailTemplate($cfg['template']['newtkt'], $tmplHash);
            $subj   = 'Trouble Tickets:  Creation notification [#' . $tktNum . ']';
        }
        // Either way, we're going to respond to the sender, so we might as well save
        // a couple of lines of code by breaking this out of the "if/else" checks.
        notifySender($body,$subj,$emailAddr);

    // Else, we've determined that the email refers to an already open ticket.
    // We need to verify the ticket number exists.  If so, we'll continue processing
    // and stuff it into the database.
    } else {
        // Check to see if the ticket exists in the `tickets` table.
        $tktExists  = chkTktExists($tktExists);

        // If it doesn't we send an error to the sender.
        if(!$tktExists){
            // Build our "replace" string hash so we can build our email
            // out of the defined template.
            $tmplHash   = array(
                    '%DATE%'    => date('l F, dS Y H:i:s A [T]'),
                    '%ERR%'     => genErrMsg(2),
                    '%SUBJ%'    => $emailSubj,
                    '%BODY%'    => indentStr(1,$bodyHash['comment']));

            $body   = parseEmailTemplate($cfg['template']['error'], $tmplHash);
            $subj   = 'Trouble Tickets:  Error in submission';

            // Send the error email.
            notifySender($body,$subj,$emailAddr);

        // Otherwise, we know the ticket exists, and can properly insert the comment
        // into the `modifications` table for that ticket.
        } else {

            // Otherwise, we parse it as an "Existing" ticket
            $body   = prsEmailBody($msgBody);

            // We need to build of hash out of the data we're inserting into the database
            $modHash= array('creator'       => $mailUserID,
                            'ticketid'      => $tktExists,
                            'tktnotes'      => $body);

            // Insert the email data into the database as a 'modification'
            $modNum = dbInsertTkt('mod',$modHash);

            // Let's go ahead and parse the attachments and place them on the filesystem
            // if $mimeHash['attch'] == true;
            if($mimeHash['attch']) handleAttachments($msgArray[$i]['Raw'],$tktExists,$modNum);

            // Update the `tickets` table with a new "modified" timestamp.
            $udTstmp= tixUpdateTstmp($tktExists);
        }
    }
}

/*** Functions  *****************************************************/
/**
 * Build the connection to the defined POP3 server and returns
 * a hash of each message containing:
 *  [msgNum]
 *      [From]
 *      [Body]
 */
function popMbox(){
global $cfg;

    // Initiate the class
    $pop3 =& new Net_POP3();

    // Establish a connection to the defined POP3 server.
    $conn = $pop3->connect($cfg['host'],$cfg['port']);
    if(!$conn) die('Failure connecting to: ' . $cfg['host'] . ':' . $cfg['port'] . "\n");

    // Login with the provided USER/PASS combo
    $auth = $pop3->login($cfg['user'], $cfg['pass']);
    if($auth != 1) die('Failure logging in to: ' . $cfg['host'] . ':' . $cfg['port'] . ' / ' . $auth->message . "\n");

    // Get the number of messages in the maildrop
    $numMsg = $pop3->numMsg();

    if($numMsg == 0) return false;

    // Loop through each message and pull out the pertinent information
    for($i=1;$i<=$numMsg;$i++){
        // Get the parsed headers of the message
        $prsHeaders = $pop3->getParsedHeaders($i);

        // Let's go ahead and build a hash of our new messages
        $msgArray[$i]['From'] = $prsHeaders['From'];
        $msgArray[$i]['Subj'] = $prsHeaders['Subject'];
        $msgArray[$i]['Raw']  = $pop3->getMsg($i);

        if($cfg['dele'] == true){
            $pop3->deleteMsg($i);
        }
    }

    // Disconnect from the POP3 server
    $pop3->disconnect();

return $msgArray;
}
/*******************************************************************/
/**
 * Looks at a provided string (the Subject of a message) and determines
 * whether it is a ticket "update" or not.  If [#<ticket_number] exists
 * we will assume the ticket number already exists.
 */
function prsEmailSubj($subject){
    if(preg_match('/\[#(\d+)\]/',$subject,$matches)){
        return $matches[1];
    } else {
        return false;
    }
}
/********************************************************************/
/**
 * Takes an email address, such as found in the From: line and returns the first email address found, stripping it of prevaling and trailing quotes
 * 
 * Here are the formats that it will be able to successfully parse.
 * user@example.com
 * user@example.com, anotheruser@example.com
 * User <user@example.com>
 * User <user@example.com>, Another User <anotheruser@example.com>
 *
 * In the examples where there are 2 addresses only the first will be returned.
 *
 * This function takes the globalized $senderName & $senderEmail  and assigns the values to them
 *
 * @param The email address
*/

function prsEmailAddr($address) {

    // if the email address is in the format: First Last <first.last@domain.com>
    // we will return the actual "address"
	if (preg_match('/(.*?)\<(.*?)\>/', $address, $match)) {
		return $match[2];
	} else {
        return $address;
	}
}
/********************************************************************/
/**
 * Attempts to remove the information that a mail program throws in, like
 *
 * ----- Original Message ----- 
 * From: "Kevin Schroeder" <kevin.schroeder@verizonemail.net>
 * Sent: Tuesday, August 09, 2005 10:58 AM
 * Subject: Re: Dr. appointments and such
 *
 * or
 *
 * On Wed, 2005-08-10 at 10:59, Verizon Messaging wrote:
 *
 * Verizon Messaging wrote:
*/

function prsEmailBody($body) {

    $lines      = split("\r\n", $body);
    $lineCount  = count($lines);

    for($i=0;$i<=$lineCount;$i++){
        // If an email "reply/fwd" type comment exists, we need to go ahead and break out of this loop.
        if(preg_match('/.*?wrote\:/i', $lines[$i]) || preg_match('/Original Message/i', $lines[$i]) || preg_match('/^--$/i', $lines[$i])){
            break;
        } 
        $output.= $lines[$i] . "\r\n";
    }

// Couldn't find anything.  Return as is.
return $output;
}
/********************************************************************/
/**
 * Parses the full message and returns the body of a MIME email.
 */
function prsEmailMIME($msg){
    $params['include_bodies'] = true;
    $params['decode_bodies']  = true;
    $params['decode_headers'] = true;
    //$params['crlf']           = "\r\n";

    $decoder = new Mail_mimeDecode($msg);
    $struct = $decoder->decode($params);

    //print_r($struct);

    // First we check to see if the message type is 'text'
    // if so, we perform a second check to see if it's secondary type
    // is html.  Is so, we strip the html.  Otherwise, we return the body.
    echo '-----------------------------------------------------' . "\n";
    if($struct->ctype_primary == 'text'){
        echo 'Msg is Text:' . "\n";
        if($struct->ctype_secondary == 'html'){
            echo "\t" . 'Body type is: Text/HTML' . "\t\n";
            $mimeHash['body'] = strip_tags($struct->body);
        } else {
            echo "\t" . 'Body type is: Text' . "\t\n";
            $mimeHash['body'] = $struct->body;
        }
    // If the message type is determined to be a 'multipart' message
    // We'll find the "best matched" 'part' of the message and return
    // it.  If the return secondary type is determined to be html, we will
    // send it the the html 'strip' function.
    } elseif ($struct->ctype_primary == 'multipart'){
        echo 'Msg is Multipart: ' . $struct->ctype_secondary . "\n";
        if($struct->ctype_secondary == 'alternative'){
            $mimeHash['body'] = findMultiText($struct);
        } elseif($struct->ctype_secondary == 'mixed'){
            $mimeHash['body'] = findMultiText($struct);
            $mimeHash['attch']= true;
        // If for some reason the user tries to send an email with HTML
        // and multiple attachments, let's dig through the message and
        // find the proper text.
        } elseif($struct->ctype_secondary == 'related'){
            $mimeHash['body'] = findMultiText($struct);
            $mimeHash['attch']= true;
        }
    }
return $mimeHash;
}
/********************************************************************/
/**
 * Takes a MIME object that's of multipart/* type and finds the closest thing
 * to a plain text we it can.
 */
function findMultiText($struct){
    // Loop through each message 'part' and find the closest matching
    // the plain text message.
    foreach($struct->parts as $part){
        if($part->ctype_primary == 'multipart' && $part->ctype_secondary == 'alternative'){
            echo "\t" . 'Body is ' . $part->ctype_primary . '/' . $part->ctype_secondary . "\n";
            $body   = findMultiText($part);
            break;
        }
        if($part->ctype_primary == 'text'){
            if($part->ctype_secondary == 'plain'){
                echo "\t" . 'Body is ' . $part->ctype_primary . '/' . $part->ctype_secondary . "\n";
                $body = strip_tags($part->body);
                break;
            } elseif($part->ctype_secondary == 'html'){
                echo "\t" . 'Body is ' . $part->ctype_primary . '/' . $part->ctype_secondary . "\n";
                $body = strip_tags($part->body);
            }
        }
    }
return $body;
}
/********************************************************************/
/**
 * Parses the body of a new ticket to get out important information such as:
 * Priority = Low
 * System   = Portal
 */
function prsNewTkt($body){

    // Let's go ahead send $body through the "email comment" (fwd/reply)
    // filter.
    $body   = prsEmailBody($body);
    
    // Break $body up into an array of lines
    $lines  = split("\r\n",$body);
    $count  = count($lines);

    // Loop through each line and see if it matches either of the following:
    //      Priority: <value>
    //      Service: <value>
    // and add it to it's equivalent key in the return hash, otherwise we
    // add it to the "comment" key of the hash
    for($i=0;$i<=$count;$i++){
        if(preg_match('/^priority:(.*?)$/i',$lines[$i],$matches)){
            $priority = trim($matches[1]);
        } elseif(preg_match('/^service:(.*?)$/i',$lines[$i],$matches)){
            $system = trim($matches[1]);
        } else {
            $comment.= $lines[$i] . "\n";
        }
    }

    // Here we need to trim off the beginning and ending whitespace 
    // From the ticket comment.
    $comment    = trim($comment);

    // Build the hash based on what we've defined.
    $bodyHash['priority']   = $priority;
    $bodyHash['service']     = $system;
    $bodyHash['comment']    = $comment;

return $bodyHash;
}
/********************************************************************/
/**
 * This function takes a hash that is keyed by what text needs to be
 * replaced in $template.
 *
 */
function parseEmailTemplate($template, $replaceHash) {
    $searchKeys = array_keys($replaceHash);
    foreach($searchKeys as $searchKey){
        $replaceKeys[] = $replaceHash[$searchKey];
    }
	$template = str_replace($searchKeys, $replaceKeys, $template);
    $template = stripslashes(wordwrap($template,70));
    $template = str_replace("\r\n", "\n", $template); 

return $template;
}
/*******************************************************************/
/**
 * This function sends an email to a predefined recipient list
 * with a passed body and subject.
 */
function notifySender($body,$subject,$recipients,$reply='none'){
global $cfg;

    // Here we define the Message headers and the subject.
    $header = 'From: ' . $cfg['name'] . '<' . $cfg['email'] . '>';
    if($reply != 'none') $header.= "\r\n" . 'Reply-To: ' . $reply;
    $header.= "\r\n" . 'X-Mailer: PHP/' . phpversion();
    
    mail($recipients,$subject,$body,$header);
}
/*******************************************************************/
/**
 * Takes a string and indents the beginning of each line to the proper
 * indent length based on what "split" characters you define.
 */
function indentStr($num=1,$string){

    // Calculate where we wrap the line based on how many indents we do.
    $wrap = (70 - (8 * $num));

    // First, we're going to reformat the string by getting rid of all
    // of the line breaks and wordwrapping to 50 characters.

    // turn all "multiple line breaks into something we can switch later.
    $string = str_replace("\r\n\r\n", '%twoline%',$string);
    $string = str_replace("\n\n", '%twoline%',$string);

    // remove all line breaks.
    $string = str_replace("\r\n",'',$string);
    $string = str_replace("\n",'',$string);

    // switch the --double-- back into a 2 line break
    $string = str_replace('%twoline%',"\n\n",$string);

    // Wordwrap the indented text.
    $string = wordwrap($string, $wrap, "\n");

    // Build the padding based on what $num is defined
    $padding    = '';
    for($i=1;$i<=$num;$i++){
        $padding   .= "\t";
    }

    $lines  = split("\n",$string);
    $count  = count($lines);
    
    for($l=0;$l<=$count;$l++){
        $output.= $padding . $lines[$l] . "\n";
    }

return $output;
}
/*******************************************************************/
/**
 * Takes and email address and first checks to see if it is valid.
 * If valid, it compares the address against the `email_users` table
 * to see if the user has emailed before.  If so, it will return true.
 * If the email address doesn't exist, it will attempt to add it to
 * the table.  If it fails for some reason, we will return false and
 * error out.  Otherwise, we'll return true, and everyone will gone
 * their merry little ways.
 */
function vrfyEmailAddr($addr){
global $cfg;

    // First we need to check and see if it is a valid email address
    if(!preg_match('/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/',$addr)) return false;

    $db = dbConnect();

    // Build the query
    $query = <<<EOT
        SELECT *
        FROM {$cfg['db']['tbl']['email_users']}
        WHERE
            addr = '{$addr}'
EOT;
    $rs = $db->Execute($query);
    $rc = $rs->RecordCount();

    // If we come back with a rowcount of 0, we'll try to add it to the
    // the `email_users` table.
    if($rc > 0){
        $row = $rs->FetchRow();
        return $row['id'];
    } else {
        $insert = 'INSERT into ' . $cfg['db']['tbl']['email_users'] . "\n";
        $insert.= "VALUES(NULL,'" . addslashes($addr) . "','1')" . "\n";

        $rs2 = $db->Execute($insert);
        $tkt = $db->Insert_ID();
        return $tkt;
    }

// something didn't work quite right, so we're going to let whoever called us
// know it.
return false;
}
/*******************************************************************/
/**
 * Will take the culprit message and email it to the administrator of the 
 * website.  He can then evaluate what the problem was and whether or not
 * it's fixable.
 */
function mailAdminErr($msgArray){
global $cfg;

        $subj   = 'Trouble Tickets:  Error';
        $body   = 'There was an error processing a particular message.';
        $body  .= 'It\'s contents are as follows:';
        $body  .= "\n";
        $body  .= "\n\t" . 'From:    ' . $msgArray['From'];
        $body  .= "\n\t" . 'Subject: ' . $msgArray['Subj'];
        $body  .= "\n";
        $body  .= "\n\t" . 'Body:';
        $body  .= "\n" . indentStr(2,$msgArray['Raw']);

        notifySender($body,$subj,$cfg['admin']);
}
/*******************************************************************/
/**
 * Accepts an array of "Insert Data" and based on the contents, stuffs
 * that data into the proper DB table(s).  Should return the Insert_ID
 * or "false" if something breaks.
 */
function dbInsertTkt($type,$insHash){
global $cfg;

    // Create the DB connection
    $db = dbConnect();

    // Based on what $type we have, we need the query to behave differently.
    switch($type){
        case 'mod':
            // Insert the ticket contents into the `modifications` table.
            // We'll use the system level "email" (-1) user as the uid.
            $insert  = '
                INSERT into ' . $cfg['db']['tbl']['modifications'] . "
                values( NULL,
                        '" . addslashes($insHash['ticketid'])   . "',
                        NULL,
                        '" . addslashes($insHash['tktnotes'])   . "',
                        '" . addslashes($insHash['creator'])    . "',
                        '1')";
            $rs = $db->Execute($insert);
            $tn = $db->Insert_ID();
            return $tn;
            break;
        case 'new':
            // Insert the "new" ticket contents into the `tickets` table.
            // We'll use the system level "email" (-1) user as the uid.
            $insert  = '
                INSERT into ' . $cfg['db']['tbl']['tickets'] . "
                values( NULL,
                        '1',
                        '" . addslashes($insHash['short_desc']) . "',
                        '" . addslashes($insHash['long_desc'])  . "',
                        '1',
                        NULL,
                        '" . date('Y-m-d H:i:s')                . "',
                        '" . addslashes($insHash['priority'])   . "',
                        '1',
                        '" . addslashes($insHash['service'])    . "',
                        '" . addslashes($insHash['creator'])    . "',
                        '1')";
            $rs = $db->Execute($insert);
            $tn = $db->Insert_ID();
            return $tn;
            break;
        default:
            // If we don't meet the proper critera, or something breaks,
            // We need to return false to indicate there was a problem.
            return false;
    }
}
/*******************************************************************/
/**
 * Initiates a connection to the database and return the connection object
 */
function dbConnect(){
global $cfg;
    // Build the database connection
    $db = ADONewConnection(mysql);
    $db->debug  = false;
    $db->Connect($cfg['db']['host'], $cfg['db']['user'], $cfg['db']['pass'], $cfg['db']['name']);
return $db;
}
/*******************************************************************/
/**
 * Connects to the database and attempts to determine what 'priority' ID
 * most matches the priority set in the newly submitted ticket email.
 */
function getPriority($priority){
global $cfg;

    // Connect to the DB
    $db = dbConnect();

    // Sanity checking
    $priority   = addslashes($priority);

    $query  = <<<EOT
        SELECT id from {$cfg['db']['tbl']['priorities']}
        WHERE name LIKE '%{$priority}%'
EOT;

    $rs = $db->Execute($query);
    $row= $rs->FetchRow();

    if(!$row){
        return $cfg['default']['priority'];
    } else {
        return $row['id'];
    }
}
/*******************************************************************/
/**
 * Connects to the database and attempts to determine what 'service' ID
 * most matches the service set in the newly submitted ticket email.
 */
function getService($service){
global $cfg;

    // Connect to the DB
    $db = dbConnect();

    // Sanity checking
    $priority   = addslashes($priority);

    $query  = <<<EOT
        SELECT id from {$cfg['db']['tbl']['projects']}
        WHERE name LIKE '%{$service}%'
EOT;

    $rs = $db->Execute($query);
    $row= $rs->FetchRow();

    if(!$row){
        return $cfg['default']['service'];
    } else {
        return $row['id'];
    }
}
/*******************************************************************/
/**
 * Check to make sure the ticket exists... otherwise, return an error
 * to false, so we send an error to the sender.
 */
function chkTktExists($num){
global $cfg;

    // Connect to the DB
    $db = dbConnect();

    // Sanity check
    $num= addslashes($num);

    $query  = <<<EOT
        SELECT * from {$cfg['db']['tbl']['tickets']}
        WHERE id = '{$num}'
EOT;

    $rs = $db->Execute($query);
    $row= $rs->FetchRow();

    // if a row is returned, return true
    if($row) return $row['id'];

// By default return false
return false;
}
/*******************************************************************/
/**
 * this function cleans up some of the "forced" line breaks in the "new"
 * ticket submission.  So it shows up cleanly in the presentation field
 * of the website.
 */
function cleanupBody($string){
    $string = str_replace("\r\n\r\n",'%twoline%',$string);
    $string = str_replace("\n\n",'%twoline%',$string);
    $string = str_replace("\r\n",'',$string);
    $string = str_replace("\n",'',$string);
    $string = str_replace('%twoline%',"\n\n",$string);
return $string;
}
/*******************************************************************/
/**
 * Updats the `tickets` table if a modication is inserted into the database.
 */
function tixUpdateTstmp($id){
global $cfg;

        // Generate the DB connection
        $db = dbConnect();

        // Build and execute the query.
        $update = '
            UPDATE
                ' . $cfg['db']['tbl']['tickets'] . '
            SET
                date_modified = NULL
            WHERE
                id = ' . addslashes($id);
        $rs = $db->Execute($update);

        // If there is a problem with the query, let's return a failure response.
        if(!$rs) return false;

// Return a success if everthing works as planned.
return true;
}
/*******************************************************************/
/**
 * generates and Error message based on what "errNum" is defined
 */
function genErrMsg($num){
global $cfg;
    switch($num){
        case 1:
            $error  = "\n\t" . 'The ticket you tried to submit was not in the proper format.';
            $error .= "\n\t" . 'A properly formatted (New) ticket should look as follows:';
            $error .= "\n\t" . '';
            $error .= "\n\t" . 'Priority:'  . "\t" . '[info|low|medium|high]';
            $error .= "\n\t" . 'Service:'   . "\t" .  '[trellix|portal|resmail|etc...]';
            $error .= "\n\t" . '';
            $error .= "\n\t" . 'A brief description of the problem you are seeing...';
            break;
        case 2:
            $error  = 'The ticket you are trying to update does not exist.  ';
            $error .= 'Please make sure you are referencing the proper ticket ';
            $error .= 'number.  If you believe this message is in error, please ';
            $error .= 'forward this message to ' . $cfg['admin'];
            $error  = indentStr(1,$error);
            break;
        default:
            $error  = 'General Error:  Something broke and I don\'t know how to handle it';
            break;
    }
return $error;
}
/*******************************************************************/
/**
 * This function will take a decoded MIME hash $struct and find the attachments
 * associate with it.  Once found, it will write them to the filesystem
 * based on $cfg['attach_dir']
 */
function handleAttachments($msg,$tktNum, $modNum=0) {
global $cfg;

    // We need to run the MIME decode against the message
    $params['include_bodies'] = true;
    $params['decode_bodies']  = true;
    $params['decode_headers'] = true;
    //$params['crlf']           = "\r\n";

    $decoder = new Mail_mimeDecode($msg);
    $struct = $decoder->decode($params);

    // Check to see if our attachment directory exists.. and die with an error if not.
    if(!file_exists($cfg['attach_dir'])) die('Error: ' . $cfg['attach_dir'] . ' doesn\'t exist.' . "\n");

    // Now let's assign $attachDir, so we can see if it exists, and if not, create it.
    $attachDir  = $cfg['attach_dir'] . '/' . $tktNum . '/' . $modNum;
    if(!file_exists($attachDir)){
        mkRecursDir($attachDir);
    }

    // Loop through the MIME decoded email ($struct)
    foreach ($struct->parts as $part) {
        if (isset($part->disposition) && $part->disposition == 'attachment') {
            // Check to see if the attachment is an attached email.  The default method of handling this by the PHP module is to create multiple child
            // objects from the elements contained in the email.  That is not what we want.  If an email is attached to the current email we want
            // to save it as an intact attachment, which is what this code here does.
            if ( count($part->parts) > 0 && preg_match('/message\/rfc822/', $part->headers['content-type'])) {

                $inMessage  = false;
                $inHeader   = true;
                $attachment = '';
                $lines      = split("\n", $msg);
                
                $boundary = $struct->headers['content-type'];
                $boundary = preg_replace('/.*boundary=(.*)/i', '$1', $boundary);
                $boundary = preg_replace('/"/', '', $boundary);
                foreach ($lines as $line ){
                    if (trim($line) == "--$boundary--") {
                        if (strlen($attachment)>0) {
                            $fname = $attachDir . '/' . $part->d_parameters['filename'];
                            echo 'Filename: ' . $fname . "\n";

                            if (strlen($attachment)>0 ) {
                                if ($fh = fopen($fname, 'w')) {
                                    if (!fwrite($fh, $attachment)) exit(1);
                                }
                                fclose($fh);
                            }
                        }
                        $inMessage = false;
                    }

                    if ($inMessage) {
                        if ($inHeader) {
                            if (strlen(trim($line))==0) {
                                $inHeader = false;
                            }
                        } else {
                            $attachment .= "$line\n";
                        }
                    }
                    if (trim($line) == "--$boundary") {
                        $inMessage = true;
                        $inHeader = true;
                        $attachment = '';
                    }
                }
            } else if (strlen($part->body)>0 && (strlen($part->body) < ($cfg['max_attch_size'] * (1024 * 1024)))) {

                // Set the filename path where we are writing.
                $fname = $attachDir . '/' . $part->d_parameters['filename'];
                echo 'Filename: ' . $fname . "\n";

                // Define the "body" of our attachment
                $data = $part->body;
                
                // Fix for crappy Windows computers that can't read simple UNIX line feeds.
                if ($part->ctype_primary == 'text') {
                    $data = str_replace("\n", "\r\n", $data);    
                }
                
                // Open the file and write the data to disk
                if ($fh = fopen($fname, 'w')) {
                    if(!fwrite($fh, $data)) exit(1);
                }
                fclose($fh);
            }
        }
    }
}
/*******************************************************************/
/**
 * Creates a directory with all of it's parent directories created if they do not exist.  Similar to the new File().mkdirs() command.
 *
 * @param $dir The name of the directory to be created.
*/

function mkRecursDir($dir) {
	$dirs = explode('/', $dir);
	$currentDir = '/';
	foreach ($dirs as $d) {
		$currentDir .= $d.'/';
		if (!file_exists($currentDir)) {
			mkdir($currentDir);
		}
	}
}
/*******************************************************************/
?>
