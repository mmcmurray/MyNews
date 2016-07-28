<?php
/* $Id: lib.inc.php 505 2005-09-27 20:25:54Z alien $ */
/********************************************************************/
/**
 * This is the function library for the MyNews backend
 * In it I will document each of the functions and explain
 * a general use of these functions so you can incorporate    
 * these functions into your website with little to no design
 * limitations                    
 */
                    
/**
 *    @author       Mike McMurray <alien@alienated.org>
 *    @project      MyNews
 */
/********************************************************************/
function mynews_connect() {
/**
 * Quite possibly the most crucial function in this library.
 * This function establishes the connection to the database
 * to Allow for the other function to perform their queries.

 * Ex:     mynews_connect()
 */
global $myNewsConf;
    mysql_connect($myNewsConf['db']['hostname'], $myNewsConf['db']['dbUser'], $myNewsConf['db']['dbPass']);
    mysql_select_db($myNewsConf['db']['dbName']);
}
/********************************************************************/
function top_story($section) {
/**
 * This function displays the latest headline in it's     
 * preview form.  With a [More...] button to view the
 * whole story.  It require you provide the section 
 * that it is pulling it's story from.            
 *                     
 * Ex:    top_story(news);            
 */
global $myNewsConf;

    // Assign some needed variables ahead of time.
    $baseStory_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseAbout_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about'];
    $baseArchive_URI    = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'];
    $baseComment_URI    = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];

    // Connect to the DB
    mynews_connect();

    // Build our SQL
    $query  = '
        SELECT
            a.artnr as sid,
            a.author as author,
            a.title as title,
            a.previewtext as previewTxt,
            sum(if(b.artnr is NULL,0,1)) as cmcount 
        FROM
            ' . $myNewsConf['db']['table']['news'] . ' as a
        LEFT JOIN
            ' . $myNewsConf['db']['table']['comments'] . " as b
        ON a.artnr=b.artnr
        AND b.type = 'news'
        WHERE a.section='" . $section . "' and a.active='1'
        GROUP by 1
        ORDER by a.artnr desc
        LIMIT 1";

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Insert our returned data into a hash.
    $row = mysql_fetch_assoc($result);
     extract($row);

    // Word count stuff (not 100% accurate, but damn close)
    $introcount = str_replace('  ', ' ', $previewTxt);
    $introcount = strip_tags($introcount);
    $introcount = explode(" ", $introcount);
    $fullcount  = count($introcount);
    
    $paragraph  = explode("\r\n\r\n",$previewTxt);
    $pgcount    = count($paragraph);

    // Here we want to rip out the first 100 words of the
    // article text to display on the front page.
    // This is a nasty hack that doesn't check for html
    // so if you can think of a better way.  Lemme know.
    $previewTxt = string_cut($previewTxt,1,'paragraph');

    // Change all \r\n (line breaks) coming out of the db
    // to HTML read <br />
    $previewTxt = make_clickable($previewTxt);
    $previewTxt = mynews_format($previewTxt);
    $cryptTitle = base64_encode($title);

    if ($cmcount != '0') {
    // Check to see if comments exist.  Add the following text to
    // our output.
        $trans = $cmcount . ' comments and';
    } else {
    // Otherwise, leave it empty
        $trans = '';
    }

    $output.= "\n\t" . '<table border=0 width="100%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td align="left" nowrap width="70%" valign="top">';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp';
    $output.= "\n\t\t\t\t" . '<small>Posted by: <a href="' . $baseAbout_URI . '?mode=zoom&author=' . $author . '">' . $author . '</a></small>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td align="right">';
    $output.= "\n\t\t\t\t" . '<b>&raquo;</b>&nbsp;&nbsp;<a href="' . $baseArchive_URI . '?section=' . urlencode($section) . '">' . $section . '</a>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . '<p>';
    $output.= "\n\t\t" . $previewTxt;
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp';
    $output.= "\n\t\t" . '<b>{</b>';

    // If more then one paragraph exists, or there are comments to the item
    // print out a "More" link
    if ($pgcount > 1 || $cmcount > 0) {
        $output.= "\n\t\t" . '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '">More</a> -';
        $output.= "\n\t\t" . '<i>' . $trans . ' ' . $fullcount . ' words in body</i>';
    // Otherwise, just display a "comment" link
    } else {
        $output.= "\n\t\t" . '<a href="' . $baseComment_URI . '?mode=compose&tid=' . $sid . '&other=news&parent=0&title=' . $cryptTitle . '">Comment</a>';
    }

    $output.= "\n\t\t" . '<b>}</b>';
    $output.= "\n\t" . '</blockquote>';

    // return an array of our output data so we can manipulate the output a little easier.
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function headlines($section,$show,$count) {
/**
 * This function displays the previous five headlines    
 *                     
 * Ex:    headlines(news,10);            
 */
global $myNewsConf;

    // Assign some needed variables ahead of time.
    $baseStory_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseArchive_URI    = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'] . '?section=' . urlencode($section);

    if (!$count) $count = $myNewsConf['default']['count'];
    if (!$show) $show = 1;

    $limit = 1;

    // Connect to the database
    mynews_connect();

    // Build the query
    $query = ("
        SELECT
            a.artnr as artnr,
            a.author as author,
            a.title as title,
            sum(if(b.artnr is NULL,0,1)) as cmcount 
        FROM
            " . $myNewsConf['db']['table']['news'] . " as a
        LEFT JOIN " . $myNewsConf['db']['table']['comments'] . " as b
        ON a.artnr=b.artnr
        AND b.type = 'news'
        WHERE a.section='$section' AND a.active='1'
        GROUP by 1
        ORDER by a.artnr desc
        LIMIT $show,$count");
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $rowCnt = mysql_num_rows($result);
    if(!$rowCnt) return;

    $output.= "\n\t" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t" . '<b>[</b>';
    $output.= "\n\t" . '<a href="' . $baseArchive_URI . '?section=' . urlencode($section) . '">Archived ' . $section . '</a>';
    $output.= "\n\t" . '<b>]</b>';
    $output.= "\n\t" . '<ul>';

    // Loop through our returned data and stuff it into a hash to be read out.
    while($row = mysql_fetch_assoc($result)) {
        extract($row);

        $output.= "\n\t\t" . '<li>';
        $output.= "\n\t\t\t" . '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $artnr . '">' . $title . '</a>';
        $output.= "\n\t\t\t" . '<small>(' . $cmcount . ') - ' . $author . '</small>';
        $output.= "\n\t\t" . '</li>';
    }

    $output .= "\n\t" . '</ul>';

    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function storyView($sid,$page) {
/**
 * This function prints out the full text and information    
 * of the article and page selected                
 *            
 * Ex:    storyView($id,$page);        
 */
global $myNewsConf;

    // Assign some needed variables ahead of time.
    $baseStory_URI  = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseAbout_URI  = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about'];
    $baseArch_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'];
    $baseImage_URI  = $myNewsConf['path']['web']['index'] . 'images';
    $next_button    = $myNewsConf['button']['next'];
    $back_button    = $myNewsConf['button']['back'];

    // Connect to the database.
    mynews_connect();

    // Build the query.
    $query =  ("
        SELECT
            *,
            unix_timestamp(date) as timestamp
        FROM " . $myNewsConf['db']['table']['news'] . "
        WHERE artnr='$sid'");

    // Run the query
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Fetch the data that has been returned, and stuff it into a hash.
    $row = mysql_fetch_assoc($result);
    extract($row);

    // Set the $cryptTitle var for passing via GET string a little later.
    $cryptTitle = base64_encode($title);

    // Here we look for [caption][/caption] tags to see whether we want to
    // Insert a table for the caption.
    $previewtext    = preg_replace("/\[caption\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\1'),'right','150')", $previewtext);
    $previewtext    = preg_replace("/\[caption align=\"(.+?)\"\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\2'),'\\1','150')", $previewtext);

    // We do some standard text manipulation here:
    // We need to check for URL's that haven't already
    // been used as links, and turn them into <HREF>'s

    // Also we want to turn newlines into HTML <br />'s

    // Finally we turn all double spaces into HTML &nbsp;'s
    // to get proper formatting on our articles
    $previewtext    = make_clickable($previewtext);
    $previewtext    = mynews_format($previewtext);
    $previewtext    = stopSpam($previewtext);

    // This is where we determine if there are multiple pages
    // in the database BLOB we suck out, and how many there
    // are.
    $fulltext   = spliti($myNewsConf['default']['pagebreak'], $previewtext);

    $pagecount  = count($fulltext);

    // Here we take our date that we pulled out of the database
    // as a unix_timestamp and convert it to something that
    // is human readable.
    $date   = date($myNewsConf['format']['date']['default'],$timestamp);

    // If the page is not passed through the function call, we
    // need to set it
    if (!isset($page)) $page = '1';

    // PLEASE COMMENT
    if ($pagecount == '1') {
        $pageButton = '';
        $nextPage   = '';
        $prevPage   = '';
    } else {
        $previous   = ($page - 1);
        $next       = ($page + 1);

        $prevPage   = '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '&page=' . $previous . '">' . $back_button . '</a>';
        $nextPage   = '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '&page=' . $next . '">' . $next_button . '</a>';
    }

    // PLEASE COMMENT
    if ($page == '1') {
        $pageButton = '<br /><br /><div align="center">' . $nextPage . '</div>';
    } 
    elseif ($page == $pagecount) {
        $pageButton = '<br /><br /><div align="center">' . $prevPage . '</div>';
    } else {
        $pageButton = '<br /><br /><div align="center">' . $prevPage;
        $pageButton.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $pageButton.= $nextPage . '</div>';
    }

    // Build the return Title.
    $title  = $section . ' : ' . $title . ' <small>(' . $viewcount . ' story views)</small>';

    // Build our content output.
    $output.= "\n\t" . '<table border=0 width="100%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '<a href="' . $baseStory_URI . '?mode=storyPrint&sid=' . $sid . '"><img src="' . $baseImage_URI . '/print.gif" border="0" alt="Printer Friendly Format"></a>';
    $output.= "\n\t\t\t\t" . '<a href="' . $baseStory_URI . '?mode=storyForm&sid=' . $sid . '&title=' . $cryptTitle . '"><img src="' . $baseImage_URI . '/friend.gif" border="0" alt="Mail this Story"></a>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . 'Posted by: <a href="' . $baseAbout_URI . '?mode=zoom&author=' . $author . '">' . $author . '</a> on ' . $date;
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t\t" . '<td valign="top" align="right">';
    $output.= "\n\t\t\t\t" . '<b>Page ' . $page . ' of ' . $pagecount . '</b>';
    $output.= "\n\t\t\t\t" . '<br />';

    // The following outputs a list of page numbers with a link to each page
    // that is not currently selected, provided there are more then one page.
    if ($pagecount != '1') {
        for($i = 1; $i <= $pagecount ; $i++){
            if ($i == $page) {
                $output.= '<b>[<u>' . $i . '</u>]</b>';
            } elseif ($i == '1') {
                $output.= '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '">[' . $i . ']</a>';
            } else {
                $output.= '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '&page=' . $i . '">[' . $i . ']</a>';
            }
        }
    }

    $output.= "\n\t\t\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t\t\t" . '</div>';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<p>';

    // Editors Notes Addon
    if ($ednote && $page == 1) {
        $ednote = mynews_format($ednote);
        $output.= makecaption($ednote,'right','200');
    }

    $pgprnt  = $page - 1;
    $output .= $fulltext[$pgprnt];

    $output.= "\n\t" . $pageButton;
    $output.= "\n\t" . '</blockquote>';

    // Get the return hash from mkPrevNext so we can determine what do to with it.
    $contentHash    = mkPrevNext($sid,$section);
    if($contentHash['error']) $returnArray['error'] = $contentHash['error'];
    $output.= $contentHash['content'];

    $output.= "\n\t" . '<br />';
    $output.= "\n\t" . '<div align="left">';
    $output.= "\n\t" . '<a href="' . $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'] . '?mode=compose&tid=' . $sid . '&other=news&parent=0&title=' . $cryptTitle . '">[Comment]</a>';
    $output.= "\n\t" . '<br />';
    $output.= "\n\t" . '<br />';

    // Here we create the hash we are going to return.
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function mkPrevNext($sid,$section) {
/**
 * This function prints out links to the articles immediately before
 * and after the selected article.  It will print out the links
 * pertaining to the section the article is in if selected from that
 * particular section's archive.  Otherwise it prints the previous
 * and next items in the database regardless of what section they
 * are in.  This function only works with items flagged as "active".
 *            
 * Ex:    mkPrevNext($sid,$section);        
 */
global $myNewsConf;

    // Assign some needed variables ahead of time.
    $baseStory_URI  = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseArch_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'];

    // Connect to the database.
    mynews_connect();

    // Build the query to get the previous active story link out of the database.
    $query  = '
        SELECT
            artnr,
            section,
            title
        FROM ' . $myNewsConf['db']['table']['news'] . '
        WHERE artnr < ' . $sid . '
        AND active = 1
        AND section = "' . addslashes($section) . '"
        ORDER BY artnr desc
        LIMIT 1';
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // We need to know how many rows were returned.
    $rowCnt = mysql_num_rows($result);

    // Build a hash out of our results.
    $row = mysql_fetch_assoc($result);
    if($rowCnt) extract($row);

    // Generate the link out of our hash.
    if (!empty($row)) {
        $prevStoryLink  = '<a class="tiny" href="' . $baseStory_URI . '?mode=storyView&sid=' . $artnr . '">&lt;&lt;</a> ';
        $prevStoryLink .= '<a class="tiny" href="' . $baseArch_URI . '?section=' . urlencode($section) . '">' . $section . '</a>: ';
        $prevStoryLink .= '<a class="tiny" href="' . $baseStory_URI . '?mode=storyView&sid=' . $artnr . '">' . $title . '</a>';
    }

    // Build the query to get the next active story out of the database.
    $query  = '
        SELECT
            artnr,
            section,
            title
        FROM ' . $myNewsConf['db']['table']['news'] . '
        WHERE artnr > ' . $sid . '
        AND active = 1
        AND section = "' . addslashes($section) . '"
        ORDER BY artnr
        LIMIT 1';
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // We need to know how many rows were returned.
    $rowCnt = mysql_num_rows($result);

    // Build a hash out of our results.
    $row = mysql_fetch_assoc($result);
    if($rowCnt) extract($row);

    // Generate the link our of our hash.
    if (!empty($row)) {
        $nextStoryLink  = '';
        if (isset($prevStoryLink)) {
            $nextStoryLink .= ' || ';
        }
        $nextStoryLink .= '<a class="tiny" href="' . $baseArch_URI . '?section=' . urlencode($section) . '">' . $section . '</a>: ';
        $nextStoryLink .= '<a class="tiny" href="' . $baseStory_URI . '?mode=storyView&sid=' . $artnr . '">' . $title . '</a>';
        $nextStoryLink .= '<a class="tiny" href="' . $baseStory_URI . '?mode=storyView&sid=' . $artnr . '">&gt;&gt;</a>';
    }

    // Build the output we're going to return.
    $output.=  "\n" . '<div align="center">';
    if (isset($prevStoryLink)) {
        $output.= "\n" . $prevStoryLink;
    }

    if (isset($nextStoryLink)) {
        $output.= "\n" . $nextStoryLink;
    }
    $output.= "\n" . '</div>';

    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
/**
 * Archive Functions
 */
/********************************************************************/
function archive($section,$sort,$show,$count) {
/**
 * This function displays headlines in archive format
 *                
 * Ex:    archive(news,author,10,10);        
 */
global $myNewsConf;

    // Assign some needed variables ahead of time.
    $baseStory_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseArchive_URI    = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive'];

    // The following could have been done in the function declaration, but since we pass
    // NULL to the fields, it thinks they exists; therefore, we have to set defaults
    // for them here.
    if (empty($count)) $count = $myNewsConf['default']['limit'];
    if (empty($show)) $show = 0;


    // Build the order clause for the query we're generating.
    if ($section == 'All') {
        $whereclause = 'where active = 1';
    } else {
        $whereclause = "where section='" . urldecode($section) . "' AND active = 1";
    }

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query = '
        SELECT
            a.artnr as sid,
            a.title,
            a.author,
            sum(if(b.artnr is NULL,0,1)) as cmcount
        FROM
            ' . $myNewsConf['db']['table']['news'] . ' as a
        LEFT JOIN
            ' . $myNewsConf['db']['table']['comments'] . " as b
        ON
            a.artnr = b.artnr
        AND
            b.type  = 'news'
        " . $whereclause . '
        GROUP by 1
        ORDER by
            sid desc
        LIMIT
            ' . $show . ',' .$count;
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Build our content output.
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<table border=0 width="95%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Title:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Author:</b></td>';
    $output.= "\n\t\t\t" . '<td width="10%" nowrap><b>Comments:</b></td>';
    $output.= "\n\t\t" . '</tr>';

    // Insert our returned data into a hash.
    while($row = mysql_fetch_assoc($result)){
        extract($row);

        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '">' . $title . '</a>';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $author . '</td>';
        $output.= "\n\t\t\t" . '<td align="center"><small>(' . $cmcount . ')</small></td>';;
        $output.= "\n\t\t" . '</tr>';
    }//EndWhile


    // Close out our html tables.
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</blockquote>';

    // Generate our where clause based on the section
    $wClause    = '';
    if($section != 'All') $wClause = "WHERE section LIKE '%" . $section . "%'"; 

    // Generate Forward and Next button navigation.
    // Query the database and determine how many records we are dealing
    // with.
    $query  = '
        SELECT
            count(*) as total
        FROM
            ' . $myNewsConf['db']['table']['news'] . '
        ' . $wClause;
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
     extract($row);

    $next   = $_GET['show'] + $myNewsConf['default']['limit'];
    $back   = $_GET['show'] - $myNewsConf['default']['limit'];

    $output.= "\n\t\t" . '<p align="center">';


    // If $_GET['show'] is zero, we need to change it to a one, for
    // readability.
    if($_GET['show'] == 0) $_GET['show'] = 1;

    // $vor is $total by default.
    $vor    = $total;

    // If we are not on the last page, set $vor to $next.
    if($next < $total) $vor = $next;
    
    // Output where we are in english.
    $output .= '<small>[' . $_GET['show'] . ' - ' . $vor . ' of ' .  $total . ']</small> <br />';

    if($back >= 0) $output.= "\n\t\t" . '<a href="' . $baseCalAdmin_URI . '?mode=calList&show=' . $back . '&section=' . urlencode($section) . '">' . $myNewsConf['button']['back'] . '</a>';

    if($back >= 0 && $next < $total) $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    if($next < $total) $output.= "\n\t\t" . '<a href="' . $baseCalAdmin_URI . '?mode=calList&show=' . $next . '&section=' . urlencode($section) .'">' . $myNewsConf['button']['next'] . '</a>';

    // Build the Title.
    $title  = 'Archive : ' . $section;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
/**
 * Comment System Functions
 */
/********************************************************************/
function comments($topic_id=0, $level=0,$type='news') {
/**
 * This function displays comments posted to a particular    
 * story                            
 *
 * Ex:    comments($sid);                    
 */
global $myNewsConf;

    // General Definitions.
    $baseComments_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];
    $comments   = 0;

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

    // Connect to the database.
    mynews_connect();

    // Build and execute the query.
    $query  = '
        SELECT
            artnr as sid,
            cmtnr as cid,
            cmtitle as title,
            cmauthor as author,
            cmdate as timestamp,
            commenttext as comment
        FROM
            ' . $myNewsConf['db']['table']['comments'] . '
        WHERE
            ' . $wClause . "
        AND
            type = '" . $type . "'
        ORDER by
            timestamp, sid";

    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

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

        $output.= "\n" . $tab;
        $output.= $pad;
        $output.= $dot . '&nbsp;';
        $output.= '<a href="' . $baseComments_URI . '?mode=display&tid=' . $sid . '&other=' . $cid . '" title="' . $timestamp . '">';
        $output.= $title . '</a> by <b>' . $author . '</b>';
        $output.= "\n" . $tab;
        $output.= '<br />';

        // Insert our returned data back into the returnArray
        $oArray = comments($cid, $level+1, $type, $action);
        $output.= $oArray['content'];
        $sqlErr.= $oArray['error'];

        // Create an array with comment ids to be deleted.
        $deleteArray[] = $cid;

        $comments++;
    }

    $title  = 'Submitted Comments:';

    $returnArray['data']    = $deleteArray;
    $returnArray['error']   = $sqlErr;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function cid($sid,$cid) {
/**
 * This function prints out the full text and information    
 * on the comment selected                    
 *                    
 * Ex:    cid($sid,$cid);                    
 */
global $myNewsConf;

    $baseComments_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];
    $baseStory_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query =  '
            SELECT
                *,
                unix_timestamp(cmdate) as timestamp
            FROM ' . $myNewsConf['db']['table']['comments'] . '
            WHERE cmtnr = ' . $cid;
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result,$query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row = mysql_fetch_assoc($result);
     extract($row);

    // run cmEmail agains the stopSpam function.
    $cmemail    = stopSpam($cmemail);

    // Begin text formatting
    $comment    = format_output($commenttext);
    //$comment    = urlPeek($comment);
    $date       = date($myNewsConf['format']['date']['default'],$timestamp);

    // encode the comment title in base 64 so we can pass
    // in the GET string to back to the write_comments function
    // and not have to worry about special chars in the GET
    // string breaking the url.
    $cryptTitle = base64_encode($cmtitle);

    // Begin out content output.
    $output = "\n\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t" . '<small>Posted by: <a href="mailto:' . $cmemail . '">' . $cmauthor . '</a> on ' . $date . '</small>';
    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . $comment;
    $output.= "\n\t" . '</blockquote>';

    // Here we check to make sure the parent is not set to 0.  If not, we 
    // continue and build the Parent link
    $parent_URI = '';
    if($pid) {
        $parent_URI = '| <a href="' . $baseComments_URI . '?mode=display&tid=' . $sid . '&other=' . $pid . '">Parent</a>';
    } 

    // Now we find the next comment on this level (children do not count)
    // and display the "Next Comment" link if one exists.

    // First we build our Query
    $query  = '
        SELECT
            cmtnr
        FROM
            ' . $myNewsConf['db']['table']['comments'] . '
        WHERE
            cmtnr   > ' . $cid . '
        AND
            pid     = ' . $pid . '
        AND
            artnr   = ' . $sid . '
        ORDER BY
            cmtnr
        LIMIT 1';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $row    = mysql_fetch_assoc($result);
    $cmtnr  = $row['cmtnr'];

    // We can assume if the $cmtnr var is empty, a following
    // comment doesn't exist.
    $nextComment_URI = '';
    if($cmtnr) {
        $nextComment_URI = ' | <a href="' . $baseComments_URI . '?mode=display&tid=' . $sid . '&other=' . $cmtnr . '" title="On this level">Next Thread</a>';
    }

    // Now we print out the Comment navigation system.

    $output.= "\n\t\t" . '<p align="left">';
    $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;';
    $output.= "\n\t\t" . '[';
    $output.= "\n\t\t" . '<a href="' . $baseComments_URI . '?mode=compose&tid=' . $sid . '&other=' . $type . '&parent=' . $cid . '&title=' . $cryptTitle . '">Reply</a>';
    $output.= "\n\t\t" . ' | ';
    $output.= "\n\t\t" . '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '#DISCUSS">Top</a>';
    $output.= "\n\t\t" . $parent_URI;
    $output.= "\n\t\t" . $nextComment_URI;
    $output.= "\n\t\t" . ']';

    // Build the title we want to return.
    $title  = 'Comments : <small>' . $cmtitle . '</small>';

    $returnHash['title']    = $title;
    $returnHash['content']  = $output;

return $returnHash;
}
/********************************************************************/
function commentAdd($sid , $parent , $other, $title = '') {
/**
 * This function prints the form to write comments to a
 * selected item (defined as $sid)
 *                
 * Ex:    commentAdd($sid);        
 */
global $myNewsConf;

    $baseComments_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];

    // Set the size of textfields, and columns/rows of textarea fields.
    $taCols = $myNewsConf['form']['textarea']['cols'];
    $taRows = $myNewsConf['form']['textarea']['rows'];
    $tWidth = $myNewsConf['form']['text']['width'];

    // Set $host by what apache knows.
    $host   = $_SERVER['REMOTE_HOST'];
    if(!$host) $host = $_SERVER['REMOTE_ADDR'];

$output = <<<HTML
        <blockquote>
        <form method="post" action="{$baseComments_URI}?mode=add&tid={$sid}">
            <b>Subject:</b>
            <br />
            <input class="textbox" type="text" name="title" size="{$tWidth}" value="{$title}">
            <br />
            <br />
            <b>Comment:</b>
            <br />
            <textarea class="textbox" name="comment" cols="{$taCols}" rows="{$taRows}" wrap="virtual"></textarea>
            <br />
            <br />
            <b>Your Name:</b>
            <br />
            <input class="textbox" type="text" name="author" size="{$tWidth}" value="Your Name Here."  OnFocus="if(this.value && this.value == 'Your Name Here.') { this.value = ''; this.form.email.value = ''} else { this.select(); }">
            <br />
            <br />
            <b>E-mail Address:</b>
            <br />
            <input class="textbox" type="text" name="email" size="{$tWidth}"  value="Your Email Address Here." OnFocus="if(this.value && this.value == 'Your Email Address Here.') { this.value = ''; this.form.author.value = ''} else { this.select(); }">
            <input type="hidden" name="host" value="{$host}">
            <input type="hidden" name="type" value="{$other}">
            <input type="hidden" name="pid" value="{$parent}">
            <br />
            <br />
            {$myNewsConf['button']['submit']}
        </form>
        </blockquote>

        <hr width="85%" size="1">
                <p>
            &nbsp;&nbsp;&nbsp;
            <b>Special Formatting Characters:</b>
        <table align="center" width="60%">
            <tr>
                <td><b>[p]</b></td>
                <td>New Paragraph</td>
            </tr>
            <tr>
                <td><b>[b]...[/b]</b></td>
                <td>Bold</td>
            </tr>
            <tr>
                <td><b>[i]...[/i]</b></td>
                <td>Italics</td>
            </tr>
            <tr>
                <td><b>[pre]...[/pre]</b></td>
                <td>Preformatted</td>
            </tr>
            <tr>
                <td><b>[indent]...[/indent]</b></td>
                <td>Indented Blocks (blockquote)</td>
            </tr>
            <tr>
                <td><b>[anchor="..."]</b></td>
                <td>Anchors (bookmarks)</td>
            </tr>
            <tr>
                <td><b>[link="..."]...[/link]</b></td>
                <td>Links (javascript is not permitted)</td>
            </tr>
        </table>

HTML;

    $returnHash['title']    = 'Reply To: <small>' . $title . '</small>';
    $returnHash['content']  = $output;

return $returnHash;
}
/********************************************************************/
function commentAdded($sid) {
/**
 * This function does the error checking of a submitted
 * comment and returns errors if there are any.  Else,
 * it write the comments to the database and returns a
 * thank you message.
 *
 * Ex:    commentAdded($sid);        
 */
global $myNewsConf;

    $baseComments_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];
    $baseStory_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
    $baseDisplay_URI    = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['display'];
    $baseEvent_URI      = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['event'];

    $cryptTitle = base64_encode($title);

    if (!$_POST['author'] || !$_POST['email'] || !$_POST['comment'] || !$_POST['title']) {
        $errorArray['error'] = myNewsError(102,'One or more required fields are missing.');
    } elseif (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$_POST['email'])) {
        $errorArray['error'] = myNewsError(102,'A Valid email address is required.');
    }

    // Return with the error if $errorArray exists.
    if($errorArray) return $errorArray;

    // Connect to the database
    mynews_connect();

    // Build and execute our query
    $query  = '
        INSERT into ' . $myNewsConf['db']['table']['comments'] . "
        values(
            '',
            '" . addslashes($_POST['pid'])      . "',
            '" . addslashes($_POST['title'])    . "',
            '" . addslashes($_POST['comment'])  . "',
            '" . addslashes($_POST['author'])   . "',
            '" . addslashes($_POST['email'])    . "',
            '" . date('Y-m-d H:i:s')            . "',
            '" . addslashes($_POST['host'])     . "',
            '" . addslashes($sid)               . "',
            '" . addslashes($_POST['type'])     . "'
            )";
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    $comment= format_output($_POST['comment']);

    $output = "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'Thank you for your comment.  Your Post is as follows:';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t\t\t" . $comment;
    $output.= "\n\t\t" . '</blockquote>';

    switch($_POST['type']){
        case 'news':
            $output.= '<a href="' . $baseStory_URI . '?mode=storyView&sid=' . $sid . '">Return to item</a>';
            break;
        case 'photos':
            $output.= '<a href="' . $baseDisplay_URI . '/' . $sid . '/">Return to item</a>';
            break;
        case 'cal':
            $output.= '<a href="' . $baseEvent_URI . '?mode=zoom&eid=' . $sid . '">Return to item</a>';
            break;
    }

    $output .= '</blockquote>';

    $title  = 'Added : <small>' . $_POST['title'] . '</small>';

    $returnArray['title']    = $title;
    $returnArray['content']  = $output;

return $returnArray;
}
/********************************************************************/
/**
 * "Friend" Functions
 */
/********************************************************************/
function storyPrint($sid) {
/**
 * This function prints out the full text and information
 * of the article selected    in printer friendly format
 *                
 * Ex:    storyPrint($sid);        
 */
global $myNewsConf;

    mynews_connect();
    $query =  mysql_query("
        SELECT
            *,
            unix_timestamp(date) as timestamp
        FROM " . $myNewsConf['db']['table']['news'] . "
        WHERE artnr='$sid'"); // or die(mysql_error());

    $row = mysql_fetch_assoc($query);
        /*
         * New way
        */
        extract($row);
        $email          = stopSpam($email);


    // Here we look for [caption][/caption] tags to see whether we want to
    // Insert a table for the caption.
    $previewtext    = preg_replace("/\[caption\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\1'),'right','150')", $previewtext);
    $previewtext    = preg_replace("/\[caption align=\"(.+?)\"\](.+?)\[\/caption\]/ies", "makecaption(stripslashes('\\2'),'\\1','150')", $previewtext);

    /**
     * We do some standard text manipulation here:
     * We need to check for URL's that haven't already
     * been used as links, and turn them into <HREF>'s
     *
     * Also we want to turn newlines into HTML <br />'s
     *
     * Finally we turn all double spaces into HTML &nbsp;'s
     * to get proper formatting on our articles
     */
    $previewtext    = make_clickable($previewtext);
    $previewtext    = mynews_format("$previewtext");

    // Insert Header Rows in place of pagebreaks
    $previewtext    = eregi_replace($myNewsConf['default']['pagebreak'],'<br /><br /><hr size="1" noshade>',$previewtext);

    /**
     * Here we take our date that we pulled out of the database
     * as a unix_timestamp and convert it to something that
     * is human readable.
     */
    $date   = date($myNewsConf['format']['date']['default'],$timestamp);

    /**
     * We do some standard text manipulation here:
     * We need to strip out the HTML tags so it doesn't
     * screw up the <TITLE></TITLE> in the final output
     */
    $page_title = strip_tags($title);


    $sitename   = $myNewsConf['default']['sitename'];

    $output = "\n" . '<html>';
    $output.= "\n\t" . '<head>';
    if (file_exists($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css')) {
        $output.= "\n\t\t" . '<link rel="STYLESHEET type="text/css" href="' . $myNewsConf['path']['web']['index'] . 'templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css">';
    }
    $output.= "\n\t\t" . '<title>' . $sitename . $section . ' : ' . $page_title . '</title>';
    $output.= "\n\t" . '</head>';
    $output.= "\n\t" . '<body>';
    $output.= "\n\t\t" . '<b>' . $title . '</b>';
    $output.= "\n\t\t" . '<p>';
    $output.= "\n\t\t" . '<small>Posted by: <a href="mailto:' . $email . '">' . $author . '</a> (' . $email . ') on ' . $date . '</small>';
    $output.= "\n\t\t" . '<blockquote>';
    $output.= "\n\t\t" . $previewtext;
    $output.= "\n\t\t" . '</blockquote>';
    $output.= "\n\t" . '</body>';
    $output.= "\n" . '</html>';

    echo $output;
}
/********************************************************************/
function storyForm($sid) {
/**
 * This function generates the form for users to fill
 * out so they can send an email with links to the
 * selected item.  It selects the info by $sid (Story id)
 *                                        
 * Ex:    storyForm($sid);              
 */
global $myNewsConf;

    $button         = $myNewsConf['button']['submit'];
    $baseStory_URI  = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];

    // Pull the Story title out of the _GET hash.
    $title  = base64_decode($_GET['title']);

    $output = "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . 'An email will be sent with a description and link to the referenced article';
    $output.= "\n\t" . '<form method="post" action="' . $baseStory_URI . '?mode=storyMail&sid=' . $sid . '">';
    $output.= "\n\t" . '<p>';
    $output.= "\n\t" . '<table border=0>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">Your Name:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" name="fromname" size="20">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">Your Email:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" name="fromemail" size="20">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">Recipient&acute;s Name:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" name="toname" size="20">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td valign="top">Recipient&acute;s Email:</td>';
    $output.= "\n\t\t\t" . '<td>';
    $output.= "\n\t\t\t\t" . '<input class="textbox" name="toemail" size="20">';
    $output.= "\n\t\t\t\t" . '<input type="hidden" name="title" value="' . $title . '">';
    $output.= "\n\t\t\t" . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t\t" . '<td>' . $button . '</td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</form>';
    $output.= "\n\t" . '</blockquote>';

    $title  = 'Mail: <small>' . $title . '</small>';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function storyMail($sid) {
/**
 * This function pulls the info out of mail_form(),
 * inputs it into php's mail() function, and sends
 * the formatted email to the person in the To: field
 * where $sid is equal to the news item.
 *                                        
 * Ex:    storyMail($sid);              
*/
global $myNewsConf;

    $output     = '';
    $base_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];

    // Turn all of our $_POST[] keys into variables.
    extract($_POST);

    if (!$toemail || !$fromemail || !$fromname || !$toname) {
        $errorArray['error'] = myNewsError(102,'One or more required fields are missing.');
    } elseif (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$fromemail)) {
        $errorArray['error'] = myNewsError(102,'Please enter a valid <b>From:</b> email address');
    } elseif (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',$toemail)) {
        $errorArray['error'] = myNewsError(102,'Please enter a valid <b>To:</b> email address');
    }

    // Return with the error if $errorArray exists.
    if($errorArray) return $errorArray;
    
    // Pull $title out of our $_POST hash.
    $title = $_POST['title'];

    // Build our "replace" string hash so we can build our email
    // out of the defined template.
    $tmplHash   = array(
            '%TNAME%'   => $toname,
            '%FNAME%'   => $fromname,
            '%SNAME%'   => $myNewsConf['default']['sitename'],
            '%TITLE%'   => $title,
            '%ART%'     => $myNewsConf['default']['siteurl'] . $base_URI . '?mode=storyView&sid=' . $sid);

    $rcpt   = $toemail;
    $subj   = $title;
    $body   = parseEmailTemplate($myNewsConf['email']['template']['friend'], $tmplHash);

    if($rcpt && $subj && $body){
        emailNotify($body,$subj,$rcpt);
    }

    // Build our content output.
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t\t" . '"' . $title . '" has been successfully sent to ' . $toemail . '.';
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . '<br />';
    $output.= "\n\t\t" . 'You should be returned to the article in a few seconds.  If your browser fails to refresh, click ';
    $output.= "\n\t\t" . '<a href="' . $base_URI . '?mode=storyView&sid='. $sid . '">here</a>';
    $output.= "\n\t" . '</blockquote>';

    // Build the meta refresh
    $meta   = '<meta http-equiv="Refresh" content="5; URL=' . $base_URI . '?mode=storyView&sid=' . $sid . '">';

    // Build the return title.
    $title  = 'Sent : ' . $title;

    $returnArray['meta']    = $meta;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
/**
 * Authors Functions
 */
/********************************************************************/
function authorList() {
/**
 * This function prints out the list of authors in the
 * authors authors table.  It gives pertinent info such as:
 * Userame:
 * RealName:
 * Class: (Admin,Editor,Author)
 *            
 * Ex:     authorList();              
 */
global $myNewsConf;

    $baseArchive_URI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about'];

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query  = '
        SELECT
            user,
            name,
            status
        FROM
            ' . $myNewsConf['db']['table']['authors'] . '
        ORDER by
            user';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;

    // Begin content output.
    $output.= "\n\t" . '<blockquote>';
    $output.= "\n\t" . '<table border="0" cellpadding="0" cellspacing="0" width="85%">';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td><b>Author:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Real Name:</b></td>';
    $output.= "\n\t\t\t" . '<td><b>Status:</b></td>';
    $output.= "\n\t\t" . '</tr>';
    $output.= "\n\t\t" . '<tr>';
    $output.= "\n\t\t\t" . '<td colspan="3">&nbsp;</td>';
    $output.= "\n\t\t" . '</tr>';

    // Loop through each user and build the content output.
    while($row = mysql_fetch_assoc($result)) {
        extract($row);

        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td>';
        $output.= "\n\t\t\t\t" . '<a href="' . $baseArchive_URI . '?mode=zoom&author=' . $user . '">' . $user . '</a>';
        $output.= "\n\t\t\t" . '</td>';
        $output.= "\n\t\t\t" . '<td>' . $name . '</td>';
        $output.= "\n\t\t\t" . '<td align="left">' . $status . '</td>';
        $output.= "\n\t\t" . '</tr>';
    } //EndWhile

    // Continue building content output.
    $output.= "\n\t" . '</table>';
    $output.= "\n\t" . '</blockquote>';

    $title  = ' About : Staff';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
function authorShow($author) {
/**
 * This function prints out the information stored
 * in the database about the selected author.
 *                                        
 * Ex:    authorShow($author);              
 */
global $myNewsConf;
    
    $newstable      = $myNewsConf['db']['table']['news'];
    $authorstable   = $myNewsConf['db']['table']['authors'];

    // Connect to the database.
    mynews_connect();

    // Build and execute our query.
    $query = ("
        SELECT
            a.name, 
            a.bio, 
            a.status, 
            a.email, 
            a.url, 
            sum(if(b.artnr is NULL,0,1)) as arcount
        FROM    
            " . $myNewsConf['db']['table']['authors'] . " as a
        LEFT JOIN
            " . $myNewsConf['db']['table']['news'] . " as b
        ON a.user=b.author
        WHERE a.user = '$author'
        GROUP by 1");

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['error'] = $sqlErr;
    if($sqlErr) return $errorArray;
    
    // Get our data from the database.
    $row    =  mysql_fetch_assoc($result);

    // Get a count of how many rows were returned.
    $rowCnt = mysql_num_rows($result);

    // if $rowCnt is > 0, we can extract.
    if($rowCnt) extract($row);

    // run some of our data through cleanup/formatting functions.
    $email      = stopSpam($email);
    $bio        = mynews_format($bio);

    if(!$name){
        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t\t" . 'Either you have selected an invalid user, or the author doesn\'t exist in the database anymore';
        $output.= "\n\t" . '</blockquote>';
    } else {
        // Make some definition based on what is returned from the database.
        if($email) {
            $email  = '<a href="mailto:' . $email . '">' . $email . '</a>';
        } else {
            $email  = '<b>N/A</b>';
        }

        if($url) {
            $url    = '<a href="' . $url . '" target="new">' . $url . '</a>';
        } else {
            $url    = '<b>N/A</b>';
        }

        $output.= "\n\t" . '<blockquote>';
        $output.= "\n\t" . '<table WIDTH="85%" CELLPADDING="2" CELLSPACING="1" BORDER="0">';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="top" width="15%"><b>Name:</b></td>';
        $output.= "\n\t\t\t" . '<td>' . $name . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="TOP" width="15%"><b>Written:</b></td>';
        $output.= "\n\t\t\t" . '<td>' . $arcount . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="TOP" width="15%"><b>URL:</b></td>';
        $output.= "\n\t\t\t" . '<td>' . $url . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="TOP" width="15%" <b>Status:</b></td>';
        $output.= "\n\t\t\t" . '<td>' . $status . '</td>';
        $output.= "\n\t\t" . '</tr>';
        $output.= "\n\t\t" . '<tr>';
        $output.= "\n\t\t\t" . '<td align="left" valign="TOP" width="15%"><b>Email:</b></td>';
        $output.= "\n\t\t\t" . '<td>' . $email . '</td>';
        $output.= "\n\t\t" . '</tr>';

        if($bio){

            $output.= "\n\t\t" . '<tr>';
            $output.= "\n\t\t\t" . '<td align="left" valign="TOP" width="15%"><b>Bio:</b></td>';
            $output.= "\n\t\t\t" . '<td>' . $bio . '</td>';
            $output.= "\n\t\t" . '</tr>';
        }

        // Close out the table
        $output.= "\n\t" . '</table>';
        $output.= "\n\t" . '</blockquote>';
    }

    $title  = ' About : ' . $author;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/********************************************************************/
/**
 * Text Formatting Functions
 */
/********************************************************************/
function format_output($output) {
/**
 * This function formats text (specifically comments right now)
 * and rather than creating a list of html tags a user cannot use,
 * it creates it's own markup language, using [ and ] rather < and >
 * when the text is passed through this function, it translates the
 * NEW markup lang. to it's corresponding HTML so the browser
 * can read it.
 *
 * Ex:    format_output($output)
 */
$output = htmlspecialchars(stripslashes($output));

    /**
     * new paragraph
     */
    $output = str_replace('[p]', '<p>', $output);
    $output = str_replace('[P]', '<p>', $output);
    $output = str_replace('[/p]', '', $output);
    $output = str_replace('[/P]', '', $output);

    /**
     * bold
     */
    $output = str_replace('[b]', '<b>', $output);
    $output = str_replace('[/b]', '</b>', $output);
    $output = str_replace('[/]', '</b>', $output);

    /**
     * italics
     */
    $output = str_replace('[i]', '<i>', $output);
    $output = str_replace('[/i]', '</i>', $output);

    /**
     * preformatted
     */
    $output = str_replace('[pre]', '<pre>', $output);
    $output = str_replace('[/pre]', '</pre>', $output);

    /**
     * indented blocks (blockquote)
     */
    $output = str_replace('[indent]', '<blockquote>', $output);
    $output = str_replace('[/indent]', '</blockquote>', $output);

    /**
     * anchors
     */
    $output = ereg_replace('\[anchor=&quot;([[:graph:]]+)&quot;\]', '<a name="\\1"></a>', $output);

    /**
     * spacing
     */
    $output = str_replace("  ", " &nbsp;", $output);

    /**
     *links, note we try to prevent javascript in links
     */
    $output = str_replace('[link=&quot;javascript', '[link=&quot; javascript', $output);
    $output = ereg_replace('\[link=&quot;([[:graph:]]+)&quot;\]', '<a href="\\1">', $output);
    $output = str_replace('[/link]', '</a>', $output);    

    return mynews_format($output);
}
/********************************************************************/
function string_cut($string,$cut_size,$type='word') {
/**
 * This function creates a paragraph count limit.        
 *                        
 * Ex:    string_cut($title,5);        
 */
    if ($type == 'paragraph') {
        $StringArray = explode("\r\n\r\n",$string);  
        $string_cut  = '';
        for($i=0;$i<$cut_size;$i++) {
            $string_cut .= ' ' . $StringArray[$i];  
        } // End for
    } elseif ($type == 'word') {
        $StringArray = explode(' ',$string);  
        $string_cut  = '';
        for($i=0;$i<$cut_size;$i++) {
            $string_cut .= ' ' . $StringArray[$i];  
        } // End for
        $string_cut .= ' ...';
    } 

    return $string_cut;
}
/********************************************************************/
function mynews_format($text) {
/**
 * This function attempts to replace the nl2br() function which
 * improperly places (actually does what it is supposed to) <br />
 * tags in ordered lists, un-ordered lists, list items and
 * blockquotes.
 *
 * It also changes spaces within the string to html compatible:
 * &nbsp;
 *
 * Ex:  mynews_format($text);
 */

    /*
     * Change the \r\n line breaks to \n line breaks so an opening tag doesn't immediately
     * have a \n<br />\n translated.
     */
    $text = stripslashes(preg_replace( "/(>)(\r\n)/e", "'\\1'.str_replace('\r\n','\n','\\2').'\\3'", $text));

    $text = str_replace("<small>\r\n", "<small>\n", $text); 
    $text = str_replace("</small>\r\n", "</small>\n", $text); 
    $text = str_replace("<blockquote>\r\n", "<blockquote>\n", $text); 
    $text = str_replace("<BLOCKQUOTE>\r\n", "<blockquote>\n", $text); 
    $text = str_replace("</blockquote>\r\n", "</blockquote>\n", $text); 
    $text = str_replace("</BLOCKQUOTE>\r\n", "</blockquote>\n", $text); 
    $text = str_replace("\r\n<ol>\r\n", "\n\t<ol>\n", $text); 
    $text = str_replace("\r\n<OL>\r\n", "\n\t<ol>\n", $text); 
    $text = str_replace("\r\n<ul>\r\n", "\n\t<ul>\n", $text); 
    $text = str_replace("\r\n<UL>\r\n", "\n\t<ul>\n", $text); 
    $text = str_replace("<li>\r\n", "\t\t<li>\n", $text); 
    $text = str_replace("<LI>\r\n", "\t\t<li>\n", $text); 
    $text = str_replace("</li>\r\n", "\n", $text); 
    $text = str_replace("</LI>\r\n", "\n", $text); 
    $text = str_replace("</ol>\r\n", "\n\t</ol>\n", $text); 
    $text = str_replace("</OL>\r\n", "\n\t</ol>\n", $text); 
    $text = str_replace("</ul>\r\n", "\n\t</ul>\n", $text); 
    $text = str_replace("</UL>\r\n", "\n\t</ul>\n", $text); 
    $text = str_replace("\r\n\r\n", "\n<p>\n", $text); 
    $text = str_replace("\r\n", "\n<br />\n", $text); 
    $text = str_replace("  ", " &nbsp;", $text); 


    /*
     * Strip out unneccesary <br /> from inbetween <pre></pre> tags.
     */
    $text = stripslashes(preg_replace( "/(<pre>)([\w\W]*)(<\/pre>)/e", "'\\1'.str_replace('<br />\n','','\\2').'\\3'", $text));

    /*
     * Strip out unneccesary <br /> from inbetween closed <table> associate (<tr><td></td></tr>)
     * tags.
     */
    $text = stripslashes(preg_replace( "/(<table.*>)([\w\W]*)(<tr>)/e", "'\\1'.str_replace('<br />','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<tr>)([\w\W]*)(<td.*>)/e", "'\\1'.str_replace('<br />','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<\/td>)([\w\W]*)(<td.*>|<\/tr>)/e", "'\\1'.str_replace('<br />','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<\/tr>)([\w\W]*)(<\/table>)/e", "'\\1'.str_replace('<br />','','\\2').'\\3'", $text));

    /*
     * Strip out unneccesary &nbsp; from inbetween closed <table> associate (<tr><td></td></tr>)
     * tags.
     */
    $text = stripslashes(preg_replace( "/(<table.*>)([\w\W]*)(<tr>)/e", "'\\1'.str_replace('&nbsp;','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<tr>)(\w\W]*)(<td.*>)/e", "stripslashes('\\1').str_replace('&nbsp;','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<\/td>)([\w\W]*)(<td>|<\/tr>)/e", "'\\1'.str_replace('&nbsp;','','\\2').'\\3'", $text));
    $text = stripslashes(preg_replace( "/(<\/tr>)([\w\W]*)(<\/table>)/e", "'\\1'.str_replace('&nbsp;','','\\2').'\\3'", $text));

    return $text;
}
/********************************************************************/
function row_place($i,$rows='2') {
/**
 * This function manipulates tables to stagger new rows.  The number
 * of columns is passed to the function and it builds the dynamic
 * table with the correct matrix.
 *
 * Ex:    row_place(3);
 */
global $myNewsConf;

    $row1 = "\n\t</tr>\n\t<tr>\n"; 
    $row2 = ""; 

    if ( ($i % $rows) == 0 ) { 
        return $row1;  
    } else {  
        return $row2;  
    } 

}
/********************************************************************/
function make_clickable($text) {
/**
 * This function takes a string and looks for anything that looks like
 * a URL or Email Address and turns them into active links.  It ignores
 * links that have already been generated.
 *
 * Ex:    make_clickable($text);
 */
    $ret = ereg_replace("(^|[[:space:]])(http://[^[:space:]]{1,})([[:space:]]|$)","\\1<a href=\"\\2\" target=\"_new\">\\2</a>\\3", $text);
    $ret = ereg_replace("(^|[[:space:]])(https://[^[:space:]]{1,})([[:space:]]|$)","\\1<a href=\"\\2\" target=\"_new\">\\2</a>\\3", $ret);
    $ret = eregi_replace("(^|[[:space:]])(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))([[:space:]]|$)"," <a href=\"mailto:\\2\">\\2</a> ", $ret);
    return($ret);
}
/********************************************************************/
function stopSpam($string) {
/**
 * This function takes a string and looks for anything that looks like
 * a URL or Email Address and turns them into active links.  It ignores
 * links that have already been generated.
 *
 * Ex:    make_clickable($text);
 */
global $myNewsConf;

    if ($myNewsConf['default']['spam']['stop'] == 'on') {
        $string = preg_replace("/([\w\.]+)(@)([\w\.]+)/i", "$1" . $myNewsConf['default']['spam']['string'] . "$3", $string);
    }
    return $string;
}
/********************************************************************/
function urlPeek($input_text) {
/**
 * This function takes a string and looks for formatted HTML links.
 * it then pulls out the domain of the link and put's it blocked to the
 * right of the string so the user will know which domain the link goes
 * to.  This is to prevent porn trolling.
 *
 * Ex:    urlPeek($text);
 */
    $test_para_array = spliti("<a", $input_text);
    foreach ($test_para_array as $key => $value) {
        if($value) {
            eregi("\"([a-zA-Z0-9:\/\._\%]{0,100})\"", $value, $output);
            eregi("\"[a-zA-Z0-9:\/\._\%]{0,100}\">([a-zA-Z0-9:\/\._\% ]+)</a>", $value, $links);
            $explode = str_replace("www.","",explode("/",$output[1]));
            $match_string=$output[1]."\">".$links[1]."</a>";
            $replace_string=$output[1]."\" target=\"_new\">".$links[1]."</a> [".$explode[2]."]";
            $input_text=eregi_replace($match_string,$replace_string,$input_text);
            unset($output,$links);
        }
    }
    return $input_text;
}
/********************************************************************/
function cleanUpGPC($processArray) {
/**
 * This function checks whether magic_quotes_gpc is turned on.  In the
 * event it is turned on, it will either do an array walk and stripslashes
 * from the values, or just stripslashes from a string.
 *
 * Ex:    cleanUpGPC($array);
 */
    if((get_magic_quotes_gpc())) {
        // Function to strip slashes
        if(!function_exists('__stripslashes')){
            function __stripslashes (&$s) {
                if(is_array($s)){
                    array_walk($s, '__stripslashes');
                } else {
                    $s = stripslashes($s);
                }
            }
        }
        // Process $text
        if(is_array($processArray)){
            array_walk($processArray, '__stripslashes');
        } else {
            $processArray   = stripslashes($processArray);
        }
    }
return $processArray;
}
/********************************************************************/
/**
 * Error Handling Functions
 */
/********************************************************************/
function myNewsError($code,$addendum=''){
/**
 * This function presents common errors in the MyNews code
 *
 * Ex:    echo myNewsError(101,'extra informative text');
 */
    $errorHash  = array(
            101 => 'Insufficient Arguments Passed',
            110 => 'You do not have permission to view this resource',
            102 => 'Submit Error',
            1   => 'General Error',
            2   => 'error #2');

    $return = <<<EOT

        <p>
        <font color='red'>MyNews Error!</font><br />
        &nbsp;&nbsp;&nbsp;<b>Error #: $code</b>
        <br />
        <blockquote>
          <font color='green'>
               {$errorHash[$code]}
          </font>
          <blockquote>
            $addendum
          </blockquote>
          <br /><br />
        </blockquote>

EOT;

    return $return;
}
/********************************************************************/
function myNewsChkSqlErr($input,$query){
/**
 * This function checks the database query and prints a friendly
 * database error message for debugging purposes.

 * Ex:    echo myNewsChkSqlErr($query);
 */
    if (!$input) {
        $errNum = mysql_errno();
        $error  = mysql_error();

        $return = <<<EOT

        <blockquote>
            <font color="red">Sql Error!</font>&nbsp;<b>{$errNum}</b></font>
            <br />
            <font color="green">{$error}</font>
            <br />
            <font color="blue"><pre>{$query}</pre></font>
        </blockquote>

EOT;

    return $return;
    }
} //End myNewsChkSqlErr()
/********************************************************************/
function makeNav($base_URI, $show, $table, $where, $extra){
/**
 * This function generates the "Back" and "Next" buttons for navigating
 * multiple pages of a particular document.
 *
 * Ex:  $output.= makeNav($baseArchive_URI, $count, $_GET['show'], '&section=' . $section);
 */
global $myNewsConf;

    // Connect to the database.
    mynews_connect();

    // Build and execute the query
    $query  = '
        SELECT
            count(*) as total
        FROM
            ' . $table . '
        ' . $where;
    $result = mysql_query($query);
    $sqlErr = myNewschkSqlErr($result, $query);

    if($sqlErr) return $sqlErr;

    $row    = mysql_fetch_assoc($result);
    $total  = $row['total'];

    // Generate Forward and Next button navigation.
    $next   = $show + $myNewsConf['default']['limit'];
    $back   = $show - $myNewsConf['default']['limit'];

    $output.= "\n\t\t" . '<p align="center">';

    // If $_GET['show'] is zero, we need to change it to a one, for
    // readability.
    if($show == 0 && $total != 0) $show = 1;

    // $vor is $total by default.
    $vor    = $total;

    // If we are not on the last page, set $vor to $next.
    if($next < $total) $vor = $next;
    
    // Output where we are in english.
    $output .= '<small>[' . $show . ' - ' . $vor . ' of ' .  $total . ']</small> <br />';

    if($back >= 0){
        $output.= "\n\t\t" . '<a href="' . $base_URI . '?show=' . $back . $extra . '">' . $myNewsConf['button']['back'] . '</a>';
    }

    if($back >= 0 && $next < $total){
        $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    }
                                                      
    if($next < $total){
        $output.= "\n\t\t" . '<a href="' . $base_URI . '?show=' . $next . $extra . '">' . $myNewsConf['button']['next'] . '</a>';
    }

return $output;
}
/********************************************************************/
function DateSelector($inName, $useDate=0, $genHourMin=true) {
/**
 * Creates three form fields for get month/day/year
 * Input: Prefix to name of field, default date
 * Output: HTML to define three date fields
 */
	//create array so we can name months
	$monthName = array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
		'04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
		'09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December');

	// if date invalid or not supplied, use current time
	if($useDate == 0) $useDate = Time();

	// Month selector
	$output.= "\n\n" . '<select name=' . $inName .  '[month]>';
	for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) {
        $currentMonth = substr_replace('00', $currentMonth, -1 * strlen($currentMonth));
        unset($selected);
		if(intval(date( 'm', $useDate)) == $currentMonth) $selected = ' selected';
		$output.= "\n\t" . '<option value="' . $currentMonth . '"' . $selected . '>' . $monthName[$currentMonth] .  '</option>';
	}
	$output.= "\n" . '</select>' ;

    $output.= "\n" . ',';

	// Day selector
	$output.= "\n\n" . '<select name=' . $inName .  '[day]>' ;
	for($currentDay=1; $currentDay <= 31; $currentDay++) {
        $currentDay = substr_replace('00', $currentDay, -1 * strlen($currentDay));
        unset($selected);
		if(intval(date( 'd', $useDate))==$currentDay) $selected = ' selected';
		$output.= "\n\t" . '<option value="' . $currentDay . '"' . $selected . '>' . $currentDay . '</option>' ;
	}
	$output.= "\n" . '</select>' ;


	// Year selector
	$output.= "\n\n" . '<select name=' . $inName .  '[year]>';
	$startYear = date( 'Y', $useDate);
	for($currentYear = $startYear - 2; $currentYear <= $startYear+2;$currentYear++) {
        unset($selected);
		if(date( 'Y', $useDate)==$currentYear) $selected = ' selected';
		$output.= "\n\t" . '<option value="' . $currentYear . '"' . $selected . '>' . $currentYear . '</option>';
	}
	$output.= "\n" . '</select>';

    // We only need to generate the following, if it is requested.
    if($genHourMin == true){
        $output.= "\n" . '&nbsp;&nbsp; - &nbsp;&nbsp;';

        // Hour selector
        $output.= "\n\n" . '<select name=' . $inName . '[hour]>';
        for($currentHour=0; $currentHour <= 23; $currentHour++){
            $currentHour = substr_replace('00', $currentHour, -1 * strlen($currentHour));
            unset($selected);
            if(date('H', $useDate) == $currentHour) $selected = ' selected';
            $output.= "\n\t" . '<option value="' . $currentHour . '"' . $selected . '>' . $currentHour . '</option>';
        }
        $output.= "\n" . '</select>';

        $output.= "\n" . ':';

        // Minute selector
        $output.= "\n\n" . '<select name=' . $inName . '[minute]>';
        for($currentMin=0; $currentMin <= 59; $currentMin++){
            $currentMin = substr_replace('00', $currentMin, -1 * strlen($currentMin));
            unset($selected);
            if(date('i', $useDate) == $currentMin) $selected = ' selected';
            $output.= "\n\t" . '<option value="' . $currentMin . '"' . $selected . '>' . $currentMin . '</option>';
        }
        $output.= "\n" . '</select>';
    }

return $output;
}
/********************************************************************/
function parseEmailTemplate($template, $replaceHash) {
/**
 * This function takes a hash that is keyed by what text needs to be
 * replaced in $template.
 *
 */
    $searchKeys = array_keys($replaceHash);
    foreach($searchKeys as $searchKey){
        $replaceKeys[] = $replaceHash[$searchKey];
    }
	$template = str_replace($searchKeys, $replaceKeys, $template);
    $template = stripslashes(wordwrap($template,70));
    $template = str_replace("\r\n", "\n", $template); 

return $template;
}
/*******************************************************************/
function emailNotify($body,$subject,$recipients,$reply='none'){
/**
 * This function sends an email to a predefined recipient list
 * with a passed body and subject.
 */
global $myNewsConf;

    // Here we define the Message headers and the subject.
    $header = 'From: ' . $_SESSION['fullname'] . '<' . $_SESSION['email'] . '>';
    if($reply != 'none') $header.= "\r\n" . 'Reply-To: ' . $reply;
    $header.= "\r\n" . 'X-Mailer: PHP/' . phpversion();
    
    mail($recipients,$subject,$body,$header);
}
/*******************************************************************/
?>
