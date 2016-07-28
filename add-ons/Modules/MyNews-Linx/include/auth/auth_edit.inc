<?
$news_query = mysql_query("select * from " . $myNewsConf['db']['table']['authors'] . " where artnr = " . $_GET['uid']) or die(mysql_error());
$row        = mysql_fetch_assoc($news_query);
    $uid        = $row['artnr'];
    $name       = $row['name'];
    $bio        = $row['bio'];
    $auth_level = $row['status'];
    $email      = $row['email'];
    $url        = $row['url'];
    $date       = $row['date'];
    $active     = $row['active'];
    $login      = $row['user'];
    $oldpass    = $row['password'];
    $listening  = $row['listening'];
    $reading    = $row['reading'];
    $thinking   = $row['thinking'];

$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Edit : $name";
$baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];
$submit             = $myNewsConf['button']['submit'];
$now                = date($myNewsConf['format']['date']['nice']);

include($myNewsConf['head']);
section_header('Members','100%','content');

include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p>
<b>Edit Author Info:</b><br>
<a href="<?=$baseAuthAdmin_URI?>">Go back to admin</a><BR><BR>

<form action="<?=$baseAuthAdmin_URI?>?mode=change&uid=<?=$uid?>" method="post">
<table WIDTH="100%" CELLPADDING=2 CELLSPACING=2 BORDER=0>
	<tr>
		<td align="Right" Valign="top">
			<p>
			Name:
		</td>
		<td Valign="top">
			<input class="textbox" type="text" name="name" size=40 value="<?=$name?>">
		</td>
	</tr>
	<tr>
		<td align="Right" Valign="top">
			<p>
			Bio:
		</td>
		<td Valign="top">
			<textarea class="textbox" NAME="bio" COLS=50 ROWS=5 WRAP="PHYSICAL"><?=$bio?></textarea>
		</td>
	</tr>
	<tr>
		<td align="Right" Valign="top">
			<p>Hearing:
		</td>
		<td Valign="top">
			<textarea class="textbox" NAME="listening" COLS=50 ROWS=5 WRAP="PHYSICAL"><?=$listening?></textarea>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>Reading:
		</td>
		<td Valign="top">
			<textarea class="textbox" NAME="reading" COLS=50 ROWS=5 WRAP="PHYSICAL"><?=$reading?></textarea>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>Thinking:
		</td>
		<td Valign="top">
			<textarea class="textbox" NAME="thinking" COLS=50 ROWS=5 WRAP="PHYSICAL"><?=$thinking?></textarea>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>URL:
		</td>
		<td Valign="top">
			<input class="textbox" type="text" name="url" size=40 value="<?=$url?>"><br>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
<p>Email:
		</td>
		<td Valign="top">
			<input class="textbox" type="text" name="email" value="<?=$email?>" size=40><br>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>Status:
		</td>
		<td Valign="top">
			<SELECT class="textbox" NAME="auth_level">
                <OPTION><?=$auth_level?>
                <OPTION>Author
                <OPTION>Editor
                <OPTION>Admin
            </SELECT>
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>Username:
		</td>
		<td Valign="top">
			<input class="textbox" type="text" name="login" value="<?=$login?>" size=40><br>
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<p>
			Leave blank to keep current password.<br>
			Complete the password field to change the password.
		</td>
	</tr>

	<tr>
		<td align="Right" Valign="top">
			<p>Password:
		</td>
		<td Valign="top">
			<input class="textbox" type="password" name="newpass" size=40>
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<?=$submit?>
		</td>
	</tr>
</table>
			<input type="hidden" name="oldpass" value="<?=$oldpass?>">
			<input type="hidden" name="active" value="yes">
			<input type="hidden" name="date" value="<?=$date?>">
</form>
<blockquote>
<small><i>Today is: <?=$now?></i></small>
</blockquote>


<?
section_footer('Members','content');
include($myNewsConf['foot']);
?>
