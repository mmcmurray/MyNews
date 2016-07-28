<?
$TITLE = $myNewsConf['default']['sitename'] . " Admin : Portal : Add New Item";
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

$baseNewsAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['portal'];
$now =					date($myNewsConf['format']['date']['nice']);
$submit =				$myNewsConf['button']['submit'];
?>
		<p>
		<b>Add a new Link.</b>
		  <br />
		<A HREF="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
<blockquote>
   <form action="<?=$baseNewsAdmin_URI?>?mode=news_added" method="post" name="the_form" onSubmit="return isOK()">
		<P>
		<b>1)</b> Choose/Create Section:
		  <br />
	   <select class="textbox" name="section" OnChange="if(this.value) { this.form.new_section.value = ''} else { this.select(); }">
		   <option value="select">-- Select</option>
		   <option value="none">-- Create</option>

<?
include($myNewsConf['path']['sys']['admin'] . '/include/seclist.inc');
?>

	   </select>
			<br />
			<br />
		<input class="textbox" type="text" name="new_section" size="30" value="-New Section-" maxlength="13">
			<br />
		<small><b>Note:</b> <i>You only need to complete the above field if you are creating a new section</i></small>
			<br />
			<P>
		<b>2)</b> Title:
			<br />
		<input class="textbox" type="text" name="title" size="30">
			<br />
			<P>
		<b>3)</b> Text:
			<br />
		<TEXTAREA class="textbox" NAME="previewtext" COLS=50 ROWS=20 WRAP="virtual"></TEXTAREA>
			<br />
			<br />
		<input type="hidden" name="author" value="<?=$_SESSION['valid_user']?>">
		<input type="hidden" name="email" value=''>
		<?=$submit?>
   </form>
		<P>
		<small><i>Today is: <?=$now?></i></small>
</blockquote>

<?

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
