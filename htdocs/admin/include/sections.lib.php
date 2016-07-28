<?
/* $Id: sections.lib.php 320 2004-07-29 22:03:19Z alien $ */

/*******************************************************************/
function secList(){
/**
 * This function outputs a form with a list of sections
 * within the the system and provides a check list of sections
 * that will exist on the main page
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Connect to the database.
        mynews_connect();

        // Build and execute the query.
        $query  = '
            SELECT
                secid as sid,
                section,
                front
            FROM
                ' . $myNewsConf['db']['table']['sections'] . '
            ORDER by
                section';
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>'; 
            $output.= "\n\t" . 'Select the Sections you would like to be presented on the main page.';
            $output.= "\n\t" . '<br />'; 
            $output.= "\n\t" . '<br />'; 
            $output.= "\n\t" . '<form action="' . $baseNewsAdmin_URI . '?mode=secEdited" method="post">';
            $output.= "\n\t" . '<table border=0 width="95%">'; 
            $output.= "\n\t\t" . '<tr>'; 

            while($row = mysql_fetch_assoc($result)){
                extract($row);

                unset($checked);
                if($front == 1) $checked = ' checked';

                $output.= row_place($i);

                $output.= "\n\t\t\t" . '<td valign="top" align="left">';
                $output.= "\n\t\t\t\t" . '<input class="textbox" type="checkbox" name="' . $sid . '"' . $checked . '> ' . $section;
                $output.= "\n\t\t\t" . '</td>';

                $i++;
            }
            $output.= "\n\t\t" . '</tr>'; 
            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td colspan=2>' . $myNewsConf['button']['submit'] . '</td>';
            $output.= "\n\t\t" . '</tr>'; 
            $output.= "\n\t" . '</table>'; 
            $output.= "\n\t" . '</form>'; 
            $output.= "\n\t" . '</blockquote>'; 

            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Sections';
        }
    }

    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function secEdited(){
/**
 * This function takes the information submitted from secList() and 
 * inputs it into the database.
 */
global $myNewsConf;

    $baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
    $baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

    if($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
        $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Edit : Error';
        $output = myNewsError(110,'You have insufficient creditentials');
    } else {
        // Connect to the database;
        mynews_connect();

        // Build and execute the query.
        $query  = '
            SELECT
                secid as sid,
                section
            FROM
                ' . $myNewsConf['db']['table']['sections'] . '
            ORDER by
                section';
        $result = mysql_query($query);
        $sqlErr = myNewsChkSqlErr($result, $query);

        if(!$sqlErr){
            $output = loginInfo();
            $output.= "\n\t" . '<blockquote>';
            $output.= "\n\t\t" . 'The following sections will now show up on the front page.';
            while($row = mysql_fetch_assoc($result)){
                extract($row);
                $set    = 0;
                if($_POST[$sid] == 'on'){
                    $set = 1;
                    $output.= "\n\t\t" . '<br />';
                    $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    $output.= "\n\t\t" . '<b>' . $section . '</b>';
                }
                $query  = '
                    UPDATE
                        ' . $myNewsConf['db']['table']['sections'] . '
                    SET
                        front = ' . $set . '
                    WHERE
                        secid = ' . $sid;
                $insert = mysql_query($query);
                $sqlErr.= myNewsChkSqlErr($insert, $query);
            }
            $output.= "\n\t" . '</blockquote>';

            $title  = '<a href="' . $baseAdmin_URI . '">Site Administration</a> : <a href="' . $baseNewsAdmin_URI . '">News</a> : Sections : Updated';
            $meta   = '<meta http-equiv="Refresh" content="2; url=' . $baseNewsAdmin_URI . '?mode=secList">';
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
