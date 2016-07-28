<?
$query  = mysql_query("select * from " . $myNewsConf['db']['table']['comments'] . " where cmtnr = " . $_GET['cid']) or die(mysql_error());
$row    = mysql_fetch_assoc($query);
	$cmtitle        = $row['cmtitle'];
	$pid            = $row['pid'];
	$commenttext    = $row['commenttext'];
	$cmauthor       = $row['cmauthor'];
	$cmemail        = $row['cmemail'];
	$cmdate         = $row['cmdate'];
	$cmip           = $row['cmip'];
	$sid            = $row['artnr'];
	$type           = $row['type'];

$cid =	$_GET['cid'];

$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Edit : $cmtitle";
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
$now                = date($myNewsConf['format']['date']['nice']);
$submit             = $myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] !='Admin' && $_SESSION['status'] !='Editor' && $cmauthor != $_SESSION['valid_user']){

	print ("<p><b>Error!</b><blockquote><p>You do not have permission to edit this comment</blockquote>");

} else {
?>

<br />
<br />
<b>Edit Comment:</b> <i><?=$cmtitle?></i><br>
<A HREF="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
<blockquote>
   <form action="<?=$baseNewsAdmin_URI?>?mode=comments_edited&cid=<?=$cid?>&sid=<?=$sid?>" method="post">
		  <br>
		<b>Comment Title:</b>
		  <br>
	<input class=textbox type="text" name="cmtitle" size="40" value="<?=$cmtitle?>">
		  <br>
		<b>Comment Text:</b>
		  <br>
	<TEXTAREA class=textbox NAME="commenttext" COLS=50 ROWS=20 WRAP=VIRTUAL><?=$commenttext?></TEXTAREA>
	<input type="hidden" name="cmauthor" value="<?=$cmauthor?>">
	<input type="hidden" name="cmemail" value="<?=$cmemail?>">
	<input type="hidden" name="cmdate" value="<?=$cmdate?>">
	<input type="hidden" name="cmip" value="<?=$cmip?>">
	<input type="hidden" name="type" value="<?=$type?>">
	<input type="hidden" name="pid" value="<?=$pid?>">
		  <br>
		  <br>
		<?=$submit?>
   </form>
		<small><i>Today is: <?=$now?></i></small>
</blockquote>

<?
}

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
