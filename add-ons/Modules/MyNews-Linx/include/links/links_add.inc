<?
$TITLE = $myNewsConf['default']['sitename'] . " Admin : Links : Add New Link";
include($myNewsConf['head']);

print <<<EOT

<script language="JavaScript">
<!--

function isOK(){ 
  with(document.the_form){
    if(section.value == 'select'){
      alert("Please choose a section for this item. \\nor enter a new section name");
      return false;
    }

    if(section.value == 'none' && new_section.value == '' ){
      alert("Please enter a new section name.");
      return false;
    }
  }
  return true;
}

//-->
</script>

EOT;

section_header('Admin','100%','content');
include($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$baseLinksAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'];
$now =					date($myNewsConf['format']['date']['nice']);
$submit =				$myNewsConf['button']['submit'];
?>
		<p>
		<b>Add a New Link.</b>
		  <br />
		<A HREF="<?=$baseLinksAdmin_URI?>">Go back to admin</a>
<blockquote>
   <form action="<?=$baseLinksAdmin_URI?>?mode=links_added" method="post" name="the_form" onSubmit="return isOK()">
		<P>
		<b>1)</b> Choose/Create Category:
		  <br />
		  <table border=0>
		  <tr>
		  <td valign=top>
	   <select class="textbox" name="category" OnChange="if(this.value) { this.form.new_category.value = ''} else { this.select(); }">
		   <option value="select">-- Select</option>
		   <option value="none">-- Create</option>

<?
include($myNewsConf['path']['sys']['admin'] . '/include/categorylist.inc');
?>
	   </select>
		  </td>
		  <td>
		<input class="textbox" type="text" name="new_category" size="30" value="-New Category-" maxlength="20">
		<br />
		<small><b>Note:</b> <i>You only need to complete the above field if you are creating a new category</i></small>
		  </td>
		  </tr>
		  </table>
		</P>

		<P>
		<b>2)</b> Choose/Create Sub-Category:
		<br />
		<table border=0>
		<tr>
		<td valign=top>
 		<select class="textbox" name="genre" OnChange="if(this.value) { this.form.new_genre.value = ''} else { this.select(); }">
		     <option value="select">-- Select</option>
		     <option value="none">-- Create</option>
<?
include($myNewsConf['path']['sys']['admin'] . '/include/genrelist.inc');
?>
		</select>
		</td>
		<td>
		<input class="textbox" type="text" name="new_genre" size="30" value="-New SubCategory-" maxlength="20">
		<br />
		<small><b>Note:</b> <i>You only need to complete the above field if you are creating a new sub-category<br>(Main category must be selected)</i></small>
		</td>
		</tr>
		</table>
		</P>

		<b>3)</b> Title:
			<br />
		<input class="textbox" type="text" name="title" size="40">
			<br />
			<P>
		<b>4)</b> Link URL:
			<br />
		<TEXTAREA class="textbox" NAME="url" COLS=50 ROWS=2 WRAP="virtual"></TEXTAREA>	
			<br />
			<br />
		<b>5)</b> Associated E-mail:
			<br />
		<input class="textbox" type="text" name="email" size="52">
			<br />
			<br />
		<b>6)</b> Description:
			<br />
		<TEXTAREA class="textbox" NAME="description" COLS=50 ROWS=10 WRAP="virtual"></TEXTAREA>
			<br />
			<br />
		<input type="hidden" name="author" value="<?=$_SESSION['valid_user']?>">
		<?=$submit?>
   </form>
		<P>
		<small><i>Today is: <?=$now?></i></small>
</blockquote>

<?

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
