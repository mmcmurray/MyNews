<?
/* $Id: authors.lib.php 514 2005-09-29 20:12:09Z alien $ */

/*******************************************************************/
function authAdmin(){
/**
 * This function outputs the authors administration navigation.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : Users : Select Tool';
    $output = loginInfo();
    $output.= <<<EOT

    <ul>
        <li><a href="{$baseAuthAdmin_URI}?mode=authAdd">New User</a></li>
        <li><a href="{$baseAuthAdmin_URI}?mode=authList">Modify/Delete User</a></li>
    </ul>

EOT;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function authAdd(){
/**
 * This function outputs a form for an admin to create a new user.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Create the javascript for required fields.
    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
          with(document.the_form){
            if(name.value == ''){
              alert("Name is a required field.");
              return false;
            }
            if(user.value == ''){
              alert("Username is a required field.");
              return false;
            }
            if(pass.value == '' ){
              alert("Password is a required field.");
              return false;
            }
          }
          return true;
        }

        //-->
        </script>
EOT;

    // Set the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : New';

    // Generate the main content.
    $output = loginInfo();
    $output.= <<<EOT
        <blockquote>
        <form action="{$baseAuthAdmin_URI}?mode=authAdded" method="post" name="the_form" onSubmit="return isOK()">
        <table border=0 width="95%">
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>1)</b> Name:</td>
                <td>
                    <input class="textbox" type="text" name="name" size="{$tWidth}">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>2)</b> URL:</td>
                <td>
                    <input class="textbox" type="text" name="url" size="{$tWidth}">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>3)</b> Email:</td>
                <td>
                    <input class="textbox" type="text" name="email" size="{$tWidth}">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>4)</b> Status:</td>
                <td>
                    <select class="textbox" name="group">
                        <option value="Admin">Admin</option>
                        <option value="Editor">Editor</option>
                        <option value="Author" selected>Author</option>
                        <option value="Guest" selected>Guest</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>5)</b> Username:</td>
                <td>
                    <input class="textbox" type="text" name="user">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left" width="10%" nowrap><b>6)</b> Password:</td>
                <td>
                    <input class="textbox" type="password" name="pass">
                    <input type="hidden" name="active" value="yes">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left"><b>&nbsp;</td>
                <td>{$myNewsConf['button']['submit']}</td>
            </tr>
        </table>
        </form>
        </blockquote>
EOT;

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function authAdded(){
/**
 * This function takes the data from the form submitted by authAdd()
 * and inserts it into the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    $cryptPass  = md5(stripslashes($_POST['pass']));

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
            INSERT into
                ' . $myNewsConf['db']['table']['authors'] . "
            values( '',
                    '" . addslashes($_POST['name'])     . "',
                    '',
                    '" . addslashes($_POST['group'])    . "',
                    '" . addslashes($_POST['email'])    . "',
                    '" . addslashes($_POST['url'])      . "',
                    '" . date('Y-m-d H:i:s')            . "',
                    '" . addslashes($_POST['active'])   . "',
                    '" . addslashes($_POST['user'])     . "',
                    '" . $cryptPass                     . "')";

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh
    $meta   = '<meta http-equiv="Refresh" content="2; URL=' . $baseAuthAdmin_URI . '?mode=authList">';

    // Build the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : Added : <small>' . $_POST['name'] . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The following user was added to the database';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t\t" . '<table border=0>';
    $output.= "\n\t\t\t" . '<tr>';
    $output.= "\n\t\t\t\t" . '<td><b>Name:</b></td>';
    $output.= "\n\t\t\t\t" . '<td>' . $_POST['name'] . '</td>';
    $output.= "\n\t\t\t" . '</tr>';
    $output.= "\n\t\t\t" . '<tr>';
    $output.= "\n\t\t\t\t" . '<td><b>Status:</b></td>';
    $output.= "\n\t\t\t\t" . '<td>' . $_POST['group'] . '</td>';
    $output.= "\n\t\t\t" . '</tr>';
    $output.= "\n\t\t\t" . '<tr>';
    $output.= "\n\t\t\t\t" . '<td><b>User:</b></td>';
    $output.= "\n\t\t\t\t" . '<td>' . $_POST['user'] . '</td>';
    $output.= "\n\t\t\t" . '</tr>';
    $output.= "\n\t\t\t" . '<tr>';
    $output.= "\n\t\t\t\t" . '<td><b>Pass:</b></td>';
    $output.= "\n\t\t\t\t" . '<td>' . $_POST['pass'] . '</td>';
    $output.= "\n\t\t\t" . '</tr>';
    $output.= "\n\t\t" . '</table>';
    $output.= "\n\t\t" . '</blockquote>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function authList(){
/**
 * This function outputs a list of authors with options to edit or 
 * delete their accounts.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;
    
    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            uid,
            name,
            user,
            status
        FROM
            ' . $myNewsConf['db']['table']['authors'] . '
        ORDER by
            user';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // If there is a problem with the query, return with the error.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function confirmDelete(URL,USER){
             where_to= confirm("Do you really want to delete the user:\\n\\n" + USER); 
            if (where_to== true) { 
                window.location=URL;
            } else {  
                window.location="{$baseAuthAdmin_URI}?mode=authList"; 
            }
        }

        //-->
        </script>
EOT;

    // Build the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : Modify';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Name:</b></td>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Username:</b></td>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Status:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    
    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td width="10%" nowrap>';
        $output.= "\n\t\t\t" . '(<a href="' . $baseAuthAdmin_URI . '?mode=authEdit&uid=' . $uid . '&user=' . $user . '">Edit</a>';
        $output.= "\n\t\t\t" . ' | ';
        $output.= "\n\t\t\t" . '<a href="javascript:confirmDelete(\'' . $baseAuthAdmin_URI . '?mode=authDeleted&uid=' . $uid . '&user=' . $user ."','" . $user . '\')">Delete</a>)';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $name . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $user . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $status . '</td>';
        $output.= "\n\t\t" . '<tr>';
    }

    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function authDeleted(){
/**
 * This function deletes a specified user from the authors table
 * in the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    // Build and execute the query.
    $query  = '
        DELETE
        FROM
            ' . $myNewsConf['db']['table']['authors'] . '
        WHERE
            uid = ' . addslashes($_GET['uid']);

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // If there is a problem with the query, return with the error.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh
    $meta   = '<meta http-equiv="Refresh" content="2; URL=' . $baseAuthAdmin_URI . '?mode=authList">';
    
    // Build the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : <a href="' . $baseNewsAdmin_URI . '?mode=authList">Modify</a> : Deleted : <small>' . $_GET['user'] . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . ' <b>' . $_GET['user'] . '</b> has been deleted.';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;
    
return $returnArray;
}
/*******************************************************************/
function authEdit(){
/**
 * This function retrieves data about the selected user from the authors
 * table and populates a form so the Site Admin can edit a user's
 * information.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            uid,
            name,
            status,
            active,
            user,
            password
        FROM
            ' . $myNewsConf['db']['table']['authors'] . '
        WHERE
            uid = ' . addslashes($_GET['uid']);
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // If there is a problem with the query, return with the error.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
     extract($row);
    
    // Build the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : <a href="' . $baseNewsAdmin_URI . '?mode=authList">Modify</a> : Edit : <small>' . $_GET['user'] . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form method="post" action="' . $baseAuthAdmin_URI . '?mode=authEdited&uid=' . $uid . '">';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>1)</b> Name:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="name" value="' . $name . '" size="' . $tWidth . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>2)</b> Status:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name="status">';

    // Loop through the group array so we can build our select list.
    $groupArray = array('Admin','Editor','Author','Guest');
    foreach($groupArray as $group){
        $selected = '';
        if($group == $status) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $group . '"' . $selected . '>' . $group . '</a>';
    }

    // Continue content output.
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>3)</b> Active:</td>';
    $output.= "\n\t\t\t" . '<td>';

    // We need to determine which radio button to set as "checked"
    if(strtolower($active) == 'no') $nCheck = ' checked';
    if(strtolower($active) == 'yes') $yCheck = ' checked';
    
    $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="yes"' . $yCheck . '> Yes';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="no"' . $nCheck . '> No';

    // Continue content output.
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>4)</b> User:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="user" value="' . $user . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap><b>5)</b> Password:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="password" name="newpass">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="password" value="' . $password . '">';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<b>*</b><small>Only Fill out the password field if you wish to change the password.';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;Leave empty to keep the same password.';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function authEdited(){
/**
 * This function takes the data posted from authEdit() and inserts it
 * into the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Here we see if the Password box was filled out, and if so, 
    // Set $password to the new password defined.
    if(!isset($_POST['newpass']) || empty($_POST['newpass'])){
        $password   = $_POST['password'];
    } else {
        $password   = md5(stripslashes($_POST['newpass']));
    }

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        UPDATE
            ' . $myNewsConf['db']['table']['authors']   . "
        SET
            name    = '" . addslashes($_POST['name'])   . "',
            status  = '" . addslashes($_POST['status']) . "',
            active  = '" . addslashes($_POST['active']) . "',
            user    = '" . addslashes($_POST['user'])   . "',
            password= '" . $password                    . "'
        WHERE
            uid   = " . $_GET['uid'];

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // If there is a problem with the query, return with the error.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh
    $meta   = '<meta http-equiv="Refresh" content="2; URL=' . $baseAuthAdmin_URI . '?mode=authEdit&uid=' . $_GET['uid'] . '&user=' . $_POST['user'] . '">';
    
    // Build the title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseAuthAdmin_URI . '">Users</a> : <a href="' . $baseNewsAdmin_URI . '?mode=authList">Modify</a> : Edited : <small>' . $_POST['user'] . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . '<b>' . $_POST['user'] . '</b> <small>(' . $_GET['uid'] . ')</small> has been been updated.';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
?>
