<?php
/* $Id: admin.lib.php 495 2005-09-16 14:12:09Z alien $ */

/********************************************************************/
function isAuth() {
/**
 * The purpose of this function is to check and see if the user
 * is authenticated with a $_SESSION['valid_user'] token.
 */
global $myNewsConf;

    session_name('AdminSessID');
    session_start();

    if(!isset($_SESSION['valid_user'])){
        $return = 0;
        $loginURI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['login'] . '?return_to=' . base64_encode($_SERVER['REQUEST_URI']);
        header('Location: ' . $loginURI);
    } else {
        $return = 1;
        header('Login: ' . $_SESSION['valid_user']);
    }
return $return;
}
/********************************************************************/
function loginInfo() {
/**
 * This function prints out the pertinent info of the person logged in
 * and presents links to the preferences, and logout scripts.
 */
global $myNewsConf;

    $output = <<<EOT

        <small>
        &nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;
        Logged in as {$_SESSION['valid_user']}
        with status of {$_SESSION['status']}
        &nbsp;&nbsp;&nbsp;
        (
        <a href="{$myNewsConf['path']['web']['admin']}{$myNewsConf['adminScripts']['preferences']}">preferences</a>
        |
        <a href="{$myNewsConf['path']['web']['admin']}{$myNewsConf['adminScripts']['logout']}">logout</a>
        )
        </small>

EOT;

return $output;
}
/********************************************************************/
function chkPerm($list,$author='') {
/**
 * This function checks a list of "valid" groups and allows returns an
 * an error if the user's group does not fit within the guidlines.
 */
    $groupArray = explode(':',$list);

    if($_SESSION['status'] == 'Guest'){
        $returnArray['title']   = 'Error';
        $returnArray['content'] = myNewsError(110, 'You have insufficient credentials');
        return $returnArray;
    }

    if(!in_array($_SESSION['status'], $groupArray)){
        $returnArray['title']   = 'Error';
        $returnArray['content'] = myNewsError(110, 'You have insufficient credentials');
        return $returnArray;
    }

    if(in_array('Author',$groupArray) && !empty($author)){
        if($author != $_SESSION['valid_user'] && $_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor'){
            $returnArray['title']   = 'Error';
            $returnArray['content'] = myNewsError(110, 'You have insufficient credentials');
            return $returnArray;
        }
    }
}
/********************************************************************/
function fixWordShit($text) {
/**
 * The Purpose of this function is to change the garb that MSWORD 
 * Cut/Pastes leave in the article.  This fixes a problem of editing
 * an item that has been written in WORD originally and when submitting
 * the edited item, most of the text is gone.
 *
 * Ex:  fixWordShit($text);
 */
	$text = str_replace("&#8220;", "\"", $text);
	$text = str_replace("&#8221;", "\"", $text);
	$text = str_replace("&#8216;", "'", $text);
	$text = str_replace("&#8217;", "'", $text);
	$text = str_replace("&#8230;", "...", $text);

return $text;
}
/********************************************************************/
function genRdf(){
/**
 * This function generates a defined RDF file for channeling the NEWS
 * table if the default:channel key is 'on';
 */
global $myNewsConf;
    if ($myNewsConf['default']['channel'] == 'on') {
        require($myNewsConf['path']['sys']['index'] . '/include/classes/mysql_rdf.class');

        $genrdf = new mysql_rdf;

        $genrdf->host     =	$myNewsConf['db']['hostname'];
        $genrdf->db       = $myNewsConf['db']['dbName'];
        $genrdf->login    = $myNewsConf['db']['dbUser'];
        $genrdf->password = $myNewsConf['db']['dbPass'];

        $genrdf->title_field = 'title';
        $genrdf->story_path  = $myNewsConf['default']['siteurl'] . '/' . $myNewsConf['scripts']['story'] . '?mode=view&amp;sid=';
        $genrdf->link_field  = 'artnr';

        $genrdf->sql        = 'SELECT * FROM ' . $myNewsConf['db']['table']['news'] . ' WHERE active = 1 ORDER BY artnr DESC limit 10';

        $genrdf->rdf_title = $myNewsConf['default']['sitename'];
        $genrdf->rdf_link  = $myNewsConf['default']['siteurl'];
        $genrdf->rdf_descr = $myNewsConf['default']['desc'];
        $genrdf->rdf_lang  = 'en-us';

        $genrdf->rdf_encoding = 'UTF-8';
        $genrdf->connect($myNewsConf['path']['sys']['index'] . '/' . $myNewsConf['default']['channel_file']); 
    }
}
/********************************************************************/
?>
