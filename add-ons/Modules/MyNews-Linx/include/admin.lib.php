<?
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
?>
<?
/********************************************************************/
function cmEditList($topic_id=0, $level=0, $type='news'){
/**
 * This function displays comments posted to a particular   
 * story                            
 *
 * Ex:  cmEditList($id);                 
 */
global $myNewsConf;

    $comments = 0;
    if($level == 0){
        $query = "select artnr, cmtnr, cmtitle, cmauthor, cmdate, commenttext from "
            . $myNewsConf['db']['table']['comments'] . " where artnr = $topic_id and pid = 0 and type = '$type'"
            . " order by cmdate, artnr";
    } else {
        $query = "select artnr, cmtnr, cmtitle, cmauthor, cmdate, commenttext from "
            . $myNewsConf['db']['table']['comments'] . " where pid = $topic_id and type = '$type'"
            . " order by cmdate, artnr";
    }

    $result = mysql_query($query);

    while($row = mysql_fetch_assoc($result)){
        $sid        =   $row['artnr'];
        $tid        =   $row['cmtnr'];
        $title      =   $row['cmtitle'];
        $author     =   $row['cmauthor'];
        $timestamp  =   $row['cmdate'];
        $comment    =   $row['commenttext'];
        if (!$level){
            if (!$comments){
print <<<EOT
<!-- Begin Comments -->
        <ul>
EOT;
            }

        } else {
            if (!$comments) {
                print "<ul>\n";
            }
            print '<li class="nest">';
        }
        $comments++;
        if (!eregi("[a-z0-9]",$author)) { $author = "[no name]"; }
        if ($tid != $topic_id){

            $baseNewsAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
print <<<EOT

        $title by <b>$author</b>
		&nbsp;&nbsp;&nbsp;&nbsp;
		(<a href="$baseNewsAdmin_URI?mode=comments_edit&cid=$tid">edit</a>
		|
		<a href="javascript:confirmChildDelete('$baseNewsAdmin_URI?mode=comments_deleted&cid=$tid&sid=$sid','$sid','$title')">delete</a>)
EOT;
        } else {
print <<<EOT

    $title by <b>$author</b>
EOT;
        }

        if (!$level) {print "<br />\n";}
        cmEditList($tid, $level+1,$type);
    }
    if ($level && $comments) {
        print "</ul>\n";
    }

} //End cmEditList()
/********************************************************************/
?>
<?
/********************************************************************/
function cmChildDelete($topic_id=0, $level=0, $type='news'){
/**
 * This function displays comments posted to a particular   
 * story                            
 *
 * Ex:  cmChildDelete($sid);                 
 */
global $myNewsConf;

    $comments = 0;
    if($level == 0){
        $query = "select artnr, cmtnr, cmtitle, cmauthor, cmdate, commenttext from "
            . $myNewsConf['db']['table']['comments'] . " where artnr = $topic_id and pid = 0"
            . " order by cmdate, artnr";
    } else {
        $query = "select artnr, cmtnr, cmtitle, cmauthor, cmdate, commenttext from "
            . $myNewsConf['db']['table']['comments'] . " where pid = $topic_id "
            . " order by cmdate, artnr";
    }

    $result = mysql_query($query);

    while($row = mysql_fetch_assoc($result)){
        $sid        =   $row['artnr'];
        $tid        =   $row['cmtnr'];
        $title      =   $row['cmtitle'];
        $author     =   $row['cmauthor'];
        $timestamp  =   $row['cmdate'];
        $comment    =   $row['commenttext'];
        if (!$level){
            if (!$comments){
print <<<EOT
<!-- Begin Comments -->
	<p>
	<b>Deleted the following Children</b>
        <ul>
EOT;
            }

        } else {
            if (!$comments) {
                print "<ul>\n";
            }
            print '<li class="nest">';
        }
        $comments++;
        if (!eregi("[a-z0-9]",$author)) { $author = "[no name]"; }
        if ($tid != $topic_id){

            $baseComments_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];
print <<<EOT

        $title by <b>$author</b> &nbsp;&nbsp;&nbsp;&nbsp; <b>Deleted!</b>
EOT;
			$delQuery   =   "DELETE FROM " . $myNewsConf['db']['table']['comments'] . " WHERE cmtnr = " . $tid;
			$delete     =   mysql_query($delQuery);
        } else {
print <<<EOT

    $title by <b>$author</b>
EOT;
        }

        if (!$level) {print "<br />\n";}
        cmChildDelete($tid, $level+1, $type);
    }
    if ($level && $comments) {
        print "</ul>\n";
    }

} //End cmChildDelete()
/********************************************************************/
?>
