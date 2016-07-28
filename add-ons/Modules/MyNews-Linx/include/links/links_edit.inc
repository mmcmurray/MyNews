<?
$links_query = mysql_query("select * from " . $myNewsConf['db']['table']['portal'] . " where linkid=" . $_GET['sid']) or die(mysql_error());
$row        = mysql_fetch_assoc($links_query);
    $title          = fixWordShit($row['linktitle']);
    $author         = $row['postby'];
    $description    = fixWordShit($row['linkdescrip']);
    $category       = $row['category'];
    $email          = $row['linkemail'];
    $url            = $row['linkhref'];
    $genre          = $row['genre'];
    $genre2         = $row['genre2'];
    $rating         = $row['rating'];

$baseLinksAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['links'];

$TITLE  = $myNewsConf['default']['sitename'] . " Admin : Edit : $title";
$submit = $myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] !='Admin' && $_SESSION['status'] !='Editor' && $author != $_SESSION['valid_user']){
    print ("<p><b>You do not have permission to edit this article</b>");
} else {
?>

        <p>
        <b>Modify a Link</b><br />
        <A HREF="<?=$baseLinksAdmin_URI?>">Go back to links admin</a>
        <br />
<blockquote>
   <form action="<?=$baseLinksAdmin_URI?>?mode=links_edited&sid=<?=$_GET['sid']?>" method="post">
        <p>
            <b>1)</b> Category:
                    <br />
	    <table border=0>
	    <tr>
	    <td valign=top>
            <SELECT class="textbox" NAME="category" OnChange="if(this.value) { this.form.new_category.value = ''} else { this.select(); }">
                <OPTION><?=$category?></OPTION>
		<OPTION value="none">-- Create</OPTION>
<?
    include($myNewsConf['path']['sys']['admin'] . "/include/categorylist.inc");
?>
            </SELECT>
	    </td>
	    <td>
	       <input class="textbox" type="text" name="new_category" size="30" value="-New Category-" maxlength="20">
		       <br />
	       <small><b>Note:</b> <i>You only need to complete the above field if you are creating a new category</i></small> 
	    </td>
	    </tr>
	    </table>
	</p>

	<p>
	    <b>2)</b> Sub-Category:
		<br>
	    <table border=0>
	    <tr>
	    <td valign=top>
	    <SELECT class="textbox" NAME="subcategory" OnChange="if(this.value) { this.form.new_subcategory.value = ''} else { this.select(); }">>
		 <OPTION><?=$genre?></OPTION>
		 <OPTION value="none">-- Create</OPTION>
<?
    include($myNewsConf['path']['sys']['admin'] . "/include/genrelist.inc");
?>
	    </SELECT>
	    </td>
	    <td>
		<input class="textbox" type="text" name="new_subcategory" size="30" value="-New SubCategory-" maxlength="20">
			<br>
		<small><b>Note:</b> <i>You only need to complete the above field if you are creating a new sub-category</i></small>
	    </td>
	    </tr>
	    </table>
	    </p>

	<p>
	    <b>3)</b> Title:
                    <br />
            <input class="textbox" type="text" name="title" size="40" value="<?=$title?>">
	</p>

	<p>
	    <b>4)</b> Link URL:
		<br>
	    <TEXTAREA class="textbox" NAME="url" COLS=50 ROWS=2 WRAP="VIRTUAL"><?=$url?></TEXTAREA>
	</p>

	<p>
	    <b>5)</b> Associated E-mail:
		<br>
	    <input class="textbox" type="text" name="email" size="52" value="<?=$email?>">
	</p>

	<p>
	    <b>6)</b> Description:
                    <br />
            <TEXTAREA class="textbox" NAME="description" COLS=50 ROWS=10 WRAP="VIRTUAL"><?=$description?></TEXTAREA>
	</p>


    <input type="hidden" name="author" value="<?=$author?>">
    <br />
        <?=$submit?>
</form>
        <p>
</blockquote>

<?
} //End if()

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
