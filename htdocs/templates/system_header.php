<?php
/* $Id: system_header.php 500 2005-09-20 04:43:05Z alien $ */

header('Cache-Control: no-cache, must-revalidate');
if (!isset($charset)) $charset='iso-8859-1';
header('Content-Type: text/html; charset='.$charset);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
    <title><?php echo $myNewsConf['default']['sitename'] . ' ' .  strip_tags($_title) ?></title>

<?php

// Insert our meta tags if $_meta is set.
if(isset($_meta)) echo $_meta;

// If our css file exists, we need to insert a call to it.
if (file_exists($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.css')) {
    print <<<EOT
    <link rel="STYLESHEET" type="text/css" href="{$myNewsConf['path']['web']['index']}templates/{$myNewsConf['default']['template']}/{$myNewsConf['default']['template']}.css">

EOT;
}

if (file_exists($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/' . $myNewsConf['default']['template'] . '.js')) {
    print <<<EOT
    <script language="JavaScript" type="text/javascript" src="{$myNewsConf['path']['web']['index']}templates/{$myNewsConf['default']['template']}/{$myNewsConf['default']['template']}.js"></script>

EOT;
}
        
// Insert our javascript if $_jscript is set.
if (isset($_jscript)) echo $_jscript;
?>

</head>

<?php
if (file_exists($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/images/' . $myNewsConf['default']['template'] . '_background.gif')) {
    print <<<EOT
    <body background="{$myNewsConf['path']['web']['index']}templates/{$myNewsConf['default']['template']}/images/{$myNewsConf['default']['template']}_background.gif">
EOT;
} else {
    echo '<body>';
}

/********************************************************************/
/*
 * Include important Defs file.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/globaldefs.php');

/*
 * Initiate the template
 */
$tpl = new Template($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template']);
$tpl->set_file(array( 'layout' => 'layout.tpl'));

/*
 * Define the Header title
 */
$tpl->set_var('main_title', $myNewsConf['default']['title_header']);

/*
 * Define the Title
 */
$tpl->set_var('title', $_title);

/*
 * Insert the seach code
 */
$tpl->set_var('search_box', $_search);

/*
 * Insert the toolbar
 */
// If we a toolbar.tpl file exists, let's parse it.
if(file_exists($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template'] . '/toolbar.tpl')){
    $tbar = new Template($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template']);
    // Here we define which template file we want to parse for the toolbar.
    $tbar->set_file(array('toolbar' => 'toolbar.tpl'));

    // Here is where we will define our toolbar links.
    // These should only be effective it a toolbar.tpl exists.
    $tbar->set_var('tb_home',   $myNewsConf['path']['web']['index']);
    $tbar->set_var('tb_archive',$myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['archive']);
    $tbar->set_var('tb_about',  $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about']);
    $tbar->set_var('tb_authors',$myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['about'] . '?mode=auth_list');
    $tbar->set_var('tb_hof',    $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['hof']);
    $tbar->set_var('tb_cal',    $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['calendar']);
    $tbar->set_var('tb_search', $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['search']);
    $tbar->set_var('tb_adm',    $myNewsConf['path']['web']['admin']);

    // Go ahead and parse out the toolbar template.
    $_toolbar = $tbar->parse('out','toolbar');
}
// Output the toolbar data.
$tpl->set_var('toolbar', $_toolbar);

/*
 * Insert the date
 */
$tpl->set_var('date', $_date);

/*
 * Insert the submit text
 */
$tpl->set_var('submit', $_submit);

/*
 * Insert our sidebars into the template
 */
$tpl->set_var('left_nav', mynews_sidebar('left',200));
$tpl->set_var('right_nav', mynews_sidebar('right',200));
/********************************************************************/
?>
