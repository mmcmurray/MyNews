<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Comments : Select Commented Item";
$baseNewsAdmin_URI  = $myNewsConf['path']['web']['path'] . $myNewsConf['adminScripts']['news'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p>
<b>Select Item to Edit Comments</b><br>
<a href="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
<p>
<table border=0 width=85%>
   <tr>
	<td>
	  &nbsp;
	</td>
	<td>
	  <b>Title:</b>
	</td>
	<td>
	  <b>Comments:</b>
	</td>
   </tr>

<?
if (!isset($_GET['show'])){ $show=0;} else {$show = $_GET['show'];}

$query = mysql_query("SELECT count(cmtnr) as count,artnr FROM " . $myNewsConf['db']['table']['comments'] . " WHERE type='news' GROUP BY artnr order by artnr desc LIMIT $show, " . $myNewsConf['default']['limit']) or die (mysql_error());
WHILE ($row = mysql_fetch_assoc($query)){
	$count  = $row["count"];
	$sid    = $row["artnr"];

	$t_query    = mysql_query("select title from " . $myNewsConf['db']['table']['news'] . " where artnr = $sid")or die(mysql_error());
	$row        = ($row = mysql_fetch_assoc($t_query));
		$title  = $row["title"];
?>

	<tr>
	  <td>
		(<a href="<?=$baseNewsAdmin_URI?>?mode=comments_edit_list&sid=<?=$sid?>">View</a>)
	  </td>
	  <td>
		<p>
		<?=$title?>
	  </td>
	  <td>
		<i><?=$count?></i>
	  </td>
	</tr>

<?
} // End While
?>

</table>

<?
include($myNewsConf['path']['sys']['admin'] . "/include/comments/comments_select_nav.inc");
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
