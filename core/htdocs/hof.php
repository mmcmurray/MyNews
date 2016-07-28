<?php
/* $Id: hof.php 444 2004-08-12 17:05:33Z alien $ */

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

$_title	= ' Hall Of Fame ::';
$baseStory_URI	= $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['story'];
$baseAbout_URI	= $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about'];

/*
 * Open a database connection
 */
mynews_connect();

/*
 * Begin Overall Site Stats
 */
$_content.= <<<EOT

<br />
&nbsp;&nbsp;&nbsp;<b>Site Statistics:</b>
<blockquote>
  <table border="0" cellspacing="0" cellpadding="0" width="85%">

EOT;

/*
 * Story Counter
 */
$query = ("
        SELECT
            count(artnr) as sCount
        FROM " . $myNewsConf['db']['table']['news']);

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);
$row    = mysql_fetch_assoc($result);
extract($row);

$_content.= <<<EOT
    <tr>
        <td width="85%">
            <p>Stories in the System
        </td>
        <td align="right">
            <i>$sCount</i>
        </td>
    </tr>

EOT;

/*
 * Comment Counter 
 */
$query  = ("
        SELECT
            count(cmtnr) as cCount
        FROM " . $myNewsConf['db']['table']['comments']);

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);
$row    = mysql_fetch_assoc($result);
extract($row);

$_content.= <<<EOT
    <tr>
        <td width="85%">
            <p>Comments in the System
        </td>
        <td align="right">
            <i>$cCount</i>
        </td>
    </tr>

EOT;

/*
 * Total Story View Counter
 */

$query =  ("
        SELECT
            sum(viewcount) as totalcount,
            avg(viewcount) as average
        FROM " . $myNewsConf['db']['table']['news'] . "
        order by viewcount");

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);
$row    = mysql_fetch_assoc($result);
extract($row);

$average = round($average);

$_content.= <<<EOT

    <tr>
        <td width="85%">
            <p>Total Story Views
        </td>
        <td align="right">
            <i>$totalcount</i>
        </td>
    </tr>
    <tr>
        <td width="85%">
            <p>Average View per Story
        </td>
        <td align="right">
            <i>$average</i>
        </td>
    </tr>
 </table>
</blockquote>

&nbsp;&nbsp;&nbsp;
<B>Most Viewed Stories:</B>
<blockquote>
  <table  border="0" cellspacing="0" cellpadding="0" width="85%">
    <tr>
        <td align="left"><b>Title:</b></td>
        <td align="left"><b>Views:</b></td>
    </tr>
    <tr>
        <td colspan=2>&nbsp;</td>
    </tr>

EOT;

$query = ("
        SELECT
            artnr,
            title,
            viewcount
        FROM " . $myNewsConf['db']['table']['news'] . "
        order by viewcount
        desc limit 10");

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);

while($row = mysql_fetch_array($result)) {
    extract($row);
    $_content.= <<<EOT

    <tr>
        <td width="85%">
            <a href="$baseStory_URI?mode=storyView&sid=$artnr">$title</a>
        </td>
        <td align="right">
            <i>$viewcount</i>
        </td>
    </tr>

EOT;
}

$_content.= <<<EOT
  </table>
</blockquote>

EOT;

/*
 * Begin active stories HOF
 */

$_content.= <<<EOT

&nbsp;&nbsp;&nbsp;
<B>Most Active Stories:</B>
<blockquote>
  <table border="0" cellspacing="0" cellpadding="0" width="85%">
    <tr>
        <td align="left"><B>Story:</B></td>
        <td align="left"><B>Comments:</B></td>
    </tr>
    <tr>
        <td colspan=2>&nbsp;</td>
    </tr>

EOT;

$query = ("
        SELECT
            a.artnr as sid,
            a.title as title,
            sum(if(b.artnr is NULL,0,1)) as count 
        FROM
            " . $myNewsConf['db']['table']['news'] . " as a
        LEFT JOIN
            " . $myNewsConf['db']['table']['comments'] . " as b
        ON a.artnr=b.artnr
        AND b.type = 'news'
        WHERE active=1
        GROUP by sid HAVING count >= 1
        ORDER by count desc
        LIMIT 10");

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);

while($row = mysql_fetch_array($result)) {
    extract($row);
    $_content.= <<<EOT
    <tr>
      <td width="85%">
      <a href="$baseStory_URI?mode=storyView&sid=$sid">$title</a>
      </td>

      <td align="right">
        <i>$count</i>
      </td>
    </tr>
EOT;
}

$_content.= <<<EOT
  </table>
</blockquote>

EOT;


/*
 * Begin active authors HOF
 */


$_content.= <<<EOT

&nbsp;&nbsp;&nbsp;
<B>Most Active Authors:</B>
<blockquote>
  <table border="0" cellspacing="0" cellpadding="0" width="85%">
    <tr>
        <td align="left"><B>Author:</B></td>
        <td align="left"><B>Stories:</B></td>
    </tr>
    <tr>
        <td colspan=2>&nbsp;</td>
    </tr>

EOT;

$query = ("
    select
        distinct(author),
        count(author) as namecnt
    from " . $myNewsConf['db']['table']['news'] . "
    group by author
    order by namecnt
    desc limit 5");

$result = mysql_query($query);
$_content.= myNewsChkSqlErr($result,$query);

while($row = mysql_fetch_array($result)) {
    extract($row);
    $_content.= <<<EOT

    <tr>
        <td width="85%">
            <a href="$baseAbout_URI?mode=zoom&author=$author">$author</a>
        </td>
        <td align="right">
            <i>$namecnt</i>
        </td>
    </tr>

EOT;

}

$_content .= <<<EOT
  </table>
</blockquote>

EOT;

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:', $_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

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
