<?
$news_query = mysql_query("select * from " . $myNewsConf['db']['table']['news'] . " where artnr=" . $_GET['sid']) or die(mysql_error());
$row        = mysql_fetch_assoc($news_query);
    $title          = fixWordShit($row['title']);
    $author         = $row['author'];
    $previewtext    = fixWordShit($row['previewtext']);
    $ednote         = $row['ednote'];
    $email          = $row['email'];
    $date           = $row['date'];
    $section        = $row['section'];
    $active         = $row['active'];
    $viewcount      = $row['viewcount'];

$baseNewsAdmin_URI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news'];

$TITLE  = $myNewsConf['default']['sitename'] . " Admin : Edit : $title";
$now    = date($myNewsConf['format']['date']['nice']);
$submit = $myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] !='Admin' && $_SESSION['status'] !='Editor' && $author != $_SESSION['valid_user']){
    print ("<p><b>You do not have permission to edit this article</b>");
} else {
?>

        <p>
        <b>Modify an Article</b><br />
        <A HREF="<?=$baseNewsAdmin_URI?>">Go back to admin</a>
        <br />
<blockquote>
   <form action="<?=$baseNewsAdmin_URI?>?mode=news_edited&sid=<?=$_GET['sid']?>" method="post">
        <p>
<table border="0" width="85%">
    <tr>
        <td align="left" valign="top">
            <b>1)</b> Section:
                    <br />
            <SELECT class="textbox" NAME="section">
                <OPTION><?=$section?></OPTION>

<?
    include($myNewsConf['path']['sys']['admin'] . "/include/seclist.inc");
?>
            </SELECT>
        </td>
        <td align="left" valign="top">
            <b>2)</b> Active?
                    <br />
                    &nbsp;&nbsp;&nbsp;
            <input type="radio" name="active" value="0" <?php if($active == 0) echo "checked"; ?>>yes
                    <br />
                    &nbsp;&nbsp;&nbsp;
            <input type="radio" name="active" value="1" <?php if($active == 1) echo "checked"; ?>>no
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left" valign="top">
            <b>3)</b> Title:
                    <br />
            <input class="textbox" type="text" name="title" size="40" value="<?=$title?>">
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left" valign="top">
            <b>4)</b> Item Text:
                    <br />
            <TEXTAREA class="textbox" NAME="previewtext" COLS=50 ROWS=20 WRAP="VIRTUAL"><?=$previewtext?></TEXTAREA>
        </td>
    </tr>

<?
    if ($_SESSION['status'] == 'Editor' || $_SESSION['status'] == 'Admin'){
?>

    <tr>
        <td colspan="2" align="left" valign="top">
            <b>5)</b> Editors Notes: <i>(Appear at top of story)</i>
                    <br />
            <TEXTAREA class="textbox" NAME="ednote" COLS=50 ROWS=5 WRAP="VIRTUAL"><?=$ednote?></TEXTAREA>
        </td>
    </tr>

<?
    } else {
?>

    <input type="hidden" name="ednote" value="OLDEDNOTE">

<?
    }
?>

</table>
    <input type="hidden" name="author" value="<?=$author?>">
    <input type="hidden" name="email" value="<?=$email?>">
    <input type="hidden" name="date" value="<?=$date?>">
    <input type="hidden" name="viewcount" value="<?=$viewcount?>">
    <br />
    <br />
        <?=$submit?>
</form>
        <p>
        <small><i>Today is: <?=$now?></i></small>
</blockquote>

<?
} //End if()

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
