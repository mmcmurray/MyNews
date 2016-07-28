<?
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Authors : Add New User";
$baseAdmin_URI      = $myNewsConf['path']['web']['admin'];
$baseAuthAdmin_URI  = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['authors'];
$submit             = $myNewsConf['button']['submit'];
$now                = date($myNewsConf['format']['date']['nice']);

include($myNewsConf['head']);
section_header('Members','100%','content');
?>

<p>
<b>Add a new user:</b><br>
<a href="<?=$baseAuthAdmin_URI?>">Go back to admin</a>
<p>
<form action="<?=$baseAuthAdmin_URI?>?mode=added_article" method="post">
<TABLE WIDTH="100%" CELLPADDING=3 CELLSPACING=0 BORDER=0>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>Name:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td  VALIGN=TOP>
<input class="textbox" type="text" name="name" size=40>
	</td>
   </tr>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>URL:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td  VALIGN=TOP>
<input class="textbox" type="text" name="url" size=40><br>
	</td>
   </tr>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>Email:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td VALIGN=TOP>
<input class="textbox" type="text" name="email" size=40><br>
	</td>
   </tr>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>Status:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td VALIGN=TOP>
<SELECT class="textbox" name="auth_level"><OPTION>Author<OPTION>Editor<OPTION>Admin</SELECT>
	</td>
   </tr>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>Username:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td VALIGN=TOP>
<input class="textbox" type="text" name="login" size=40><br>
	</td>
   </tr>
   <tr>
	<td ALIGN="Right" VALIGN="TOP" >
<p><b>Password:</b>&nbsp;&nbsp;&nbsp;&nbsp;
	</td>
	<td VALIGN="TOP">
<input class="textbox" type="password" name="newpass" size=40><br>
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
</TABLE>
<input type="hidden" name="active" value="yes">
<input type="hidden" name="bio" value="">
</form>
<blockquote>
<small><p><i>Today is: <?=$now?></i></small>
</blockquote>

<?
section_footer('Members','content');
include($myNewsConf['foot']);
?>
