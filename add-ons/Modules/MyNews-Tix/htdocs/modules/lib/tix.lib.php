<?
/*******************************************************************/
function tixList($view,$project,$sort) {
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    // If the user doesn't belong to any groups we need to set an empty
    // group for them.
    if(!$_SESSION['tix']['user_projects']){
        $projs = 0;
    } else {
        $projs  = implode(',', $_SESSION['tix']['user_projects']);
    }

    // If show isn't passed, let's set it.
    if(!$_GET['show']) $_GET['show'] = 0;

    // Here we define $where based on $view
    switch($view){
        case 'my.open':
            $where  = 'WHERE (b.owner = ' . $_SESSION['uid'] . ') AND (b.status NOT IN(9,7))';
            break;
        case 'my.submit':
            $where  = 'WHERE (b.creator = ' . $_SESSION['uid'] . ') AND (b.status NOT IN(9,7))';
            break;
        case 'my.closed':
            $where  = 'WHERE (b.creator = ' . $_SESSION['uid'] . ') AND (b.status IN (9,7))';
            break;
        case 'unowned':
            $where  = 'WHERE (b.owner = 0) AND (b.status NOT IN(9,7) AND b.project IN(' . $projs . '))';
            break;
        case 'group.open':
            $where  = 'WHERE b.project IN(' . $projs . ') AND (b.status NOT IN(9,7))';
            break;
        case 'group.closed':
            $where  = 'WHERE b.project IN(' . $projs . ') AND (b.status IN(7,9))';
            break;
        case 'all':
            $where  = 'WHERE (1)';
            break;
    }

    // Here we define the extra $where based on whether we have a project
    // Selected or not.
    if(!empty($project) || $project){
        $where .= ' AND project = ' . addslashes($project);
    }

    // Here we define $order based on $sort
    switch($sort){
        case 'id':
            $order  = 'ORDER by id';
            break;
        case 'priority':
            $order  = 'ORDER by priority desc';
            break;
        case 'status':
            $order  = 'ORDER by b.status desc';
            break;
        case 'owner':
            $order  = 'ORDER by owner';
            break;
        case 'creator':
            $order  = 'ORDER by creator';
            break;
        case 'project':
            $order  = 'ORDER by project';
            break;
        case 'modify':
            $order  = 'ORDER by date_modified desc';
            break;
        case 'added':
            $order  = 'ORDER by date_added desc';
            break;
        case 'age':
            $order  = 'ORDER by age';
            break;
        default:
            $sort   = 'age';
            $order  = 'ORDER by age';
            break;
    }

    // Connect to the database.
    mynews_connect();

    // build and execute our query.
    $query  = '
        SELECT
            (unix_timestamp(NOW()) - UNIX_TIMESTAMP(b.date_modified)) as age,
            b.owner,
            c.name as nm_prior,
            c.color,
            d.name as nm_status,
            if(b.is_email=1,h.addr,e.user) as nm_creator,
            f.user as nm_owner,
            g.name as nm_proj,
            b.id,
            unix_timestamp(b.date_modified) as date_modified,
            b.short_desc
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['tickets'] . ' as b
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['priority'] . ' as c
        ON
            b.priority = c.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['status'] . ' as d
        ON
            b.status = d.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as e
        ON
            b.creator = e.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as f
        ON
            b.owner = f.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['project'] . ' as g
        ON
            b.project = g.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['email_users'] . ' as h
        ON
            b.creator = h.id
        ' . $where . '
        ' . $order . '
        LIMIT ' . $_GET['show'] . ',' . $myNewsConf['default']['limit'];

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $jscript= <<<HTML
    <script language="javascript">
    <!--
        function changeOrder() {
            location="{$baseTix_URI}?mode=list&view={$view}&project={$project}&sort=" + document.list.sort.options[document.list.sort.selectedIndex].value;
        }

        function changeProj() {
            location="{$baseTix_URI}?mode=list&view={$view}&project=" + document.list.proj.options[document.list.proj.selectedIndex].value + "&sort={$sort}";
        }
    //-->
    </script>
HTML;

    $title  = 'Listing Open Tickets';
    $output = loginInfo();
    $output.= "\n\t" . '<br />';
    $output.= "\n\t" . '<br />';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<form name="list">';
    $output.= "\n\t\t\t" . '<td colspan="4" align="left" valign="top">';
    $output.= "\n\t\t\t\t" . 'Ticket List for:';
    $output.= "\n\t\t\t\t" . '<select class="textbox" onChange="changeProj()" name="proj">';
    $output.= "\n\t\t\t\t\t" . '<option value="0">All Projects</option>';

    $projArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project'], 'WHERE id IN(' . $projs . ')');
    if($projArray){
        $projKeys   = array_keys($projArray);
        foreach($projKeys as $projKey){
            $selected = '';
            if($_GET['project'] == $projKey) $selected = ' selected';
            $output.= "\n\t\t\t\t\t" . '<option value="' . $projKey . '"' . $selected . '>' . $projArray[$projKey] . '</option>';
        }
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td colspan="4" align="right" valign="top">';
    $output.= "\n\t\t\t\t" . 'Sort By: ';
    $output.= "\n\t\t\t\t" . '<select class="textbox" onChange="changeOrder()" name="sort">';

    $sortArray = array(
            'id'        => 'ID',
            'priority'  => 'Priority',
            'status'    => 'Status',
            'owner'     => 'Owner',
            'creator'   => 'Creator',
            'project'   => 'Project',
            'age'       => 'Age',
            'added'     => 'Date Added');

    $sortKeys  = array_keys($sortArray);
    foreach($sortKeys as $sortKey){
        $selected = '';
        if($sort == $sortKey) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $sortKey . '"' .  $selected . '>' . $sortArray[$sortKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '</form>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="8">&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>ID:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Priority:</b></td>';
    $output.= "\n\t\t\t" . '<td>&nbsp;&nbsp;&nbsp;<b>Status:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Description:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Owner:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Creator:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Age:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    
    $i  = 1;
    while($row=mysql_fetch_assoc($result)){
        extract($row);

        unset($extra);
        if(($i%2) == 0) $extra = ' class="alt"';

        // Here we calculate the age and set it to a human readable format
        // based on hold old it actually is.
        if($age < 60){
            $age = $age . ' seconds';
        } elseif($age < 3600){
            $age = round($age/60)       . ' minutes';
        } elseif($age < 86400){
            $age = round($age/(60*60),1). ' hours';
        } else {
            $age = round($age/86400)    . ' days';
        }

        // We need to assign nm_creator to 'Unowned' if creator is set to 0.
        if($owner == 0) $nm_owner = 'Unowned';

        // If the creator field is in the format of an email address, we need to change it up a little.
        if(preg_match('/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/',$nm_creator)){
            $nm_creator = '<i><a title="' . $nm_creator . '">Email User</a></i>';
        }

        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top"><small>' . $id . '</small></td>';
        $output.= "\n\t\t\t" . '<td class="highlight" nowrap valign="top" bgcolor="' . $color . '">' . $nm_prior . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">&nbsp;&nbsp;&nbsp;' . $nm_status . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' valign="top"><a href="' . $baseTix_URI . '?mode=edit&id=' . $id . '">' . $short_desc . '</a></td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_owner . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_creator . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_proj . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top" nowrap><small>' . $age . '</small></td>';
        $output.= "\n\t\t" . '</tr>';

        $i++;
    }

    $output.= "\n\t" . '</table>';

    // Build the navigation.
    $extra  = '&mode=list&view=' . $view . '&project=' . $project . '&sort=' . $sort;
    $table  = $myNewsModule['db']['tbl']['tix']['tickets'] . ' as b';
    $output.= makeNav($baseTix_URI, $_GET['show'], $table, $where, $extra);

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;
return $returnArray;
}
/*******************************************************************/
function tixAdd() {
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    // Build the javascript we're going to use.
    $jscript= <<<HTML
        <script language="JavaScript">
        <!--
        function projInfo() {
            myWinLoc="{$baseTix_URI}?mode=info&view=projects&id=" + document.list['mod[project]'].options[document.list['mod[project]'].selectedIndex].value;
            window.open(myWinLoc,'ProjectInfo','width=320,height=240,directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes');
        }

        function checkSubmit() {
            if (document.list['mod[project]'].options[document.list['mod[project]'].selectedIndex].value == -1) {
                alert('Project Missing, You must select a Project.');
            } else if (document.list['mod[category]'].options[document.list['mod[category]'].selectedIndex].value == -1) {
                alert('Category Missing, You must select a Category.');
            } else if (document.list['mod[short_desc]'].value == "") {
                alert('Title Missing, You must enter a Title.');
            } else if (document.list['mod[long_desc]'].value == "") {
                alert('Long Description Missing: You must enter a Long Description');
            } else {
                document.list.submit();
            }
        }

        -->
        </script>
HTML;

    // Build the title.
    $title  = 'Tickets: Add: New';
    $output = loginInfo();

    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . '<form action="' . $baseTix_URI . '?mode=edited" method="post" name="list">';
    $output.= "\n\t" . '<input type="hidden" name="mod[creator]" value="' . $_SESSION['uid'] . '" />';
    $output.= "\n\t" . '<input type="hidden" name="mod[status]" value="1" />';
    $output.= "\n\t" . '<table cellpadding="0" cellspacing="0" width="95%" align="center">';
    $output.= "\n\t\t" . '<tr valign="top">';
    $output.= "\n\t\t\t" . '<td><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[project]">';
    $output.= "\n\t\t\t\t\t" . '<option value="-1">Select Project</option>';
        
    // Build the projects option list.
    $projects   = implode(',', $_SESSION['tix']['user_projects']);
    $tmpArray   = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project'], 'WHERE id IN(' . $projects . ')');
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $project) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }


    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<input class="button" type="button" onClick="projInfo()" name="Info" value="info" />';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td><b>Title:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign="top">';
    $output.= "\n\t\t\t" . '<td><b>Category:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[category]">';
    $output.= "\n\t\t\t\t\t" . '<option value="-1">Select Category</option>';

    // Build the Category option list.
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['category']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td rowspan="3">';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="mod[short_desc]" wrap="virtual" cols="40" rows="3"></textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td><b>Priority:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[priority]">';

    // Build the Priority option list.
    $tmpArray   = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['priority']);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $priority) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign="top">';
    $output.= "\n\t\t\t" . '<td><b>Owner:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[owner]">';

    // Build the Owner option list based on all users that exist in groups the current user
    // exists in.
    $groups     = implode(',',$_SESSION['tix']['user_groups']);
    $tmpArray   = tixGetUserList($groups);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $_SESSION['uid']) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td colspan="3"><b>Long Description:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td colspan="3" align="center">';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="mod[long_desc]" rows="10" cols="70" wrap="virtual"></textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td colspan="3" align="center">';
    $output.= "\n\t\t\t\t" . '<input class="button" type="submit" value="Submit Trouble Ticket" onClick="checkSubmit()" \>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixEdit($id) {
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    $ticket     = tixGetTicket($id);
    extract($ticket);

    // Build the per-page javascript.
    $jscript= <<<HTML
        <script language="JavaScript">
        <!--

        function projInfo() {
            myWinLoc="{$baseTix_URI}?mode=info&view=projects&id=" + document.list['mod[project]'].options[document.list['mod[project]'].selectedIndex].value;
            window.open(myWinLoc,'ProjectInfo','width=320,height=240,directories=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,resizable=yes');
        }

        -->
        </script>
HTML;

    // Build the page/content title.
    $title  = 'Tickets: Edit : <small>(' . $id . ') ' . $short_desc . '</small>';

    // Build the content output.
    $output = loginInfo();
    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . '<form method="post" action="' . $baseTix_URI . '?mode=edited" name="list">';
    $output.= "\n\t" . '<input type="hidden" name="mod[id]" value="' . $id . '">';
    $output.= "\n\t" . '<table border="0" width="650">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top"><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[project]">';

    // If the logged in user doesn't belong to group
    if($_SESSION['tix']['user_projects']){
        $prHash = $_SESSION['tix']['user_projects'];
    } else {
        $prHash = array();
    }
    // Add the current project to the project array.
    array_push($prHash, $project);
    $projs  = implode(',', $prHash);
    
    // Build the projects option list.
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project'], 'WHERE id IN(' . $projs . ')');
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $project) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t\t" . '<input class="button" type="button" onClick="projInfo()" name="Info" value="info" />';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td><b>Title:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Category</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[category]">';
    
    // Build the Category option list.
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['category']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td valign="top" rowspan="2">';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="mod[short_desc]" rows="2" cols="40">' . $short_desc . '</textarea>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Status:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[status]">';
    
    // Build the Status option list.
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['status']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $status) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Creator:</b></td>';
    $output.= "\n\t\t\t" . '<td>' . $name_creator . '</td>';
    $output.= "\n\t\t\t" . '<td><b>Long Description:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Owner</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[owner]">';

    // Build the Owner option list based on all users that exist in groups the current user
    // exists in.
    $groups     = implode(',',$_SESSION['tix']['user_groups']);
    $tmpArray   = tixGetUserList($groups);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $owner) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }
    

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td valign="top" rowspan="4">';
    $output.= "\n\t\t\t\t" . '<textarea class="textbox" name="mod[long_desc]" rows="8" cols="50">' . $long_desc . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Priority:</b></td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="mod[priority]">';
    
    // Build the Priority option list.
    $tmpArray   = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['priority']);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $priority) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }

    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Date Added:</b></td>';
    $output.= "\n\t\t\t" . '<td>' . date($myNewsConf['format']['date']['default'],$added) . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Last Update:</b></td>';
    $output.= "\n\t\t\t" . '<td>' . date($myNewsConf['format']['date']['default'],$modified) . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4">&nbsp;</b>';
    $output.= "\n\t\t" . '</tr>';

    $attach = tixChkAttach($id);
    if($attach){
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td colspan="4"><b>Attachment(s):</b></td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td colspan="4">' . $attach . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td colspan="4">&nbsp;</b>';
        $output.= "\n\t\t" . '</tr>';
    }

    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4"><b>Notes / Modifications:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4">';
    
    // Output the ticket notes into an iFrame
    $output.= "\n\t\t\t\t" . '<iframe name="iframe" src="' . $_SERVER['PHP_SELF'] . '?mode=getNotes&id=' . $id . '" width="100%" height="200" frameborder="0" scrolling="auto"></iframe>';
    $output.= "\n\t\t\t\t" . '<br /><br />';
    $output.= "\n\t\t\t\t" . '<iframe name="iframe" src="' . $_SERVER['PHP_SELF'] . '?mode=getOS&id=' . $id . '" width="100%" height="200" frameborder="0" scrolling="auto"></iframe>';

    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4">&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4"><b>Add Notes:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="4">';
    $output.= "\n\t\t\t\t" . '<blockquote>';
    $output.= "\n\t\t\t\t\t" . '<textarea class="textbox" name="notes" rows="8" cols="70" wrap="virtual"></textarea>';
    $output.= "\n\t\t\t\t\t" . '<br />';

    // Here we check to see if the default notify key is turned on, and
    // check the checkbox if so.
    $notify = tixGetProjInfo($project);
    $notify = eregi_replace(' ','',$notify['notify']);

    if($notify['notify']){
        $output.= "\n\t\t\t\t\t" . 'Email Modification to:';
        $output.= "\n\t\t\t\t\t" . '<blockquote>';

        $notKeys= explode(',',$notify);
        foreach($notKeys as $notKey){
            unset($checked);
            if($myNewsModule['email']['notify']['tix']) $checked = ' checked';
            $output.= "\n\t\t\t\t\t" . '<input type="checkbox" name="notify[' . $notKey . ']"' . $checked . '> ' . $notKey;
            $output.= "\n\t\t\t\t\t" . '<br />';
        }
        $output.= "\n\t\t\t\t\t" . '</blockquote>';
    }

    $output.= "\n\t\t\t\t\t" . 'Also Notify: <small>(comma delimited list of email addresses)</small>';
    $output.= "\n\t\t\t\t\t" . '<blockquote>';
    $output.= "\n\t\t\t\t\t" . '<input class="textbox" type="text" name="alsoNot" size="30">';
    $output.= "\n\t\t\t\t\t" . '</blockquote>';

    // Output the option to create an outage summary if the outage
    // summary module is turned on.
    if($myNewsModule['tix']['os']['on']){
        $output.= "\n\t\t\t\t\t" . '<input type="checkbox" name="genOS"> Create an Outage Summary';
    }

    $output.= "\n\t\t\t\t" . '</blockquote>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="center" colspan="4">';

    // If we are not the owner of the ticket, give the option to take
    // ownership
    if($_SESSION['uid'] != $owner){
        $output.= "\n\t\t\t\t" . '<input class="button" type="submit" name="take" value="Take Ownership" />';
    }
    $output.= "\n\t\t\t\t" . '<input class="button" type="submit" name="save" value="Save Trouble Ticket" />';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '';

    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixGetMods($id){
global $myNewsConf;
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            a.*,
            if(a.is_email=1,c.addr,b.name) as name,
            unix_timestamp(ts) as timestamp
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['modifications'] . ' as a
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as b
        ON
            a.uid = b.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['email_users'] . ' as c
        ON
            a.uid = c.id
        WHERE
            ticket_id = ' . $id . '
        ORDER by
            timestamp DESC';
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
        $errorTxt  .=   "\n" . 'No Modifications Found';
        $errorTxt  .=   "\n" . '</body>';
        $errorTxt  .=   "\n" . '</html>';
        
        $returnArray['content'] = $errorTxt;
        return $returnArray;
    }

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $output.= "\n\t" . '<small>&nbsp;&middot;&nbsp;<u>Modification by ' . $name . ' on ' . date($myNewsConf['format']['date']['default'],$timestamp) . '</u></small>';
        $output.= "\n\t" . '<blockquote>';
        $text   = str_replace('&lt;b&gt;','<b>',$text);
        $text   = str_replace('&lt;/b&gt;','</b>',$text);
        $text   = str_replace(' ','&nbsp; ',$text);
        $text   = nl2br($text);
        //$text   = eregi_replace(' ','&nbsp;',$text);
        $output.= "\n\t\t" . $text;
        
        $attach = tixChkAttach($id,$rowid);
        if($attach){
            $output.= "\n\t\t" . '<b>Attachment(s):</b> <br />';
            $output.= "\n" . $attach;
        }

        $output.= "\n\t" . '</blockquote>';
    }

    $output.= "\n" . '</body>';
    $output.= "\n" . '</html>';

    $returnArray['content'] = $output;
return $returnArray;
}
/*******************************************************************/
function tixEdited($id) {
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    if(empty($id)) $addMode = true;

    // Here we define what key types have "modify" notifications.
    $setTypes = array('project','category','status','priority');

    // Get the ticket from the hash returned by tixGetTicket($id)
    if(!$addMode){
        $ticket     = tixGetTicket($id);
        $modKeys    = array_keys($_POST['mod']);
        $modified   = false;
        foreach($modKeys as $modKey){
            // If there is a disrepancy between the current ticket
            // and the submitted ticket, we need to add it to the
            // $upHash.
            if($_POST['mod'][$modKey] != $ticket[$modKey]){
                $modified = true;
                $upHash[$modKey] = $_POST['mod'][$modKey];
            }
        }
    }

    // We need to update the owner field if the current user is taking
    // ownership of the ticket.
    if($_POST['take']){
        // Connect to the database.
        mynews_connect();

        // Build and execute the update query.
        $query  = '
            UPDATE
                ' . $myNewsModule['db']['tbl']['tix']['tickets'] . '
            SET owner = ' . $_SESSION['uid'] . '
            WHERE
                id = ' . $id;
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        $modtxt.= '<b>***</b> ' . $_SESSION['fullname'] . ' has taken ownership' . "\n";
    }

    // If we are in add mode we insert the data into the database.
    // differently.
    if($addMode){
        // Connect to the database.
        mynews_connect();

        // Build and execute our insert query.
        $query  = '
            INSERT into
                ' . $myNewsModule['db']['tbl']['tix']['tickets'] . "
            VALUES(
                NULL,
                '" . addslashes($_POST['mod']['owner']) . "',
                '" . addslashes($_POST['mod']['short_desc']) . "',
                '" . addslashes($_POST['mod']['long_desc']) . "',
                '" . addslashes($_POST['mod']['category']) . "',
                NULL,
                '" . date('YmdHis') . "',
                '" . addslashes($_POST['mod']['priority']) . "',
                '" . addslashes($_POST['mod']['status']) . "',
                '" . addslashes($_POST['mod']['project']) . "',
                '" . addslashes($_POST['mod']['creator']) . "',
                0)";
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        // Now we set the id.
        $id = mysql_insert_id();
    }

    // If $modified is set, we need to build our insert query properly.
    if($modified){
        $i = 1;
        $update = 'SET ';
        $upKeys = array_keys($upHash);
        $count  = count($upKeys);
        foreach($upKeys as $upKey){
            unset($extra);
            if($i < $count) $extra = ', ';
            $update.= $upKey . " = '" . addslashes($upHash[$upKey]) . "'" . $extra;

            // Only add the text to modify if it is a particular key type
            if(in_array($upKey,$setTypes)){
                $modtxt.= '** ' . ucfirst($upKey) . ' has been set to ' . tixGetFieldName($upKey,$upHash[$upKey]) . "\n"; 
            }

            if($upKey == 'owner'){
                if($upHash[$upKey] == 0){
                    $toWho  = 'Unowned';
                } else {
                    $toWho  = tixGetUserName($upHash[$upKey]);
                }
                $modtxt.= '*** ' . $_SESSION['fullname'] . ' has assigned ownership to: ' . $toWho . "\n";
            } elseif ($upKey == 'long_desc' || $upKey == 'short_desc'){
                $transArray = array('long_desc' => 'Long Description', 'short_desc' => 'Title');
                $modtxt.= '*** ' . $transArray[$upKey] . ' has been updated from:' . "\n";
                $modtxt.= "\n" . '' . $ticket[$upKey] . '' . "\n";
                $modtxt.= "\n" . '------------------------------------------------------' . "\n\n";
            }



        $i++;
        }
        // Connect to the database.
        mynews_connect();

        // Build and execute the update query.
        $query  = '
            UPDATE
                ' . $myNewsModule['db']['tbl']['tix']['tickets'] . '
            ' . $update . '
            WHERE
                id = ' . $id;
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;
    }

    if($_POST['notes']){
        if($modtxt){
            $modtxt.= "\n";
        }
        $modtxt.= $_POST['notes'];
    } 

    // We need to add the modification into the database.
    if($modtxt){

        // Add the modification to the database.
        // and get the return string to display
        $modification = tixAddMod($id, $_SESSION['uid'], $modtxt);

        // Here we send an email notification to the notify list
        // of the project (if it exists) and the notify config key
        // is true.
        if($_POST['alsoNot'] || $_POST['notify']){
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

            $fullTix_URI = $myNewsConf['default']['siteurl'] . $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];
            $tmpHash= array(
                '%DATE%'    => date("l F, dS Y H:i:s A [T]"),
                '%NUM%'     => $id,
                '%ACTION%'  => 'Updated',
                '%DESC%'    => $_POST['mod']['short_desc'],
                '%MOD%'     => strip_tags($modtxt),
                '%URL%'     => $fullTix_URI,
                '%SYS_NAME%'=> $myNewsModule['name']['tix']);

            $subj   = $myNewsModule['name']['tix'] . ':  Update Notification [#' . $_POST['mod']['id'] . ']';
            $body   = parseEmailTemplate($myNewsModule['tix']['template']['notify'], $tmpHash);

            $rcpt   = implode(',',$notifyArray);

            emailNotify($body,$subj,$rcpt,$myNewsModule['email']['mbox']);
        }
    }

    // Here we check to see if the Outage Summary (os) module is turned on.
    if($_POST['genOS']){
        $desc   = base64_encode($_POST['mod']['short_desc']);
        header('Location: ' . $baseTix_URI . '?mode=osAdd&id=' . $id . '&project=' . $_POST['mod']['project'] . '&desc=' . $desc);
        return;
    }

    // Build the meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseTix_URI . '?mode=edit&id=' . $id . '">';

    // Build the content title.
    $title  = 'Tickets: Edited: <small>(' . $id . ') ' . $_POST['mod']['short_desc'] . '</small>';

    // Build the page content.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'Updated ticket <small>(' . $_POST['mod']['id'] . ')</small>';
    $output.= "\n\t\t" . '<blockquote>';

    if(is_array($modification)){
        $output.= $modification['error'];
    } else {
        $output.= nl2br($modification);
    }    

    $output.= "\n\t\t" . '</blockquote>';
    if($notify){
        $output.= "\n\t" . '<i>Notification sent to project distribution</i>';
    }
    $output.= "\n\t" . '</blockquote>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixAddMod($id, $uid, $text){
global $myNewsModule;

        // We need to clean all of the rogue escape chars out of the
        // modification text.
        $text   = cleanUpGPC($text);

        // Since we are inserting into the database, we need to 
        // run htmlspecialchars, so it displays correctly in the
        // ticket notes.
        $text   = preg_replace("/\[code\](.+?)\[\/code\]/ies", "htmlspecialchars('\\1',ENT_COMPAT)", $text);
        $text   = str_replace('\&quot;','"',$text);

        // Update the tickets table with a new modified tstmp.
        $tixUpdate  = tixUpdateTstmp($id);
        if(is_array($tixUpdate)) return $tixUpdate;

        // Connect to the database.
        mynews_connect();

        // Build and execute our modification query.
        $query  = '
            INSERT INTO
                ' . $myNewsModule['db']['tbl']['tix']['modifications'] . "
            VALUES(
                NULL,
                '" . addslashes($id)    . "',
                NULL,
                '" . addslashes($text)  . "',
                '" . addslashes($uid)   . "',
                0)";
        $result = mysql_query($query);
        $sqlErr = myNewschkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

return $text;
}
/*******************************************************************/
function tixUpdateTstmp($id){
global $myNewsModule;

        // Connect to the database
        mynews_connect();

        // We need to update the tickets table with a new modified
        // timestamp if we are adding a modification.
        $query  = '
            UPDATE
                ' . $myNewsModule['db']['tbl']['tix']['tickets'] . '
            SET
                date_modified = NULL
            WHERE
                id = ' . addslashes($id);
        $result = mysql_query($query);
        $sqlErr = myNewschkSqlErr($result, $query);

        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

return true;
}
/*******************************************************************/
function tixGetTicket($id){
global $myNewsConf;
global $myNewsModule;

    // Connect to the database
    mynews_connect();

    // Build and execute our query
    $query  = '
        SELECT
            a.*,
            if(a.is_email=1,c.addr,b.name) as name_creator,
            unix_timestamp(date_modified) as modified,
            unix_timestamp(date_added) as added
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['tickets'] . ' as a
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as b
        ON
            a.creator = b.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['email_users'] . ' as c
        ON
            a.creator = c.id
        WHERE
            a.id = ' . $id;

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Assign our data to a hash.
    $record = mysql_fetch_assoc($result);

return $record;
}
/*******************************************************************/
function tixGetUserInfo(){
/*
 * This function gets the userinfo out of the tix.udb table
 * and inserts certain tix based values into the users session.
 */
global $myNewsConf;
global $myNewsModule;

    // Check to see if we have already gotten the proper user info.
    if($_SESSION['tix']) return;

    // clear out any old data if we have to run it again.
    unset($_SESSION['tix']);

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            b.gid,
            project
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['acl'] . ' as a
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['groups'] . " as b
        ON
            a.id_tag = b.gid  AND a.acl_type = 'group'
        WHERE
            (acl_type = 'user' AND id_tag = " . $_SESSION['uid'] . ")
            OR
            (acl_type = 'group' AND id_tag IN(b.gid))
            AND
            (b.uid = " . $_SESSION['uid'] . ')';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $_SESSION['tix']['user_projects'][$project] = $project;
        if(!is_null($gid)) $_SESSION['tix']['user_groups'][] = $gid;
    }

return;
}
/*******************************************************************/
function tixGetUserList($groups,$limit=true,$unknown=true){
global $myNewsModule;

    // We need to set some of the default users.
    if($unknown) $returnArray[0] = 'Unowned';

    // Here we check to see if we want to include a where clause or not.
    if($limit){
        $query = '
            SELECT
                b.name,
                a.uid
            FROM
                ' . $myNewsModule['db']['tbl']['tix']['groups'] . ' as a
            LEFT JOIN
                ' . $myNewsModule['db']['tbl']['tix']['udb'] . ' as b
            ON
                a.uid = b.uid
            WHERE a.gid in(' . $groups . ')
            ORDER by a.uid';
    } else {
        $query = '
            SELECT
                name,
                uid
            FROM
                ' . $myNewsModule['db']['tbl']['tix']['udb'];
    }

    // Connect to the database.
    mynews_connect();

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) echo $sqlErr;

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $returnArray[$uid] = $name;
    }

return $returnArray;
}
/*******************************************************************/
function tixBuildOptionList($table, $where=''){
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            id,
            name
        FROM
            ' . $table . '
        ' . $where . '
        ORDER by id';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    if($sqlErr) return $sqlErr;

    while($row  = mysql_fetch_assoc($result)){
        extract($row);
        $returnArray[$id] = $name;
    }
return $returnArray;
}
/*******************************************************************/
function tixGetUserName($id){
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            name
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['udb'] . '
        WHERE
            uid = ' . $id;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $record = mysql_fetch_assoc($result);
    $output = $record['name'];

return $output;
}
/*******************************************************************/
function tixGetFieldName($table,$id){
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            name
        FROM
            ' . $myNewsModule['db']['tbl']['tix'][$table] . '
        WHERE
            id = ' . $id;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $record = mysql_fetch_assoc($result);
    $output = $record['name'];

return $output;
}
/*******************************************************************/
function tixInfo($view,$id){
global $myNewsConf;
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            *
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['project'] . '
        WHERE
            id = ' . $id;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    if($sqlErr) return $sqlErr;

    $record = mysql_fetch_assoc($result);
    extract($record);

    $output = "\n" . '<html>';
    $output.= "\n" . '<head>';
    $cssFile= $myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css';
    if(file_exists($cssFile)){
        $output.= "\n\t\t" . '<link rel="STYLESHEET" type="text/css" href="' . $myNewsConf['path']['web']['index'] . 'templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css">';
    }
    $output.= "\n" . '</head>';
    $output.= "\n" . '<body>';
    $output.= "\n\t" . '<p align="center">';
    $output.= "\n\t" . '<b>' . $name . '</b>';
    $output.= "\n\t" . '<p align="left">';
    $output.= "\n\t" . '<b>Description:</b> ';
    $output.= "\n\t" . $description;
    $output.= "\n\t" . '<p>';
    
    if($notify){
        $output.= "\n\t" . '<b>Notify List:</b> ';
        $output.= "\n\t" . $notify;
    }

    $output.= "\n" . '</body>';
    $output.= "\n" . '</html>';

return $output;
}
/*******************************************************************/
function tixNav($project='0',$sort='age'){
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];



    $output.= <<<HTML

        &nbsp;
        <small>[Jump to a ticket]</small>
        <br />
        <form  method="GET" action="{$_SERVER['PHP_SELF']}">
            <input type="hidden" name="mode" value="edit">
            &nbsp;&nbsp;&nbsp;&middot;&nbsp;
            <small>Ticket ID:</small>&nbsp;
            <input class="textbox" type="text" name="id" size="2">
        </form>
        &nbsp;
        <small>[Ticket Queues:]</small>
        <br />&nbsp;&nbsp;&nbsp;&nbsp;
        <small>[My]</small>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=my.open&project={$project}&sort={$sort}">Open</a>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=my.submit&project={$project}&sort={$sort}">Submitted</a>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=my.closed&project={$project}&sort={$sort}">Closed/Fixed</a>
        <br />&nbsp;&nbsp;&nbsp;&nbsp;
        <small>[Group]</small>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=group.open&project={$project}&sort={$sort}">Open</a>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=group.closed&project={$project}&sort={$sort}">Closed/Fixed</a>
                <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=list&view=unowned&project={$project}&sort={$sort}">Unowned</a>
        <br />&nbsp;
        <small>[Ticket Functions:]</small>
                <br />&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=search">Tickets: Search</a>
                <br />&nbsp;&nbsp;&nbsp;&middot;&nbsp;<a href="{$baseTix_URI}?mode=add">Tickets: Add New</a>
HTML;

return $output;
}
/*******************************************************************/
function tixGetProjInfo($id){
global $myNewsConf;
global $myNewsModule;

    // Connect to the database.
    mynews_connect();

    // We need to get the notify list out of the projects table.
    $query  = '
        SELECT
            *
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['project'] . '
        WHERE
            id = ' . $id;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $record = mysql_fetch_assoc($result);

return $record;
}
/*******************************************************************/
function tixSearch(){
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    // Build the page/content title.
    $title  = 'Tickets: Search';

    // Build content output.
    $output = loginInfo();
    $output.= "\n\t" . '<p>';

    $output.= "\n\t" . '<form method="post" action="' . $baseTix_URI . '?mode=searchResults">';
    $output.= "\n\t\t" . '<input type="hidden" name="area" value="search">';
    $output.= "\n\t\t" . '<input type="hidden" name="submitSearch" value="1">';

    $output.= "\n\t" . '<table border=0 width="95%">';

    // Output the Titles for the following select boxes:
    // Short Desc, Long Desc, Category, Status.
    $output.= "\n\t\t" . '<tr class="alt" valign=top>';
    $output.= "\n\t\t\t" . '<td colspan=2><b>Title</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Category</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Status</b></td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the Title textbox.
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td colspan=2 >';
    $output.= "\n\t\t\t\t" . '<input type="text" name="short_desc" size="30">';
    $output.= "\n\t\t\t" . '</td>';

    // Output the Category select box.
    $output.= "\n\t\t\t" . '<td rowspan=3>';
    $output.= "\n\t\t\t\t" . '<select name=mod[category][] SIZE="10" MULTIPLE>';
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['category']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';

    // Output the Status select box.
    $output.= "\n\t\t\t" . '<td rowspan=3>';
    $output.= "\n\t\t\t\t" . '<select name=mod[status][] size="10" MULTIPLE>';
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['status']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the Long Description textbox.
    $output.= "\n\t\t" . '<tr height="1" valign=top>';
    $output.= "\n\t\t\t" . '<td colspan=2><b>Long Description</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan=2 align="left" valign="top">';
    $output.= "\n\t\t\t\t" . '<input type="text" name="long_desc" size="30">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the Titles for the following select boxes.
    // Project, Priority, Owner, Creator
    $output.= "\n\t\t" . '<tr class="alt" align=top>';
    $output.= "\n\t\t\t" . '<td><b>Project</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Priority</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Assigned To</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Creator</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr valign=top>';

    // Output the Project select box.
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name=mod[project][] SIZE="10" MULTIPLE>';
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['project']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';

    // Output the Priority select box.
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name=mod[priority][] SIZE="10" MULTIPLE>';
    $tmpArray  = tixBuildOptionList($myNewsModule['db']['tbl']['tix']['priority']);
    $tmpKeys   = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        unset($selected);
        if($tmpKey == $category) $selected = ' selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '"' . $selected . '>' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';

    // Output the Owner select box.
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name=mod[owner][] SIZE="10" MULTIPLE>';
    $tmpArray   = tixGetUserList('',false,true);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '">' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';

    // Output the creator select box.
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select name=mod[creator][] SIZE="10" MULTIPLE>';
    $tmpArray   = tixGetUserList('',false,true);
    $tmpKeys    = array_keys($tmpArray);
    foreach($tmpKeys as $tmpKey){
        $output.= "\n\t\t\t\t\t" . '<option value="' . $tmpKey . '">' . $tmpArray[$tmpKey] . '</option>';
    }
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the Title for the date selectors.
    $output.= "\n\t\t" . '<tr class="alt" valign=top>';
    $output.= "\n\t\t\t" . '<td colspan="4"><b>Date Select</b></td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the Start date selections.
    $output.= "\n\t\t" . '<tr valign=top>';
    $output.= "\n\t\t\t" . '<td colspan="6">';
    $output.= "\n\t\t\t\t" . '<input type="checkbox" name="useDate" /> Use date<br>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t\t" . '<td>Begin Date:</td>';
    $output.= "\n\t\t\t" . '<td colspan="5">';
    $output.= "\n\t\t\t\t" . DateSelector('begin', time());
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';

    // Output the End date selections.
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>End Date:</td>';
    $output.= "\n\t\t\t" . '<td colspan="5">';
    $output.= "\n\t\t\t\t" . DateSelector('end', time());
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';

    // Output the submit button.
    $output.= "\n\t\t\t" . '<td colspan="6">';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . $myNewsConf['button']['search'];
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';

    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function tixSearchResults(){
global $myNewsConf;
global $myNewsModule;

    $baseTix_URI = $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts']['tix'];

    // Here we build our WHERE clause, based on what search terms
    // were provided.
    
    // Initiate the WHERE clause.
    $where  = 'WHERE (1)';

    // Now we need to go through each search criteria, and see if 
    // we build an AND statement, if the criteria is populated.
    if($_POST['mod']){
        $modKeys= array_keys($_POST['mod']);
        foreach($modKeys as $modKey){
            $list   = implode(',', $_POST['mod'][$modKey]);
            $where .= "\n\t" . ' AND ' . $modKey . ' IN(' . $list . ')';
        }
    }

    // We also need to build a LIKE statement for each text field.
    if($_POST['long_desc']){
        $where .= "\n\t" . " AND long_desc LIKE '%" . addslashes($_POST['long_desc']) . "%'";
    }
    if($_POST['short_desc']){
        $where .= "\n\t" . " AND short_desc LIKE '%" . addslashes($_POST['short_desc']) . "%'";
    }

    // Here we check to see if "Use Date" is checked. If so, create 
    // an AND clause that limits it between the dates selected.
    if($_POST['useDate']){
        $sEpoch = mktime($_POST['begin']['hour'], $_POST['begin']['minute'], 0, $_POST['begin']['month'], $_POST['begin']['day'],$_POST['begin']['year']);
        $eEpoch = mktime($_POST['end']['hour'], $_POST['end']['minute'], 0, $_POST['end']['month'], $_POST['end']['day'],$_POST['end']['year']);
        $where .= "\n\t" . ' AND unix_timestamp(b.date_modified) between ' . $sEpoch . ' AND ' . $eEpoch;
    }

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query  = '
        SELECT
            (unix_timestamp(NOW()) - UNIX_TIMESTAMP(b.date_modified)) as age,
            b.owner,
            c.name as nm_prior,
            c.color,
            d.name as nm_status,
            if(b.is_email=1,h.addr,e.user) as nm_creator,
            f.user as nm_owner,
            g.name as nm_proj,
            b.id,
            unix_timestamp(b.date_modified) as date_modified,
            b.short_desc
        FROM
            ' . $myNewsModule['db']['tbl']['tix']['tickets']  . ' as b
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['priority'] . ' as c
        ON
            b.priority  = c.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['status']   . ' as d
        ON
            b.status    = d.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb']      . ' as e
        ON
            b.creator   = e.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['udb']      . ' as f
        ON
            b.owner     = f.uid
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['project']  . ' as g
        ON
            b.project   = g.id
        LEFT JOIN
            ' . $myNewsModule['db']['tbl']['tix']['email_users'] . ' as h
        ON
            b.creator = h.id
        ' . $where . '
        ORDER BY date_modified desc';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $count  = mysql_num_rows($result);

    $output = loginInfo();

    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . '<table width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>ID:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Priority:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Status:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Description:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Owner:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Creator:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Project:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Age:</b></td>';
    $output.= "\n\t\t" . '</tr>';

    while($record = mysql_fetch_assoc($result)){
        extract($record);

        unset($extra);
        if(($i%2) == 0) $extra = ' class="alt"';

        // Here we calculate the age and set it to a human readable format
        // based on hold old it actually is.
        if($age < 60){
            $age = $age . ' seconds';
        } elseif($age < 3600){
            $age = round($age/60)       . ' minutes';
        } elseif($age < 86400){
            $age = round($age/(60*60),1). ' hours';
        } else {
            $age = round($age/86400)    . ' days';
        }

        // We need to assign nm_creator to 'Unowned' if creator is set to 0.
        if($owner == 0) $nm_owner = 'Unowned';

        // If the creator field is in the format of an email address, we need to change it up a little.
        if(preg_match('/^[A-z0-9][\w.-]*@[A-z0-9][\w\-\.]+\.[A-z0-9]{2,6}$/',$nm_creator)){
            $nm_creator = '<i><a title="' . $nm_creator . '">Email User</a></i>';
        }

        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top"><small>' . $id . '</small></td>';
        $output.= "\n\t\t\t" . '<td class="highlight" nowrap valign="top" bgcolor="' . $color . '">' . $nm_prior . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_status . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' valign="top"><a href="' . $baseTix_URI . '?mode=edit&id=' . $id . '">' . $short_desc . '</a></td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_owner . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_creator . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top">' . $nm_proj . '</td>';
        $output.= "\n\t\t\t" . '<td' . $extra . ' nowrap valign="top" nowrap><small>' . $age . '</small></td>';
        $output.= "\n\t\t" . '</tr>';

        $i++;
    }
    $output.= "\n\t" . '</table>';

    if($count == 0) $output = myNewsError(1,'Your Search Criteria produced no results.');

    // Build the page/content title.
    $title  = 'Tickets : Search Results : <small>(' . $count . ') found</small>';

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
/**
 * Check the defined attachments directory and if an attachment exists,
 * displays a link to "download" it.
 */
function tixChkAttach($tktNum, $modNum=0){
global $myNewsModule;

    $attachDir  = $myNewsModule['path']['sys']['attach'] . '/' . $tktNum . '/' . $modNum;
    if(file_exists($attachDir)){
        $handle = opendir($attachDir);
        while($file = readdir($handle)){
            if(!is_dir($attachDir . '/' . $file)){
                $attach.= '&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;';
                $attach.= '<a href="' . $myNewsModule['path']['web']['attach'] . '/' . $tktNum . '/' . $modNum . '/' . $file . '" target="_blank">';
                $attach.= $file;
                $attach.= '</a><br />' . "\n";
            }
        }
        closedir($handle);
    } else { return false; }

return $attach;
}
/*******************************************************************/
?>
