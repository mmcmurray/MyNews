<?php
/* $Id: test.php,v 4.3 2004/08/12 17:05:33 alien Exp $ */

define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('ADMIN_ROOT', $myNewsConf['path']['sys']['admin']);
require_once(ADMIN_ROOT . '/include/admin.lib.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.class.php');
include(MYNEWS_ROOT . '/templates/template.functions.php');
/*************** Begin Code *****************************************/
// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

// If the provided file includes the predefined 'private' string, we need to require authentication.
$private    = false;
if($myNewsModule['docrep']['private']){
    if(eregi($myNewsModule['docrep']['private'],$_GET['file'])){
        $auth = isAuth();
        $private = true;
    }
}

// Generate the full path to the file.
$file       = $myNewsConf['path']['sys']['index'] . $_GET['file'];

// Check the file type, so we can determine whether to display it or not.
$dsplyTypes = array('txt','html','htm');
$parts      = pathinfo($file);
$extension  = $parts['extension'];

if(in_array($extension,$dsplyTypes) && !$_GET['mode']){
    $_GET['mode']   = 'main';
} else {
    $_GET['mode']   = 'show';
}

switch($_GET['mode']){
    case 'main':
        if(!$private){
            $fSource = $_GET['file'];
        } else {
            $fSource = $_SERVER['PHP_SELF'] . '?mode=show&file=' . $_GET['file'];
        }
        $fname  = prsFileName($_GET['file']);
        //$_title = '<a href="' . $_GET['file'] . '">' . str_replace('%20',' ',$fname) . '</a>';
        if($extension == 'txt'){
            $_title = '<b>' . str_replace('%20',' ',$fname) . '</b>';
            $_content.= "\n" . '<br />';
            $_content.= "\n" . nl2br(displayFile($file,$extension));
        } else {
            $_iframe.= "\n" . '<iframe name="iframe" src="' . $fSource . '" width="100%" height="1000" frameborder="0" scrolling="auto"></iframe>';
        }
        break;
    case 'show':
        displayFile($file,$extension);
        die;
        break;
}

/*
 * Require the header
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

/********************************************************************/

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:',$_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Insert our content into the template
 */
$tpl->set_var('error',$_error);
$tpl->set_var('main_content', $_content);
$tpl->set_var('comments','');
/********************************************************************/

/*
 * Require the footer
 */
require($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');

/*************** Functions  *****************************************/
function displayFile($file,$extension){
global $dsplyTypes;


    // Stupid transition from http to https translates %20s into %2520s.  We need it to NOT DO THAT
    $file       = str_replace('%2520',' ',$file);
    $file       = str_replace('%20',' ',$file);

    // Since we can't figure out how to get the fucking MIME Magic shit working, we'll pretend we
    // know what were doing.
    $extDict    = array('txt'=>'text/html','pdf'=>'application/pdf','doc'=>'application/msword','exe'=>'application/octet-stream');
    if($extDict[$extension]){
        $mtype = $extDict[$extension];
    } else {
        $mtype = 'application/octet-stream';
    }

    // Make sure nobody tries anything naughty.
    if(strstr($file, '../')){
        $output = myNewsError(110,'You\'re trying to do something bad.');
    // If the file doesn't exist, we tell the user.
    } elseif(!is_readable($file)){
        $output = myNewsError(1,'File does not exist');
    // Now we can proceed.
    } else {
        if(in_array($extension,$dsplyTypes)){
            $fn     = $file;
            $fps    = filesize($file);
            $fpc    = fopen ($fn, 'rb'); // read-only
            if (!$fpc) $_error = myNewsError(1,'ERROR: Unable to open remote file!');
            $output.= fread($fpc, $fps);
            fclose ($fpc); 
        } else {
            $fp     = fopen($file, 'rb');
            $fps    = filesize($file);
            # eg. $filename="setup.abc.exe";
            if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
                # workaround for IE filename bug with multiple periods / multiple dots in filename
                # that adds square brackets to filename - eg. setup.abc.exe becomes setup[1].abc.exe
                $iefilename = preg_replace('/\./', '%2e', $file, substr_count($file, '.') - 1);
                $fpn = basename ($iefilename);
            } else {
                $fpn = basename ($file);
            }
            header('Pragma: ');
            header('Cache-Control: ');
            header('Content-Type: ' . $mtype);
            header('Content-Disposition: attachment; filename="'.$fpn.'"');
            header('Content-Length: ' . $fps);
            while(!feof($fp)) {
                //$stream.= fread($fp, 1024);
                echo fread($fp, 1024);
                flush();
            }
            fclose($fp);
            exit;
        }
    }
return $output;
}
/********************************************************************/
function prsFileName($file){
    $parts  = explode('/',$file);
    $count  = count($parts);
    $fname  = $parts[($count - 1)];

return $fname;
}
/********************************************************************/
?>
