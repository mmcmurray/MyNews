<?php
/* $Id: comments.lib.php 495 2005-09-16 14:12:09Z alien $ */

/********************************************************************/
function cmSelect(){
/**
 * The purpose of this function is to display a list of news items
 * with comments attached to them.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    if(!isset($_GET['show'])) $_GET['show'] = 0;

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    $query  = ('
        SELECT
            count(a.cmtnr) as count,
            a.artnr as sid,
            b.title,
            b.author,
            b.section
        FROM
            ' . $myNewsConf['db']['table']['comments'] . ' as a
        LEFT JOIN
            ' . $myNewsConf['db']['table']['news']  . ' as b
        ON
            a.artnr = b.artnr
        WHERE
            type = "news"
        GROUP by sid
        ORDER by sid desc
        LIMIT ' . addslashes($_GET['show']) . ', ' . $myNewsConf['default']['limit']);

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    if(!$sqlErr){

        $output = loginInfo();
        $output.= <<<EOT

        <blockquote>
        <table border=0 width="95%">
            <tr>
                <td>&nbsp;</td>
                <td><b>Title:</b></td>
                <td><b>Section:</b></td>
                <td><b>Author:</b></td>
                <td><b>Comments:</b></td>
            </tr>
EOT;

        while($row = mysql_fetch_assoc($result)){
            extract($row);

            // We need to base64_encode the title so we can pass it via $_GET
            $encTitle   = base64_encode($title);

            $output.= <<<EOT

            <tr>
                <td>
                    (<a href="{$baseNewsAdmin_URI}?mode=cmSelected&sid={$sid}&title={$encTitle}">View</a>)
                </td>
                <td>{$title}</td>
                <td>{$section}</td>
                <td>{$author}</td>
                <td align="center"><b>{$count}</b></td>
            </tr>

EOT;
        }

        $output.= "\t" . '</table>' . "\n\t" . '</blockquote>';

        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Select';

        $query  = ('
            SELECT
                count(distinct artnr) as total
            FROM
                ' . $myNewsConf['db']['table']['comments']);

        $result = mysql_query($query);
        $sqlErr.= myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
            $row    = mysql_fetch_assoc($result);
             extract($row);

            $next   = addslashes($_GET['show']) + $myNewsConf['default']['limit'];
            $back   = addslashes($_GET['show']) - $myNewsConf['default']['limit'];

            $output.= "\n" . '<p align="center">';

            if($back >= 0){
                $output.= '<a href="' . $baseNewsAdmin_URI . '?mode=cmSelect&show=' . $back . '">' . $myNewsConf['button']['back'] . '</a>';
                $output.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }

            if($next < $total){
                $output.= '<a href="' . $baseNewsAdmin_URO . '?mode=cmSelect&show=' . $next . '">' . $myNewsConf['button']['next'] . '</a>';
            }
        }
    }


    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cmSelected(){
/**
 * This function takes the selected sid (artnr) and displays a comment
 * table with the option of editing or deleting the particular comment
 * and it's children (if applicable).
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--
            function confirmParentDelete(URL,SID,TITLE){
                 where_to= confirm("Do you really want to delete the item:\\n\\n" + TITLE + "\\n\\n Note:  This will delete the comment and all replies");
                if (where_to== true) {
                    window.location=URL;
                } else {
                    window.location="{$baseNewsAdmin_URI}?mode=cmSelected&sid=" + SID;
                }
            }
        //-->
        </script>
EOT;

    $cHash  = cmChildModify($_GET['sid']);

    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Selected : <small>' . base64_decode($_GET['title']) . '</small>';;
    $output = loginInfo(); 
    $output.= "\n\t" . '<blockquote>' . "\n";
    $output.= $cHash['content'];
    $output.= "\n\t" . '</blockquote>' . "\n";
    $error  = $cHash['error'];

    
    $returnArray['error']   = $error;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cmDeleted(){
/**
 * The purpose of this function is to call cmChildModify() on the passed
 * $topic_id and delete the comment and it's children.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Deleted : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        $cHash  = cmChildModify($_GET['cid'], 1 , 'news', 'delete');

        $cHash['data'][]    = $_GET['cid'];

        foreach($cHash['data'] as $cid){
            $query  = '
                DELETE
                FROM
                    ' . $myNewsConf['db']['table']['comments'] . '
                WHERE
                    cmtnr = ' . $cid;
            $result = mysql_query($query);
            $sqlErr.= myNewsChkSqlErr($result, $query);
        }

        $meta   = '<meta http-equiv="Refresh" content="2; URL=' . $baseNewsAdmin_URI . '?mode=cmSelect">';

        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Deleted : <small>' . base64_decode($_GET['title']) . '</small>';;

        $output = loginInfo();
        $output.= "\n\t" . '<blockquote>' . "\n";
        $output.= "\n\t\t" . 'Deleted <b>' . base64_decode($_GET['title']) . '</b>';
        $output.= ' and the following replies:';
        $output.= '<br />';
        $output.= $cHash['content'];
        $output.= "\n\t" . '</blockquote>' . "\n";

        $error  = $sqlErr;
        $error .= $cHash['error'];
    }

    $returnArray['error']   = $error;
    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cmEdit(){
/**
 * This function prints out a form with the data populated from an
 * existing comment so and editor can edit it.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    // Predefined sizes for text and textarea form fields.
    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    // Connect to the database;
    mynews_connect();

    // Build and execute our query.
    $query  = '
        SELECT
            cmtnr as cid,
            cmtitle as title,
            pid,
            commenttext as copytext,
            cmauthor as author,
            cmemail as email,
            cmdate as date,
            cmip as addr,
            artnr as sid,
            type
        FROM
            ' . $myNewsConf['db']['table']['comments'] . '
        WHERE
            cmtnr = ' . addslashes($_GET['cid']);

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Only continue if $sqlErr is empty
    if(!$sqlErr){
        $row    = mysql_fetch_assoc($result);
         extract($row);

        $output = loginInfo();

        $output.= <<<EOT

        <blockquote>
        <form method="post" action="{$baseNewsAdmin_URI}?mode=cmEdited&cid={$cid}&sid={$sid}">
        <table border=0 width="95%">
            <tr>
                <td valign="top" align="left"><b>1)</b> Title:</td>
                <td>
                    <input class="textbox" type="text" name="title" size="{$tWidth}" value="{$title}">
                </td>
            </tr>
            <tr>
                <td valign="top" align="left"><b>2)</b> Comment:</td>
                <td>
                    <textarea name="copytext" cols="{$taCols}" rows="{$taRows}" wrap="virtual">{$copytext}</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                    <input type="hidden" name="author" value="{$author}">
                    <input type="hidden" name="email" value="{$email}">
                    <input type="hidden" name="date" value="{$date}">
                    <input type="hidden" name="addr" value="{$addr}">
                    <input type="hidden" name="type" value="{$type}">
                    <input type="hidden" name="pid" value="{$pid}">
                </td>
                <td>
                    {$myNewsConf['button']['submit']}
                </td>
            </tr>
        </table>            
        </form>
        </blockquote>

EOT;

        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Edit : <small>' . $title . '</small>';
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cmEdited(){
/**
 * This function processes the form submitted via cmEdit()
 * and commits changes to the comment to the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor');
    if($errorArray) return $errorArray;

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comment : Edited : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Connect to the database
        mynews_connect();

        $query  = '
            UPDATE
                ' . $myNewsConf['db']['table']['comments'] . '
            SET
                cmtitle = "' . addslashes($_POST['title']) . '",
                commenttext = "' . addslashes($_POST['copytext']) . '"
            WHERE
                cmtnr = ' . addslashes($_GET['cid']). '
            AND
                artnr = ' . addslashes($_GET['sid']);

        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Comments : Edited : <small>' . $_POST['title'] . '</small>';

        $meta   = '<meta http-equiv="Refresh" content="2; URL=' . $baseNewsAdmin_URI . '?mode=cmSelect">';

        $output = loginInfo();
        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t\t" . '<b>"' . $_POST['copytext'] . '"</b> has been updated.';
        $output.= "\n\t" . '</blockquote>';
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cmChildModify($topic_id=0, $level=0, $type='news', $action='list'){
/**
 * This function displays comments posted to a particular   
 * story                            
 *
 * Ex:  cmChildModify($id);                 
 */
global $myNewsConf;
global $deleteArray;

    // General Definitions.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
    $comments = 0;

    // Build the nesting.
    for($i=0;$i <= $level;$i++){
        if($i > 0) $pad.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        if($i > 0) $dot = $myNewsConf['button']['thread'];
        $tab   .= "\t";
    }

    // Based on our level, we need a different WHERE clause for our SQL
    if($level == 0) {
        $wClause    = 'artnr = ' . $topic_id . ' and pid = 0';
    } else {
        $wClause    = 'pid = ' . $topic_id;
    }

    // Connect to the database
    mynews_connect();

    // Build and run our query
    $query  = ('
        SELECT
            artnr as sid,
            cmtnr as tid,
            cmtitle as title,
            cmauthor as author,
            cmdate as timestamp,
            commenttext as comment
        FROM
            ' . $myNewsConf['db']['table']['comments'] . '
        WHERE
            ' . $wClause . "
            and
            type = '". $type . "'
        ORDER by
            cmdate, artnr");

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    while($row = mysql_fetch_assoc($result)) {
        extract($row);

        // If author is not a valid alphanumeric value, let's set it to
        // something readable.
        if (!eregi("[a-z0-9]",$author)) $author = '[no name]';

        // We need to base64_encode() the title, so we can pass
        // it via $_GET
        $encTitle   = base64_encode($title);

        // Output our Links
        if($action == 'list'){
            $output.= "\n" . $tab;
            $output.= '(';
            $output.= '<a href="' . $baseNewsAdmin_URI . '?mode=cmEdit&cid=' . $tid . '&title=' . $encTitle . '">edit</a>';
            $output.= ' | ';
            $output.= '<a href="javascript:confirmParentDelete(\'' . $baseNewsAdmin_URI . '?mode=cmDeleted&cid=' . $tid . '&sid=' . $sid . '&title=' . $encTitle . "','" . $sid . "','" . addslashes($title) . '\')">delete</a>';
            $output.= ')';
        }
        $output.= "\n" . $tab;
        $output.= $pad;
        $output.= $dot . '&nbsp;';
        $output.= $title . ' by <b>' . $author . '</b>';
        $output.= "\n" . $tab;
        $output.= '<br />';

        // Insert our returned data back into the returnArray
        $oArray = cmChildModify($tid, $level+1, $type, $action);
        $output.= $oArray['content'];
        $sqlErr.= $oArray['error'];

        // Create an array with comment ids to be deleted.
        $deleteArray[] = $tid;

        $comments++;
    }

    $returnArray['data']    = $deleteArray;
    $returnArray['error']   = $sqlErr;
    $returnArray['content'] = $output;

return $returnArray;
} //End cmChildModify()
/********************************************************************/
?>
