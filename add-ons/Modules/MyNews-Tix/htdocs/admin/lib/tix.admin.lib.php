<?


/*******************************************************************/
function tixAdmin(){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $baseTix_URI    = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : Tickets : Select Tool';
    $output = loginInfo();
    $output.= "\n\t" . '<p>';
    $output.= <<<HTML

    <ul>
        <li><a href="{$baseTix_URI}?mode=groupList">Modify Group Memberships</a>
        <li><a href="{$baseTix_URI}?mode=aclList">Modify/Delete ACLs</a>
    </ul>
    <ul>
        <li><a href="{$baseTix_URI}?mode=pscpList&view=project">Modify/Delete Projects</a>
        <li><a href="{$baseTix_URI}?mode=pscpList&view=user_group">Modify/Delete Groups</a>
        <li><a href="{$baseTix_URI}?mode=pscpList&view=status">Modify/Delete Statuses</a>
        <li><a href="{$baseTix_URI}?mode=pscpList&view=category">Modify/Delete Categories</a>
        <li><a href="{$baseTix_URI}?mode=pscpList&view=priority">Modify/Delete Priorities</a>
    </ul>
HTML;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixListPSCP($view){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    $viewHash  = tixBuildOptionList($myNewsModule['db']['tbl']['tix'][$view]);

    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Quick Edit : <small>' . $view . '</small>';
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form action="' . $tixAdmin_URI . '?mode=pscpMod" method="post">';
    $output.= "\n\t\t" . '<input type="hidden" name="view" value="' . $view . '">';
    $output.= "\n\t" . '<table border=0 cellpadding="4" cellspacing="0">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Edit:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Name:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Delete:</b></td>';
    $output.= "\n\t\t" . '</tr>';

    $viewKeys   = array_keys($viewHash);
    foreach($viewKeys as $viewKey){
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td valign="top" align="right">';
        $output.= "\n\t\t\t\t" . '<a href="' . $tixAdmin_URI . '?mode=pscpEdit&view=' . $view . '&id=' . $viewKey . '">[' . $viewKey . ']</a></small>';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">';
        $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="mod[' . $viewKey . ']" value="' . $viewHash[$viewKey] . '">';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top" align="left">';
        $output.= "\n\t\t\t\t" . '<input class="textbox" type="checkbox" name="del[' . $viewKey . ']">';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
    }
    
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top" align="right"><small>New:</small></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="add">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n\t\t\t" . '<td>&nbsp</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixModPSCP($table){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Here we generate a hash of objects currently in the database
    // for comparison.
    $compHash   = tixBuildOptionList($myNewsModule['db']['tbl']['tix'][$table]);

    // Connect to the database.
    mynews_connect();

    // If there is a new entry, we need to insert it into the database.
    if($_POST['add']){
        $query  = '
            INSERT into
                ' . $myNewsModule['db']['tbl']['tix'][$table] . "
                   (id,name)
            VALUES (NULL,'" . addslashes($_POST['add']) . "')";
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        $modTxt = '<b>' . $_POST['add'] . ':</b> has been added to the <i>' . $table . '</i> table.' . "\n";
    }

    // If anything has the deleted box checked, we need to delete it.
    if($_POST['del']){
        // Turn the id's to delete into a comma separated list.
        $delKeys= implode(',', array_keys($_POST['del']));
        $query  = '
            DELETE
            FROM
                ' . $myNewsModule['db']['tbl']['tix'][$table] . '
            WHERE
                id IN(' . $delKeys . ')';
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        // We need to build our modification text.
        $notKeys= array_keys($_POST['del']);
        foreach($notKeys as $notKey){
            $modTxt.= '<b>' . $compHash[$notKey] . ':</b> has been removed from the <i>' . $table . '</i> table.' . "\n";
        }

        // If $table is set to user_group, we need to empty out the
        // group memberships from the groups table.
        if($table == 'user_group'){
            $delete = '
                DELETE
                FROM
                    ' . $myNewsModule['db']['tbl']['tix']['groups'] . '
                WHERE
                    gid IN(' . $delKeys . ')';
            $result = mysql_query($delete);
            $sqlErr = myNewsChkSqlErr($result, $delete);

            $errorArray['error'] = $sqlErr;
            if($sqlErr) return $errorArray;
        }
    }

    // Here we compare what is currently in the database, and what
    // is submitted, and set $modified to true.  We also build a hash
    // of the records we want to edit so we can build our queries
    // when the time comes.
    $modKeys    = array_keys($_POST['mod']);
    foreach($modKeys as $modKey){
        if($_POST['mod'][$modKey] != $compHash[$modKey]){
            $modified = true;
            $modHash[$modKey] = $_POST['mod'][$modKey];
        }
    }

    // If $modified is set to true, we take each modification out of
    // $modHash and update it's record in the database.
    if($modified){
        $modKeys= array_keys($modHash);
        foreach($modKeys as $modKey){
            $query  = '
                UPDATE
                    ' . $myNewsModule['db']['tbl']['tix'][$table] . "
                SET
                    name = '" . $modHash[$modKey] . "'
                WHERE
                    id = " . $modKey;
            $result = mysql_query($query);
            $sqlErr = myNewsChkSqlErr($result, $query);

            $errorArray['error'] = $sqlErr;
            if($sqlErr) return $errorArray;

            $modTxt.= '<b>' . $compHash[$modKey] . '</b> has been renamed to: <i>' . $modHash[$modKey] . '</i>' . "\n";
        }
    }

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $tixAdmin_URI . '?mode=pscpList&view=' . $table . '">';

    // Build the page/content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Modified : <small>' . $table . '</small>';

    // Build the return output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . nl2br($modTxt);
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixEditPSCP($id,$table){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];
    
    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            *
        FROM
            ' . $myNewsModule['db']['tbl']['tix'][$table] . '
        WHERE
            id = ' . $id;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Turn our returned record into a hash
    $record = mysql_fetch_assoc($result);

    // Build the page/content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Edit : <small>' . $table . ' : ' . ' (' . $id . ') ' . $record['name'] . '</small>';

    // Build the return output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form action="' . $tixAdmin_URI . '?mode=pscpEdited" method="post">';
    $output.= "\n\t\t" . '<input type="hidden" name="id" value="' . $id . '">';
    $output.= "\n\t\t" . '<input type="hidden" name="view" value="' . $table . '">';
    $output.= "\n\t" . '<table border=0>';

    // start a counter
    $i  = 0;
    // get the hash keys out of our record hash.
    $tmpKeys= array_keys($record);
    foreach($tmpKeys as $tmpKey){
        if($tmpKey != 'id'){
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>' . ucfirst($tmpKey) . ':</b></td>';
            $output.= "\n\t\t\t" . '<td>';
            $ftype  = mysql_field_type($result, $i);
            switch($ftype){
                case 'string':
                    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="mod[' . $tmpKey . ']" value="' . $record[$tmpKey] . '">';
                    break;
                case 'blob':
                        $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="mod[' . $tmpKey . ']" cols="30" rows="5">' . $record[$tmpKey] . '</textarea>';
                    break;
            }
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t" . '</tr>';
        }
        $i++;
    }

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
function tixEditedPSCP($id,$table){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Now we need to build the edit list, based on what fields were submitted.
    $modKeys= array_keys($_POST['mod']);
    $count  = count($modKeys);
    $i = 1;
    foreach($modKeys as $modKey){
        unset($extra);
        if($i < $count) $extra = ',' . "\n";
        $update.= $modKey . " = '" . addslashes($_POST['mod'][$modKey]) . "'" . $extra ;
        $i++;
    }

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        UPDATE
            ' . $myNewsModule['db']['tbl']['tix'][$table] . '
        SET
            ' . $update . '
        WHERE
            id = ' . addslashes($id);
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $tixAdmin_URI . '?mode=pscpList&view=' . $table . '">';

    // Build the page/content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Edited : <small>' . $table . ' : ' . ' (' . $id . ')</small>';

    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . '<b>' . $_POST['mod']['name'] . '</b> <small>(' . $id . ')</small> updated.'; 
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixListGroup(){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            *
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['user_group'];
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript= <<<HTML
        <script language="JavaScript">
        <!--
        function confirmDelete(ID,TITLE) {

            confirm = confirm("Do you really want to delete the group:\\n\\n" + TITLE);
            if (confirm == true) {
                window.location="{$tixAdmin_URI}?mode=groupDelete&id=" +ID;
            } else {
                window.location="{$tixAdmin_URI}?mode=groupList";
            }
        }
        -->
        </script>
HTML;

    // Build the page/content title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : <small>Groups</small>';

    // Build the return content.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td width="10%" valign="top"><b>Group:</b></td>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Members:</b></td>';
    $output.= "\n\t\t" . '</tr>';

    // Now we loop through the records returned, and output data
    // base on what we got from the database.
    while($record = mysql_fetch_assoc($result)){
        extract($record);
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td valign="top">';
        $output.= '<a href="' . $tixAdmin_URI . '?mode=groupEdit&id=' . $id . '&name=' . urlencode($name) . '">' . $name . '</a>:';
        $output.= '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">';

        // Here we get the the list of users that exist in each group.
        $uHash  = tixGetUserList($id,true,false);
        if($uHash){
            $uKeys  = array_keys($uHash);
            foreach($uKeys as $uKey){
                $output.= "\n\t\t\t\t" . '<small>' . $uHash[$uKey] . '</small><br />';
            }
        }

        $output.= "\n\t\t\t\t" . '&nbsp;';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
    }

    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixEditGroup($id,$name){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    $userHash   = tixGetUserList($id,false,false);
    $compHash   = tixGetUserList($id,true,false);
    
    // Build the content/page title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Groups : Edit : <small>' . $name . ' (' . $id . ')</small>';

    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form action="' . $tixAdmin_URI . '?mode=groupEdited" method="post">';
    $output.= "\n\t\t" . '<input type="hidden" name="id" value="' . $id . '">';
    $output.= "\n\t" . '<table border=0>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Users:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name="users[]" size="10" multiple>';

    $userKeys   = array_keys($userHash);
    foreach($userKeys as $userKey){
        unset($selected);
        if($userHash[$userKey] == $compHash[$userKey]) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $userKey . '"' . $selected . '>' . $userHash[$userKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>'. $myNewsConf['button']['submit'] . '</td>';
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
function tixEditedGroup($id){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Connect to the database.
    mynews_connect();

    // We need to remove all the users from our selected group so we
    // can re-add the newly selected users.
    $delete = '
        DELETE
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['groups'] . '
        WHERE
            gid = ' . addslashes($id);
    $result = mysql_query($delete);
    $sqlErr = myNewsChkSqlErr($result, $delete);
    
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Now we go through the submitted userlist and add each selected
    // user (if there are any).
    if($_POST['users']){
        foreach($_POST['users'] as $user){
            $insert = '
                INSERT into
                    ' . $myNewsModule['db']['tbl']['tix']['groups'] . "
                values(
                    NULL,
                    '" . addslashes($user)  . "',
                    '" . addslashes($id)    . "')";
            $result = mysql_query($insert);
            $sqlErr = myNewsChkSqlErr($result, $insert);

            $errorArray['error'] = $sqlErr;
            if($sqlErr) return $errorArray;
        }
    }

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $tixAdmin_URI . '?mode=groupList">';

    // Build the content/page title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : Groups : Edited : <small>(' . $id . ')</small>';

    // Build our return content.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'Group members update for group: <small>' . $id . '</small>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixListACL(){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Connect to the database.
    mynews_connect();

    // build and execute the query.
    $query  = '
        SELECT
            a.*,
            b.name as nm_project,
            if(a.acl_type=\'group\',d.name,c.name) as name
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['acl'] . ' as a
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['project'] . ' as b
        ON
            a.project = b.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as c
        ON
            a.id_tag = c.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['user_group'] . ' as d
        ON
            a.id_tag = d.id
        WHERE
            a.id_tag != 0
        ORDER by a.id';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript= <<<HTML
        <script language="JavaScript">
        <!--
        function confirmDelete(ID,TITLE) {
            confirm = confirm("Do you really want to delete the group:\\n\\n" + TITLE);
            if (confirm == true) {
                window.location="{$tixAdmin_URI}?mode=aclDelete&id=" +ID;
            } else {
                window.location="{$tixAdmin_URI}?mode=aclList";
            }
        }

        function isOK(){
            with(document.the_form){
                if(project.value == '-1'){
                    alert("Error: You must enter a Project.");
                    return false;
                }

                if(acl_type[0].checked == '1' && group.value == '-1' ){
                    alert("Error: You must select a group.");
                    return false;
                }
                if(acl_type[1].checked == '1' && user.value == '-1' ){
                    alert("Error: You must select a user.");
                    return false;
                }
            }


            return true;
        }
        -->
        </script>
HTML;

    // Build the content/page title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : ACLs';

    // Build our return content.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';

    $output.= "\n\t\t" . '<u>Add ACL</u>';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t" . '<table border=0>';
    $output.= "\n\t" . '<form action="' . $tixAdmin_URI . '?mode=aclAdd" method="post" name="the_form" onSubmit="return isOK()">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td colspan="2">';
    $output.= "\n\t\t\t\t" . '<select name="project">';
    $output.= "\n\t\t\t\t\t" . '<option value="-1">Select Project</option>';

    // Build the projects list.
    $tmpHash= tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project']);
    $tmpKeys= array_keys($tmpHash);
    foreach($tmpKeys as $tmpKey){
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '">' . $tmpHash[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>ACL Type:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="acl_type" value="group" checked> Group';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="acl_type" value="user"> User';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name="group">';
    $output.= "\n\t\t\t\t\t" . '<option value="-1">Select Group</option>';

    // Build the groups list.
    $tmpHash= tixBuildOptionList($myNewsModule['db']['tbl']['tix']['user_group']);
    $tmpKeys= array_keys($tmpHash);
    foreach($tmpKeys as $tmpKey){
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '">' . $tmpHash[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name="user">';
    $output.= "\n\t\t\t\t\t" . '<option value="-1">Select User</option>';

    // Build the users list.
    $tmpHash= tixGetUserList(NULL,false,false);
    $tmpKeys= array_keys($tmpHash);
    foreach($tmpKeys as $tmpKey){
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '">' . $tmpHash[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="3">&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t\t" . '</blockquote>';

    // Output existing ACLs
    $output.= "\n\t\t" . '<u>Existing ACLs</u>';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t" . '<table border=0 width="50%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>User/Group:</b></td>';
    $output.= "\n\t\t" . '</tr>';


    while($record = mysql_fetch_assoc($result)){
        extract($record);
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td><a href="javascript:confirmDelete(\'' . $id . '\',\'' . $nm_project . '\')">Delete</a></td>';
        $output.= "\n\t\t\t" . '<td>' . $nm_project . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $name . ' <small>(' . $acl_type . ')</small></td>';
        $output.= "\n\t\t" . '</tr>';
    }

    $output.= "\n\t" . '</table>';
    $output.= "\n\t\t" . '</blockquote>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixAddACL(){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // We need to see what acl_type we have, and set $id_tag
    // appropriately.
    switch($_POST['acl_type']){
        case 'group':
            $id_tag = $_POST['group'];
            break;
        case 'user':
            $id_tag = $_POST['user'];
            break;
    }

    // Connect to the database.
    mynews_connect();

    // Build and execute the insert query.
    $insert = '
        INSERT into
            ' . $myNewsModule['db']['tbl']['tix']['acl'] . "
        VALUES(
            NULL,
            '" . addslashes($_POST['acl_type']) . "',
            '" . addslashes($id_tag)            . "',
            '7',
            '" . addslashes($_POST['project'])  . "')";
    $result = mysql_query($insert);
    $sqlErr = myNewsChkSqlErr($result, $insert);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Get the id of the newly inserted ACL.
    $id     = mysql_insert_id();

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $tixAdmin_URI . '?mode=aclList">';

    // Build the content/page title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : ACLs : Added : <small>(' . $id . ')</small>';

    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'Inserted the ACL with id: <small>(' . $id  . ')</small> into the database.';
    $output.= "\n\t" . '</pre>';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixDeleteACL($id){
global $myNewsConf;
global $myNewsModule;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];
    $tixAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsModule['adminScripts']['tix'];

    // Connect to the database.
    mynews_connect();

    // Build and execute the delete query.
    $delete = '
        DELETE
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['acl'] . '
        WHERE
            id = ' . addslashes($id);
    $result = mysql_query($delete);
    $sqlErr = myNewsChkSqlErr($result, $delete);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $tixAdmin_URI . '?mode=aclList">';

    // Build the content/page title.
    $title  = '<a href="' . $baseAdmin_URI . '">Admin</a> : <a href="' . $tixAdmin_URI . '">Tickets</a> : ACLs : Deleted : <small>(' . $id . ')</small>';

    // Build the return Content.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'Deleted the ACL with id: <small>(' . $id . ')</small>';
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
?>
