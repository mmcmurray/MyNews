<?php
/* $Id: sub.lib.php 516 2005-10-08 16:02:47Z alien $ */
/********************************************************************/
function subAdd() {
/**
 * This fuction prints out the html form for submitting a story item.
 *
 * Ex:  subAdd();
 */
global $myNewsConf;

    $baseSubmit_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['submit'];
    $now            = date($myNewsConf['format']['date']['nice']);

    $output = <<<HTML

    <blockquote>
    <p>
    Please write your article/story filling the following form and double
    check your submission.  Be advised: not all submissions will be
    posted.  Your submission will be checked for proper grammar and maybe
    edited by our staff.

    <form action="$baseSubmit_URI?mode=added" method="post">
    <input type="hidden" name="section" value="Contributed">
    <b>Title:</b>
    <br>
    <input class="textbox" type="text" name="title" size="{$myNewsConf['form']['text']['width']}">
    <br>
    <br>
    <b>Article Text:</b>
    <br>
    <TEXTAREA class="textbox" NAME="text" COLS="{$myNewsConf['form']['textarea']['cols']}" ROWS="{$myNewsConf['form']['textarea']['rows']}" WRAP="virtual"></TEXTAREA>
    <br>
    <br>
    <b>Your Name:</b>
    <br>
    <input class="textbox" type="text" name="author" size="{$myNewsConf['form']['text']['width']}" value="Your Name Here." OnFocus="if(this.value && this.value == 'Your Name Here.') { this.value = ''; this.form.email.value = '' } else { this.select(); }">
    <br>
    <br>
    <b>Your Email:</b>
    <br>
    <input class="textbox" type="text" name="email" size="{$myNewsConf['form']['text']['width']}" value="Your Email Address Here." OnFocus="if(this.value && this.value == 'Your Email Address Here.') { this.value = ''; this.form.author.value = '' } else { this.select(); }">
    <br>
    <br>
    {$myNewsConf['button']['submit']}
    </form>
    </blockquote>

HTML;

    $returnArray['title']   = 'Submit a Story';
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
/********************************************************************/
function subAdded() {
/**
 * This fuction processes the html form and inserts the submission
 * into the database for review.
 *
 * Ex:  subAdded();
 */
global $myNewsConf;

    $baseSubmit_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['submit'];

     extract($_POST);

    if (!$author || !$email || !$text || !$title) {
        $errorArray['error'] = myNewsError(102,'One or more required fields are missing.');
    } 
    elseif (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$email)) {
        $errorArray['error'] = myNewsError(102,'A Valid email address is required.');
    }

    // Return with the error if $errorArray exists.
    if($errorArray) return $errorArray;

    // for security reason we want to add slashes to all of our
    // our submitted values.

    mynews_connect();

    $query  = "insert into " . $myNewsConf['db']['table']['submissions'] . " values(
        '',
        '" . addslashes($_POST['title'])    . "',
        '" . addslashes($_POST['text'])     . "',
        '',
        '" . addslashes($_POST['author'])   . "',
        '" . addslashes($_POST['email'])    . "',
        '" . date('Y-m-d H:i:s')            . "',
        '" . addslashes($_POST['section'])  . "')";

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $subID  = mysql_insert_id();

    $subAdmin_URI   = $myNewsConf['default']['siteurl'] . $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
    $tmplHash   = array(
        '%SITENAME%'    => $myNewsConf['default']['sitename'],
        '%SITEURL%'     => $subAdmin_URI . '?mode=subEdit&sid=' . $subID);

    $subj   = 'New Submission';
    $body   = parseEmailTemplate($myNewsConf['email']['template']['submit'], $tmplHash);
    $rcpt   = $myNewsConf['admin']['mail']['site'];

    if($rcpt && $subj && $body){
        emailNotify($body,$subj,$rcpt);
    }

    /*
     * Tidey up the copy text so we can present it back to the user.
     */
    $_POST['text']  = format_output($_POST['text']);

    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . '<b>' . $_POST['title'] . '</b> has been entered with Submission ID=' . $subID;
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . 'Posted by: <u>' . $_POST['author'] . '</u><br />';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t\t\t" . '<p>';
    $output.= "\n\t\t\t" . $_POST['text'];
    $output.= "\n\t\t" . '</blockquote>';
    $output.= "\n\t" . '</blockquote>';

    $title  = 'Thank you for your submission';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
?>
