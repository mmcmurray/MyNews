<?php
/* $Id: blocks.lib.php 409 2004-08-09 18:03:48Z alien $ */
/********************************************************************/
function mynews_last($view) {
/**
 * This function prints out the 10 newest items added
 * added to the database, and links each of them to the
 * story_script.
 *
 * Ex:     mynews_last(10);
 */
global $myNewsConf;

    $baseStory_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    
    // Connect to the Database
    mynews_connect();

    $query = '
        SELECT
            a.artnr as sid,
            a.title as title,
            sum(if(b.artnr is NULL,0,1)) as cmcount
        FROM
            ' . $myNewsConf['db']['table']['news'] . ' as a
        LEFT JOIN
            ' . $myNewsConf['db']['table']['comments'] . " as b
        ON  
            a.artnr = b.artnr
        AND
            b.type  = 'news'
        WHERE
            active  = 1
        GROUP by 1
        ORDER by a.artnr desc
        LIMIT " .  $view;

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $output.= "\n\t" . '<!-- Begin Top Ten -->';

    while ($row  =  mysql_fetch_assoc($result)) {
        extract($row);
        $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&middot;&nbsp;';
        $output.= "\n\t\t" . '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '" title="' . $title . '">' . string_cut($title,2) . '</a>';
        $output.= "\n\t\t" . '<small>(' . $cmcount . ')</small>';
        $output.= "\n\t\t" . '<br />';
    }

    $output.= "\n\t" . '<!-- End Top Ten -->';

    // Build the title
    $title  = 'Last 10 Articles:';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function mynews_sections() {
/**
 * This function prints out each section listed in the database
 * ordered by the sections "id" number.
 *
 * Ex:    mynews_sections($list);
*/
global $myNewsConf;

    $base_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'];

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            distinct(section),
            count(*) as count
        FROM
            ' .  $myNewsConf['db']['table']['news'] . '
        WHERE 
            active = 1
        GROUP by section
        ORDER by section';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $output = "\n\t" . '<!-- Begin Sections -->';

    while ($row = mysql_fetch_assoc($result)) {
        extract($row);

        $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&middot;&nbsp;';
        $output.= "\n\t\t" . '<a href="' . $base_URI . '?section=' . urlencode($section) . '">' . $section . '</a> <small>(' . $count . ')</small>';
        $output.= "\n\t\t" . '<br />';
    }

    $output.= "\n\t" . '<!-- End Sections -->';

    $title  = 'Sections:';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
?>
