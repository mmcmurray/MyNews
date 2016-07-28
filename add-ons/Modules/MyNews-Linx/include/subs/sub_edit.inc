<?
$news_query = mysql_query("select * from " . $myNewsConf['db']['table']['submissions'] . " where artnr = " . $_GET['sid']) or die(mysql_error());
$row        = mysql_fetch_assoc($news_query);
        $sid            = $row['artnr'];
        $title          = $row['title'];
        $author         = $row['author'];
        $previewtext    = $row['previewtext'];
        $email          = $row['email'];
        $date           = $row['date'];
        $section        = $row['section'];

$TITLE              = $myNewsConf['default']['sitename'] . ' Admin : Submit : ' . $title;
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];
$submit             = $myNewsConf['button']['submit'];
$now                = date($myNewsConf['format']['date']['nice']);

include($myNewsConf['head']);
section_header('Admin','100%','content');
 
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc"); 
?>

		<p>
		<b>Add Submission</b><br>
		<A HREF="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
<blockquote>
		<p>
		<b>Author:</b> <?=$author?> <br />
		<b>Email:</b> <?=$email?> <br />
		<b>Title:</b> <?=$title?> <br />
		<b>Item Text:</b>
<blockquote>
		<p>

<?
echo mynews_format($previewtext);
?>

</blockquote>
		<p>
   <form action="<?=$baseNewsAdmin_URI?>?mode=sub_posted" method="post">
		<b>Section:</b>
		  <br>
	<SELECT class=textbox NAME="section">
	<OPTION><?=$section?></OPTION>

<?
include($myNewsConf['path']['sys']['admin'] . "/include/seclist.inc");
?>


	</SELECT>
		  <br>
		  <br>
		<b>Title:</b>
		  <br>
	<input class=textbox type="text" name="title" size="40" value="<?=$title?>">
		  <br>
		  <br>
		<b>Item Text:</b>
		  <br>
	<TEXTAREA class=textbox NAME="previewtext" COLS=50 ROWS=20 WRAP=VIRTUAL><?=$previewtext?></TEXTAREA>
		  <br>
		  <br>
	<input type="hidden" name="author" value="<?=$_SESSION['valid_user']?>">
	<input type="hidden" name="email" value="">
		<?=$submit?>
   </form>
		<small><i>Today is: <?=$now?></i></small>
</blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
