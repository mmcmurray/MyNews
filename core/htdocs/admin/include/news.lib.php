<?
/* $Id: news.lib.php 495 2005-09-16 14:12:09Z alien $ */

/*******************************************************************/
function newsAdmin(){
/**
 * The purpose of this function is to print out the News admin list.
 */
global $myNewsConf;

    $baseAdmin_URI  = $myNewsConf['path']['web']['admin'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    $title      = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : News : Select Tool';
    $output     = loginInfo();
    $output    .= <<<EOT

    <ul>
        <li><a href="$baseAdmin_URI{$myNewsConf['adminScripts']['news']}?mode=nAdd">New News Item</a>
        <li><a href="$baseAdmin_URI{$myNewsConf['adminScripts']['news']}?mode=nList">Modify/Delete News Item.</a>
        <li><a href="$baseAdmin_URI{$myNewsConf['adminScripts']['news']}?mode=cmSelect">Modify/Delete Comments.</a>
        <li><a href="$baseAdmin_URI{$myNewsConf['adminScripts']['news']}?mode=subList">Modify/Delete Submissions.</a>
        <li><a href="$baseAdmin_URI{$myNewsConf['adminScripts']['news']}?mode=secList">Modify News Sections.</a>
    </ul>


EOT;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
// News Functions
/*******************************************************************/
function newsAdd(){
/**
 * The purpose of this function is to provide a form for the author to
 * fill out to submit new news items
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    $title      = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Add';


    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
          with(document.the_form){
            if(section.value == 'select'){
              alert("Please choose a section for this item. \\n or enter a new section name");
              return false;
            }

            if(section.value == 'none' && new_section.value == '' ){
              alert("Please enter a new section name.");
              return false;
            }
          }
          return true;
        }

        //-->
        </script>
EOT;

    $output     = loginInfo();
    $output    .= <<<EOT
        <blockquote>
            <form action="$baseNewsAdmin_URI?mode=nAdded" method="post" name="the_form" onSubmit="return isOK()">
            <table border=0 width="95%">
                <tr>
                    <td valign="top"><b>1)</b> Section:</td>
                    <td>
                        <select class="textbox" name="section" OnChange="if(this.value) { this.form.new_section.value = ''} else { this.select(); }">
                            <option value="select">-- Select</option>
                            <option value="none">-- Create</option>

EOT;

    $sectionHash    = _getSections();
    $sectionKeys    = array_keys($sectionHash['data']);
    foreach($sectionKeys as $sectionKey){
        $output.= "\t\t\t\t\t" . '<option value="' . $sectionKey . '">&nbsp;-&nbsp;' . $sectionKey . '</option>' . "\n";
    }

    $output    .= <<<EOT
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input class="textbox" type="text" name="new_section" size="$tWidth" value="-New Section-" maxlength="13">
                        <br />
                        <small><b>Note:</b> <i>You only need to complete the above field if you are creating a new section</i></small>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><b>2)</b> Title:</td>
                    <td>
                        <input class="textbox" type="text" name="title" size="$tWidth">
                    </td>
                </tr>
                <tr>
                    <td valign="top"><b>3)</b> Text:</td>
                    <td>
                        <textarea NAME="copytxt" COLS=$taCols ROWS=$taRows WRAP="virtual"></TEXTAREA>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="hidden" name="author" value="{$_SESSION['valid_user']}">
                        <input type="hidden" name="email" value=''>
                        {$myNewsConf['button']['submit']}
                    </td>
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
function newsAdded(){
/**
 * The purpose of this function is to insert the submitted form
 * data into the database.
 */
global $myNewsConf;

    // Just a couple of definitions beforehand.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    // Connect to the MyNews database;
    mynews_connect();

    // Check to see if we are creating a new section.  And add it to
    // the section table if we are.
    if($_POST['section'] == 'none'){
        $_POST['section'] = $_POST['new_section'];
        $insSection = '
            INSERT into
                ' . $myNewsConf['db']['table']['sections'] . "
            VALUES(
                    'NULL',
                    '" . addslashes($_POST['section']) . "',
                    '0')";

        $result     = mysql_query($insSection);
    }

    // Strip out or fix any broken "M$ Word" special chars
    // that come from copy/paste from "M$ Word"
    $_POST['title']     = fixWordShit($_POST['title']);
    $_POST['copytxt']   = fixWordShit($_POST['copytxt']);

    // Set our insert date.
    $insDate    = date('Y-m-d H:i:s');

    // Build our Query
    $insQuery   = ('
        INSERT into ' . $myNewsConf['db']['table']['news'] . "
            values( '',
                    '" . addslashes($_POST['title'])    . "',
                    '" . addslashes($_POST['copytxt'])  . "',
                    '',
                    '" . addslashes($_POST['author'])   . "',
                    '',
                    '" . $insDate                       . "',
                    '" . addslashes($_POST['section'])  . "',
                    '1',
                    '')");

    $result = mysql_query($insQuery);
    $sqlErr = myNewsChkSqlErr($result,$insQuery);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // If $sqlErr is empty, we can hand back a success message.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Added';
    $output = loginInfo();
    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . 'The item <b>' . $_POST['title'] . '</b> has been added to the database.';

    // Set a meta refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '">';

    $returnArray['meta']    = $meta;
    $returnArray['content'] = $output;
    $returnArray['title']   = $title;

return $returnArray;
}
/*******************************************************************/
function newsList(){
/**
 * The purpose of this function is to display a list of items that exist
 * in the news table, and provide an option to either edit or delete them.
 */
global $myNewsConf;

    // Just a couple of definitions beforehand.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author');
    if($errorArray) return $errorArray;

    // Output the javascript form checks.
    $jscript    = <<<EOT
        <script language="JavaScript">
        <!--

        function isOK(){
            with(document.the_form){
                if( sid.value == "" ){
                    alert("To use this function, you have to input an article number.");
                    return false;
                }
            }
            return true;
        }

        function confirmDelete(URL,SHOW,TITLE){
            where_to= confirm("Do you really want to delete the item:\\n\\n" + TITLE);
            if (where_to== true) {
                window.location=URL;
            } else {
                window.location="{$baseNewsAdmin_URI}?mode=nList&show=" + SHOW;
            }
        }

        //-->
        </script>
EOT;

    // If $_GET['show'] is not set, we set it to 0
    if(!$_GET['show']) $_GET['show'] = 0;

    // Set an empty $wClause.
    $wClause    = '';

    // If the author status is set to Author, we set a where clause ($wClause)
    if($_SESSION['status'] == 'Author') $wClause    = "WHERE author = '" . $_SESSION['valid_user'] . "'";

    // Connect to the database;
    mynews_connect();

    // Build our Query.
    $query  = ('
        SELECT
            artnr as sid,
            title,
            section,
            active,
            author
        FROM
            ' . $myNewsConf['db']['table']['news'] . "
        $wClause
        ORDER by
            artnr desc
        LIMIT
            " . $_GET['show'] . ",
            " . $myNewsConf['default']['limit']);

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $output = loginInfo();

    // Open the table, and output the Column Titles.
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form method="GET" action="' . $baseNewsAdmin_URI . '" name="the_form" onSubmit="return isOK()">';
    $output.= "\n\t" . '<small>Input and Article ID to jump to that article:</small>';
    $output.= "\n\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t" . '<input type="hidden" name="mode" value="nEdit">';
    $output.= "\n\t" . '<input class="textbox" type="text" size="2" name="sid">';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td><b>Title:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Section:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Author:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Active:</b></td>';
    $output.= "\n\t\t" . '</tr>';

    while($row  = mysql_fetch_assoc($result)){
        extract($row);

        if($active == 0) $active = 'no';
        if($active == 1) $active = 'yes';

        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td nowrap valign="top">';
        $output.= "\n\t\t\t\t" . '(<a href="' . $baseNewsAdmin_URI . '?mode=nEdit&sid=' . $sid . '">Edit</a>';
        $output.= "\n\t\t\t\t" . '|';
        $output.= "\n\t\t\t\t" . "<a href=\"javascript:confirmDelete('" . $baseNewsAdmin_URI . "?mode=nDeleted&sid=" . $sid . "&author=" . $author . "&title=" . base64_encode($title) . "','" . $_GET['show'] . "','" . addslashes($title) . "')\">Delete</a>)";
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">' . $title . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">' . $section . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">' . $author . '</td>';
        $output.= "\n\t\t\t" . '<td valign="top">' . $active . '</td>';
        $output.= "\n\t\t" . '</tr>';
    }

    // Close out the table.
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '<p align="center">';

    // Set our title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Select Option';

    // Build our navigation.
    $query  = ('
        SELECT
            count(*) as total 
        FROM
            ' . $myNewsConf['db']['table']['news'] . '
        ' . $wClause);

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
     extract($row);

    $next   = $_GET['show'] + $myNewsConf['default']['limit'];
    $back   = $_GET['show'] - $myNewsConf['default']['limit'];

    // If $_GET['show'] is zero, we need to change it to a one, for
    // readability.
    if($_GET['show'] == 0) $_GET['show'] = 1;

    // $vor is $total by default.
    $vor    = $total;

    // If we are not on the last page, set $vor to $next.
    if($next < $total) $vor = $next;
    
    // Output where we are in english.
    $output .= '<small>[' . $_GET['show'] . ' - ' . $vor . ' of ' .  $total . ']</small> <br />';

    if($back >= 0)      $output.= '<a href="' . $baseNewsAdmin_URI . '?mode=nList&show=' . $back . '">' . $myNewsConf['button']['back'] . '</a>';
    if($back >= 0 && $next < $total) $output.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    if($next < $total)  $output.= '<a href="' . $baseNewsAdmin_URI . '?mode=nList&show=' . $next . '">' . $myNewsConf['button']['next'] . '</a>';

    // Close out the blockquote.
    $output.= '</blockquote>';

    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function newsEdit($sid){
/**
 * The purpose of this function is to provide a form view of a selected
 * news item from the news table.  This will provide the author the
 * ability to edit the news item.
 */
global $myNewsConf;

    // Just a couple of definitions beforehand.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Predefined sizes for text and textarea form fields.
    $taCols     = $myNewsConf['form']['textarea']['cols'];
    $taRows     = $myNewsConf['form']['textarea']['rows'];
    $tWidth     = $myNewsConf['form']['text']['width'];

    // Connect to the database
    mynews_connect();

    // Build and execute our query
        $query  = ('
        SELECT
            *
        FROM
            ' . $myNewsConf['db']['table']['news'] . '
        WHERE
            artnr = ' . $sid); 

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
     extract($row);

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$author);
    if($errorArray) return $errorArray;

    // Begin Building output.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<form action="' . $baseNewsAdmin_URI . '?mode=nEdited&sid=' . $sid . '" method="POST">';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="left" valign="top">';
    $output.= "\n\t\t\t\t" . '<b>1)</b> Section:';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<select class="textbox" name="section">';

    // Get a list of available sections, and display them.
    $sectionHash    = _getSections();
    $sectionKeys    = array_keys($sectionHash['data']);
    foreach($sectionKeys as $sectionKey){
        $isSelect   = '';
        if($sectionKey == $section) $isSelect = 'selected';
        $output.= "\n\t\t\t\t\t" . '<option value="' . $sectionKey . '" ' . $isSelect . '>&nbsp;-&nbsp;' . $sectionKey . '</option>';
    }

    //Set whether the story is active or not
    $yCheck = '';
    $nCheck = '';
    if($active == 0) $nCheck = ' checked';
    if($active == 1) $yCheck = ' checked';

    // Continue building output.
    $output.= "\n\t\t\t\t" . '</select>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="left" valign="top">';
    $output.= "\n\t\t\t\t" . '<b>2)</b> Active:';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="1"' . $yCheck . '>Yes';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '<input type="radio" name="active" value="0"' . $nCheck . '>No';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="left" valign="top">';
    $output.= "\n\t\t\t\t" . '<b>3)</b> Title:';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" type="text" name="title" size="' . $tWidth . '" value="' . $title . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="left" valign="top">';
    $output.= "\n\t\t\t\t" . '<b>4)</b> Text:';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<textarea name="copytext" cols="' . $taCols . '" rows="' . $taRows . '" wrap="virtual">' . $previewtext . '</textarea>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr> ';

    if($_SESSION['status'] == 'Editor' || $_SESSION['status'] == 'Admin'){
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="top">';
        $output.= "\n\t\t\t\t" . '<b>5)</b> Editor\'s Note:';
        $output.= "\n\t\t\t\t" . '<br />';
        $output.= "\n\t\t\t\t" . '<small>(Appears at the top of story)</small>';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . '<textarea name="ednote" cols="' . $taCols . '" rows="5" wrap="virtual">' . $ednote . '</textarea>';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t" . '</tr>';
    } else {
        $output.= "\n\t\t" . '<input type="hidden" name="ednote" value="OLDEDNOTE">';
    }

    $output.= "\n\t\t" . '<input type="hidden" name="author" value="' . $author . '">';
    $output.= "\n\t\t" . '<input type="hidden" name="email" value="' . $email . '">';
    $output.= "\n\t\t" . '<input type="hidden" name="date" value="' . $date . '">';
    $output.= "\n\t\t" . '<input type="hidden" name="viewcount" value="' . $viewcount . '">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '<form>';
    $output.= "\n\t" . '</blockquote>';

    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : <small>' . $title . '</small>';

    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function newsEdited(){
/**
 * The purpose of this function is to insert the changes made from 
 * newEdit() into the database.
 */
global $myNewsConf;

    // Just a couple of definitions beforehand.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$_POST['author']);
    if($errorArray) return $errorArray;

    // Connect to the database;
    mynews_connect();

    // If $_POST['ednote'] is OLDEDNOTE, we need to query the database
    // to get the old value, so we can reinsert it.
    if($_POST['ednote'] == 'OLDEDNOTE'){
        $query  = ('SELECT ednote from ' . $myNewsConf['db']['table']['news'] . ' WHERE artnr = ' . $_GET['sid']);
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        // Return with an error if it exists.
        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        $row                = mysql_fetch_assoc($result);
        $_POST['ednote']    = $row['ednote'];
    }

    $query  = ('
        REPLACE into
            ' . $myNewsConf['db']['table']['news'] . "
        values(
            '" . addslashes($_GET['sid'])       . "',
            '" . addslashes($_POST['title'])    . "',
            '" . addslashes($_POST['copytext']) . "',
            '" . addslashes($_POST['ednote'])   . "',
            '" . addslashes($_POST['author'])   . "',
            '" . addslashes($_POST['email'])    . "',
            '" . addslashes($_POST['date'])     . "',
            '" . addslashes($_POST['section'])  . "',
            '" . addslashes($_POST['active'])   . "',
            '" . addslashes($_POST['viewcount']). "')");

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;
         
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '?mode=nList">';

    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'The Item <b>' . $_POST['title'] . '</b> has been updated as item number: <b>' . $_GET['sid'] . '</b>.';
    $output.= "\n\t" . '</blockquote>';

    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edited : <small>' . $title . '</small>';

    $returnArray['meta']    = $meta;
    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function newsDeleted(){
/**
 * The purpose of this function is to remove the selected news item
 * from the database.
 */
global $myNewsConf;

    // Just a couple of definitions beforehand.
    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    // Check to see if the currently logged in user exists in the defined
    // groups.  If not, return with an error.
    $errorArray = chkPerm('Admin:Editor:Author',$_GET['author']);
    if($errorArray) return $errorArray;

    // Open the connection to the database.
    mynews_connect();

    // Delete all comments associated with this story first.
    $query  = '
        DELETE
        FROM
            ' . $myNewsConf['db']['table']['comments'] . '
        WHERE
            artnr = ' . addslashes($_GET['sid']);
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Now that we know there are no errors, let's go ahead and delete
    // the article.
    $query  = ('
        DELETE
        FROM
            ' . $myNewsConf['db']['table']['news'] . '
        WHERE
            artnr = ' . $_GET['sid']);
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // We want our success page to refresh back to the News admin page.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '?mode=nList">';

    // Build our output text.
    $output = loginInfo();
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . 'The Item <b>"' . base64_decode($_GET['title']) . '"</b> and all comments associated with it have been deleted.';
    $output.= "\n\t" . '</blockquote>';

    // Set our title.
    $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Deleted : <small>' . base64_decode($_GET['title']) . '</small>';

    $returnArray['error']   = $sqlErr;
    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
// Misc Functions
/*******************************************************************/
function _getSections(){
/**
 * The purpose of this function is to return an array of section that
 * exist in the myNews database
 */
global $myNewsConf;

    mynews_connect();
    $query   = ('
        SELECT
            distinct(section)
        FROM
            ' . $myNewsConf['db']['table']['news'] . '
        ORDER by section');

    $result = mysql_query($query);
    $output.= myNewsChkSqlErr($result, $query);

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $sectionHash[$section] = 0;
    }

    $returnArray['content'] = $output;
    $returnArray['data']    = $sectionHash;

return $returnArray;
}
?>
