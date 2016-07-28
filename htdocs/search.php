<?php
/* $Id: search.php 488 2005-08-18 20:22:02Z mmcmurr $ */

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

/*************** Begin Code *****************************************/
// Require the search library
require_once($myNewsConf['path']['sys']['index'] . '/include/libs/search.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$baseStory_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];

if (isset($_GET['query'])) {
    $query = ereg_replace("[^a-zA-Z0-9\ ]",'',$_GET['query']);
} else {
    $query = ereg_replace("[^a-zA-Z0-9\ ]",'',$_POST['query']);
}

if ($query == '' || !isset($query)) {
	$_title = ' Search:';

    $_content  = <<<EOT
        <p>
        <blockquote>
        Enter your search string here:
        <br />
        <form method="post" action="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['search']}">
            <input class="textbox" type="text" name="query" size="20">
            <br />
            {$myNewsConf['button']['search']}
        </form>
        </blockquote>
EOT;

} else {

    if (!isset($count) || empty($count)) $count = $myNewsConf['default']['limit'];
    if (!isset($_GET['show']) || empty($_GET['show'])) {
        $show = 0;
    } else {
        extract($_GET);
    }


    // database connection
    mynews_connect();

    // sql construction
    $fulltext_key = get_fulltext_key($myNewsConf['db']['table']['news']);

    $count_sql =  "SELECT artnr, author, title, \n"
            .boolean_sql_select(
                    boolean_inclusive_atoms($query),
                    $fulltext_key)." as relevance \n"
            ."FROM {$myNewsConf['db']['table']['news']} \n"
            ."WHERE \n"
            .boolean_sql_where($query,$fulltext_key)." \n"
            ."HAVING relevance>0 \n"
            ."ORDER BY relevance DESC \n";

    $count_result = mysql_query($count_sql);
    $sqlErr.= myNewsChkSqlErr($count_result,$count_sql);

    if(!$sqlErr) $total = mysql_num_rows($count_result);

    $sql =  "SELECT artnr, author, title, \n"
            .boolean_sql_select(
                    boolean_inclusive_atoms($query),
                    $fulltext_key)." as relevance \n"
            ."FROM {$myNewsConf['db']['table']['news']} \n"
            ."WHERE \n"
            .boolean_sql_where($query,$fulltext_key)." \n"
            ."HAVING relevance>0 \n"
            ."ORDER BY relevance DESC LIMIT $show,$count\n";

    // data query
    $result = mysql_query($sql);
    $sqlErr.= myNewsChkSqlErr($result,$sql);

    if(!$sqlErr){
        $result_rows = mysql_num_rows($result);

        if ($total == 0) {
            $_title = ' Search : Error!';
            $_content .= <<<EOT
                  <p>
                <b>Error!</b>
                <blockquote>
                  <p>
                Your Search produced no matches.
                </blockquote>
EOT;

        } else {

            $_title = ' Search : ' . $total . ' item(s)';

            // get results //
            $output = "
                    <table border=0 width=85%>
                      <tr>
                       <th align=left><b>Title</b></th>
                       <th align=left><b>Author</b></th>
                      </tr>
                     ";

            for($ith=0;$ith<$result_rows;$ith++) {
                    $ir=mysql_fetch_row($result);
                    $output .= "
                            <tr>
                             <td><a href=\"$baseStory_URI?mode=storyView&sid={$ir[0]}\">$ir[2]</a></td>
                             <td>$ir[1] </td>
                            </tr>\n";
            }
            $output .= "\n</table>\n"; 

            // get user readable statement //
            $parsed_as = boolean_parsed_as($query);

            $sql = nl2br($sql);

            $debug = 1;

            // display process //
            $_title = 'Search Results - <small>' . $total . ' item(s) matched</small>';


            $_content  .= <<<EOT
                    listing them in order of relevance
                    <blockquote>
                    <p>$output</p>
                    <center>

EOT;


            // Output our navigation if we have multiple pages.
            $baseSearch_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['search'];

            // Check and see if $_POST['query'] is empty.  If so, we can assume we received the query
            // via $_GET[''].  Either way, we need to urlencode it, so it won't break the browser.
            if (empty($_POST)) {
                $search_query = urlencode($_GET['query']);
            } else {
                $search_query = urlencode($_POST['query']);
            }

            $next = $show + $myNewsConf['default']['limit'];
            $back = $show - $myNewsConf['default']['limit'];

            if ($next < $total) {
                if (!isset($show) || $show == 0){$show = 1;}
                $_content  .= "<small>[$show - $next of $total]</small> <br />";
            } else {
                if (!isset($show) || $show == 0){$show = 1;}
                $_content  .= "<small>[$show - $total of $total]</small> <br />";
            }


            if ($back >= 0) {
                $_content  .= '<a href="' . $baseSearch_URI . '?query=' . $search_query . '&show=' . $back . '">' . $myNewsConf['button']['back'] . '</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }

            if ($next < $total) {
                $_content  .= '<a href="' . $baseSearch_URI . '?query=' . $search_query . '&show=' . $next . '">' . $myNewsConf['button']['next'] . '</a>';
            }


            $_content  .= <<<EOT
                    </center>
                    </blockquote>
EOT;

        } // End If
    }
} // End If

$_error = $sqlErr;

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:', $_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
