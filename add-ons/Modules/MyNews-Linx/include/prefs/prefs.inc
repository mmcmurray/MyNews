<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Preferences";
$baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
$basePrefsAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['preferences'];
$submit             = $myNewsConf['button']['submit'];
$now                = date($myNewsConf['format']['date']['nice']);
 
$news_query = mysql_query("select * from " . $myNewsConf['db']['table']['authors'] . " where user='" . $_SESSION['valid_user'] . "'") or die(mysql_error());
 
$row  =  mysql_fetch_assoc($news_query);
	$uid        = $row['artnr'];
	$name       = $row['name'];
	$bio        = $row['bio'];
	$email      = $row['email'];
	$url        = $row['url'];
	$date       = $row['date'];
	$active     = $row['active'];
	$user       = $row['user'];
	$oldpass    = $row['password'];
	$listening  = $row['listening'];
	$reading    = $row['reading'];
	$thinking   = $row['thinking'];


include($myNewsConf['head']);
section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . '/include/login_check.inc'); 
?>

		<p>
		<b>Edit User Info:</b><br>
		<a href="<?=$baseAdmin_URI?>">Go back to admin</a>

<form action="<?=$basePrefsAdmin_URI?>?mode=edit&uid=<?=$uid?>" method="post">
<table width="85%" CELLPADDING=2 CELLSPACING=2 BORDER=0>
	<tr>
		<td align="right" valign="top">
			<b>Name:</b>
		</td>
		<td valign="top">
			<input class="textbox" type="text" name="name" size="40" value="<?=$name?>">
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<b>Bio:</b>
			  <br>
			<i>(Optional)</i>
		</td>
		<td valign="top">
			<TEXTAREA class="textbox" NAME="bio" COLS=50 ROWS=10 WRAP=VIRTUAL><?=$bio?></TEXTAREA>
		</td>
	</tr>

	<tr>
		<td align="right" valign="top">
			<b>Hearing:</b>
			  <br>
			<i>(Optional)</i>
		</td>
		<td valign="top">
			<TEXTAREA class="textbox" NAME="listening" COLS=50 ROWS=5 WRAP=VIRTUAL><?=$listening?></TEXTAREA>
		</td>
	</tr>

	<tr>
		<td align="right" valign="top">
			<b>Reading:</b>
			  <br>
			<i>(Optional)</i>
		</td>
		<td valign="top">
			<TEXTAREA class="textbox" NAME="reading" COLS=50 ROWS=5 WRAP=VIRTUAL><?=$reading?></TEXTAREA>
		</td>
	</tr>

	<tr>
		<td align="right" valign="top">
			<b>Thinking:</b>
			<br>
			<i>(Optional)</i>
		</td>
		<td valign="top">
			<TEXTAREA class="textbox" NAME="thinking" COLS=50 ROWS=5 WRAP=VIRTUAL><?=$thinking?></TEXTAREA>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<b>URL:</b>
		</td>
		<td valign="top">
			<input class="textbox" type="text" name="url" size="40" value="<?=$url?>">
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<b>Email:</b>
		</td>
		<td valign="top">
			<input class="textbox" type="text" name="email" value="<?=$email?>" size="40">
		</td>
	</tr>

	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<p>
			<small>
			Enter Password twice to change it.
			<br>
			Leave the next two fields blank to keep your current password.
			</small>
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<b>Password:</b>
			<br>
		</td>
		<td valign="top">
			<input class="textbox" type="password" name="newpass">
		</td>
	</tr>
	<tr>
		<td align="right" valign="top">
			<b>Confirm Password:</b>
		</td>
		<td valign="top">
			<input class="textbox" type="password" name="confirmpass">
		</td>
	</tr>
	<tr>
		<td>
			<input type="hidden" name="oldpass" value="<?=$oldpass?>">
			<input type="hidden" name="active" value="Yes">
			<input type="hidden" name="date" value="<?=$date?>">
			<input type="hidden" name="user" value="<?=$_SESSION['valid_user']?>">
			<input type="hidden" name="status" value="<?=$_SESSION['status']?>">
		</td>
		<td>
			<?=$submit?>
		</td>
	</tr>
</table>
</form>

<blockquote>
		<small><i>Today is <?=$now?></i></small>
</blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
