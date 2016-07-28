<?
/* $Id: subs.lib.php 481 2005-08-18 20:13:06Z mmcmurr $ */

/*******************************************************************/
function subList(){
/**
 * This function lists all submissions in the database
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function confirmDelete(URL,TITLE){
            where_to = confirm("Do you really want to delete the submission:\\n\\n" + TITLE); 
            if (where_to == true) { 
                window.location = URL;
            } else {  
                window.location = "{$baseNewsAdmin_URI}?mode=subList"; 
            }
        }

        //-->
        </script>
EOT;

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Connect to the database
        mynews_connect();

        // Build and execute our query
        $query  = '
            SELECT
                artnr as sid,
                title,
                author,
                email,
                date
            FROM
                ' . $myNewsConf['db']['table']['submissions'] . '
            ORDER by
                artnr
            DESC';
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        // only continue if $sqlErr is empty
        if(!$sqlErr){
            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>';
            $output.= "\n\t" . '<table border=0 width="95%">';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top">&nbsp;</td>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>Title:</b></td>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>Author:</b></td>';
            $output.= "\n\t\t" . '</tr>';

            while($row = mysql_fetch_assoc($result)){
                extract($row);
                $output.= "\n\t\t" . '<tr>';
                $output.= "\n\t\t\t" . '<td valign="top" width="10%" nowrap>';
                $output.= "\n\t\t\t\t";
                $output.= '(<a href="' . $baseNewsAdmin_URI . '?mode=subEdit&sid=' . $sid . '">Submit</a>';
                $output.= "\n\t\t\t\t" . ' | ';
                $output.= "\n\t\t\t\t";
                $output.= '<a href="javascript:confirmDelete(\'' . $baseNewsAdmin_URI . '?mode=subDeleted&sid=' . $sid . "&title=" . base64_encode($title) . "','" . addslashes($title) . '\')">Delete</a>)';
                $output.= "\n\t\t\t" . '</td>';
                $output.= "\n\t\t\t" . '<td valign="top">' . $title . '</td>';
                $output.= "\n\t\t\t" . '<td valign="top"><a href="mailto:' . $email . '">' . $author . '</a>';
                $output.= "\n\t\t" . '</tr>';
            }

            $output.= "\n\t" . '</table>';
            $output.= "\n\t" . '</blockquote>';

            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Submissions';
        }
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function subEdit(){
/**
 * This function prints out a form with the populated submission fields
 * for editing and posting the submission.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Connect to the database
        mynews_connect();

        // Build and execute the query
        $query  = '
            SELECT
                artnr as sid,
                title,
                author,
                previewtext as copy,
                email,
                date,
                section
            FROM
                ' . $myNewsConf['db']['table']['submissions'] . '
            WHERE
                artnr = ' . addslashes($_GET['sid']);

        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
            $row    = mysql_fetch_assoc($result);
            extract($row);

            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>';
            $output.= "\n\t" . '<form method="post" action="' . $baseNewsAdmin_URI . '?mode=subPosted">';
            $output.= "\n\t" . '<table border=0 width="95%">';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>1)</b> Title:</td>';
            $output.= "\n\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="title" size="' . $tWidth . '" value="' . $title . '">';
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t" . '</tr>';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>2)</b> Section:</td>';
            $output.= "\n\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t" . '<select class="textbox" name="section">';

            $sectionHash    = _getSections();
            $sectionKeys    = array_keys($sectionHash['data']);
            foreach($sectionKeys as $sectionKey){
                $output.= "\n\t\t\t\t\t" . '<option value="' . $sectionKey . '">&nbsp;-&nbsp;' . $sectionKey . '</option>';
            }

            $output.= "\n\t\t\t\t\t" . '<option value="Contributed" selected>&nbsp;-&nbsp;Contributed</option>';
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t" . '</tr>';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>3)</b> Active:</td>';
            $output.= "\n\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="1"> Yes ';
            $output.= "\n\t\t\t\t" . '<br />';
            $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="0" checked> No ';
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t" . '</tr>';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td valign="top"><b>4)</b> Text:</td>';
            $output.= "\n\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t" . '<textarea name="copy" cols="' . $taCols . '" rows="' . $taRows . '" wrap="virtual">' . $copy . '</textarea>';
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t" . '</tr>';
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td>';
            $output.= "\n\t\t\t\t" . '&nbsp;';
            $output.= "\n\t\t\t\t" . '<input type="hidden" name="author" value="' . $author . '">';
            $output.= "\n\t\t\t\t" . '<input type="hidden" name="postid" value="' . $_SESSION['valid_user'] . '">';
            $output.= "\n\t\t\t\t" . '<input type="hidden" name="email" value="' . $email . '">';
            $output.= "\n\t\t\t\t" . '';
            $output.= "\n\t\t\t" . '</td>';
            $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
            $output.= "\n\t\t" . '</tr>';
            $output.= "\n\t" . '</table>';
            $output.= "\n\t" . '</form>';
            $output.= "\n\t" . '</blockquote>';

            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : <a href="' . $baseNewsAdmin_URI . '?mode=subList">Submissions</a> : Edit : <small>' . $title . '</small>';
        }
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function subPosted(){
/**
 * This function submits the data posted from the subEdit form into the
 * database as a new item in the news table.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Build the Editor's Note.
        $ednote = 'Originally submitted by: <a href="mailto:' . $_POST['email'] . '">' . $_POST['author'] . '</a>';

        // Connect to the database
        mynews_connect();

        // Build and execute our query.
        $query  = '
            INSERT into
                ' . $myNewsConf['db']['table']['news'] . "
            VALUES(
                '',
                '" . addslashes($_POST['title'])    . "',
                '" . addslashes($_POST['copy'])     . "',
                '" . addslashes($ednote)            . "',
                '" . addslashes($_POST['postid'])   . "',
                '',
                '" . date('Y-m-d H:i:s')            . "',
                '" . addslashes($_POST['section'])  . "',
                '" . addslashes($_POST['active'])   . "',
                '')";
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>';
            $output.= "\n\t\t" . '<b>"' . $_POST['title'] . '"</b> has been successfully submitted to the news database';
            $output.= "\n\t" . '</blockquote>';

            $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '?mode=subList">';
            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : <a href="' . $baseNewsAdmin_URI . '?mode=subList">Submissions</a> : Posted : <small>' . $_POST['title'] . '</small>';
        }
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function subDeleted(){
/**
 * This function deletes the submission from the queue.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // connect to the database
        mynews_connect();

        // Build and execute the query.
        $query  = '
            DELETE
            FROM
                ' . $myNewsConf['db']['table']['submissions'] . '
            WHERE
                artnr = ' . addslashes($_GET['sid']);

        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
             
            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>';
            $output.= "\n\t\t" . '<b>"' . base64_decode($_GET['title']) . '"</b> successfully removed from the database.';
            $output.= "\n\t" . '</blockquote>';

            $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '?mode=subList">';
            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : <a href="' . $baseNewsAdmin_URI . '?mode=subList">Submissions</a> : Deleted : <small>' . base64_decode($_GET['title']) . '</small>';
        }
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
?>
