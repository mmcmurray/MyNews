<?
/* $Id: prefs.lib.php 495 2005-09-16 14:12:09Z alien $ */

/*******************************************************************/
function getPrefs() {
/**
 * The purpose of this function is to output the preferences form
 * for the user to input their data, change password, etc...
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $basePrefsAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['preferences'];
    $submit             = $myNewsConf['button']['submit'];

    // Connect to the database.
    mynews_connect();

    // Build our query
    $query  = ('
        SELECT
            artnr as uid,
            name,
            bio,
            email,
            url,
            date,
            active,
            user,
            password as oldpass
        FROM
            ' . $myNewsConf['db']['table']['authors'] . '   
        WHERE
            user="' . $_SESSION['valid_user'] . '"');

    $result = mysql_query($query);

    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
            with(document.the_form){
                if( email.value == '' ){
                    alert('Email Address required.');
                    return false;
                }
            }
            return true;
        }

        //-->
        </script>
EOT;

    $output.= myNewsChkSqlErr($result,$query);

    $row  =  mysql_fetch_assoc($result);
     extract($row);

    $output.= loginInfo();

    $output.= <<<EOT

    <blockquote>
    <form action="{$basePrefsAdmin_URI}?mode=edit&uid={$uid}" method="post" name="the_form" onSubmit="return isOK()">
    <table width="85%" CELLPADDING=2 CELLSPACING=2 BORDER=0>
        <tr>
            <td align="right" valign="top">
                <b>Name:</b>
            </td>
            <td valign="top">
                <input class="textbox" type="text" name="name" size="40" value="$name">
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <b>Bio:</b>
                  <br>
                <i>(Optional)</i>
            </td>
            <td valign="top">
                <textarea name="bio" cols=50 rows=10 wrap=virtual>$bio</textarea>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <b>URL:</b>
            </td>
            <td valign="top">
                <input class="textbox" type="text" name="url" size="40" value="$url">
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <b>Email:</b>
            </td>
            <td valign="top">
                <input class="textbox" type="text" name="email" value="$email" size="40">
            </td>
        </tr>

        <tr>
            <td>
                &nbsp;
            </td>
            <td>
                <p>
                <small>
                Enter Password twice to change it.
                <br>
                Leave the next two fields blank to keep your current password.
                </small>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <b>Password:</b>
                <br>
            </td>
            <td valign="top">
                <input class="textbox" type="password" name="newpass">
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <small><i>Confirm:</i></small>
            </td>
            <td valign="top">
                <input class="textbox" type="password" name="confirmpass">
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="oldpass" value="{$oldpass}">
                <input type="hidden" name="active" value="Yes">
                <input type="hidden" name="date" value="{$date}">
                <input type="hidden" name="user" value="{$_SESSION['valid_user']}">
                <input type="hidden" name="status" value="{$_SESSION['status']}">
            </td>
            <td>
                $submit
            </td>
        </tr>
        </table>
    </form>

EOT;

    // Here we create the hash we're going to return.
    $return['jscript']  = $jscript;
    $return['title']    = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : Preferences : ' . $_SESSION['valid_user'];
    $return['content']  = $output;

return $return;
}
/*******************************************************************/
function changePrefs(){
/**
 * The purpose of this function is to take the data submitted from
 * getPrefs() and stuff it into the database.
 */
global $myNewsConf;

    $newpass    = $_POST['newpass'];
    $confpass   = $_POST['confirmpass'];
    $oldpass    = $_POST['oldpass'];

    if(!isset($_GET['uid'])){
        $title = 'Error';
        $output = myNewsError(102,'You passed an empty POST hash');
    } elseif($_POST['newpass'] != $_POST['confirmpass']){
        // Check to see if newpass and confirmpass match.
        // if not, throw an error.
        $title  = 'Error :';
        $output = myNewsError(102,'Passwords do not match');
    } else {
        // if $newpass is empty, our password doen't need to be
        // changed.  If it's not empty, go ahead and change it.
        if(empty($newpass)){
            $pass = $_POST['oldpass'];
        } else {
            $pass = md5(stripslashes($_POST['newpass']));
        }

        // Set our title.
        $title  = 'Updated :: ' . $_POST['user'];

        // Connect to the database.
        mynews_connect();

        // Insert our data into the database, and have a nice day
        $insert = ('
            REPLACE INTO
                ' . $myNewsConf['db']['table']['authors'] . '
            values(
                "' . $_GET['uid']                   . '",
                "' . addslashes($_POST['name'])     . '",
                "' . addslashes($_POST['bio'])      . '",
                "' . addslashes($_POST['status'])   . '",
                "' . addslashes($_POST['email'])    . '",
                "' . addslashes($_POST['url'])      . '",
                "' . addslashes($_POST['date'])     . '",
                "' . addslashes($_POST['active'])   . '",
                "' . addslashes($_POST['user'])     . '",
                "' . $pass                          . '")');

        $result = mysql_query($insert);
        $output = myNewsChkSqlErr($result,$insert);
        $output.= <<<EOT

        <meta http-equiv="Refresh" content="2; URL={$myNewsConf['path']['web']['admin']}{$myNewsConf['adminScripts']['preferences']}">
        <blockquote>
            <small>
                {$_POST['name']} has been successfully updated.
            </small>
        </blockquote>

EOT;
    }

    // Create our return hash.
    $return['title']    = $title;
    $return['content']  = $output;
return $return;
}
?>
