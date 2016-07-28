<?php 
/* $Id: index.php 444 2004-08-12 17:05:33Z alien $ */

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
// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

// Connect to the database
mynews_connect();

/*
 * Here we build our query to find the section name of the last
 * posted article that appears in a section flagged for the 
 * front page
 */
$query = '
    SELECT
        a.title,
        a.section,
        b.front
    from
        ' . $myNewsConf['db']['table']['news'] . ' as a
    LEFT JOIN
        ' . $myNewsConf['db']['table']['sections'] . ' as b
    ON
        a.section   = b.section
    WHERE
        b.front     = 1
    AND
        a.active    = 1
    GROUP by 1
    ORDER by    
        artnr desc
    LIMIT 1';
$result = mysql_query($query);
$sqlErr = myNewsChkSqlErr($result, $query);

if(!$sqlErr){
    $row    = mysql_fetch_assoc($result);
    extract($row);

    $output = "\n" . '<!-- Begin ' . $section . ' Row -->';

    /*
     * We then build our HTML for it as it is our top "headline"
     * for lack of a better word
     */

    // Here we assign the results of top_story() to an array we can work with.
    $contentHash    = top_story($section);
    $headlinHash   = headlines($section,1,$myNewsConf['default']['count']);

    $output.= $contentHash['content'];
    $output.= $headlinHash['content'];

    $output.= "\n" . '<!-- End ' . $section . ' Row -->';

    $_content  = makebox($contentHash['title'],$output,'content');
}

/*
 * Now we build our query to exclude the section that we just echoed
 * and loop through sections flagged for the front page, and randomize
 * their order.
 */
$query  = '
    SELECT
        *
    FROM
        ' . $myNewsConf['db']['table']['sections'] . '
    WHERE
        front = 1
    AND
        section != "' . $section . '"
    ORDER by
        RAND()';
$result = mysql_query($query);
$sqlErr.= myNewsChkSqlErr($result, $query);

if(!$sqlErr){
    while($row = mysql_fetch_assoc($result)) {
        extract($row);

        // Here we assign the results of top_story() to an array we can work with.
        $contentHash    = top_story($section);
        $headlinHash    = headlines($section,1,$myNewsConf['default']['count']);

        $output = $contentHash['content'];
        $output.= $headlinHash['content'];

        $_content .= makebox($contentHash['title'], $output, 'content');

    } //End While Loop
}

$_error     = $sqlErr;
$_jscript   = $contentHash['jscript'];
$_meta      = $contentHash['meta'];
$_title     = ' ' . $myNewsConf['default']['desc'];

// We only want to build the error box if errors exist
if($_error) $_error = makebox('Error:',$_error, 'content');

/*
 * Require the header file
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
