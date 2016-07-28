<?php
$TITLE              = $myNewsConf['default']['sitename'] . " Admin : Calendar : Modify : " . $_GET['title'];
$baseCalAdmin_URI   = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];
$submit             = $myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

$query = "SELECT * FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_id = " . $_GET['id'];
$result = mysql_query($query);
/*
 * Check and Make sure the query doesn't fail
 */
if( !$result ){
  echo mysql_error() . ": " . mysql_errno();
}

/*
 * Dump return results from SQL into a hash
 */
$event = mysql_fetch_assoc($result);
$event["msg_text"] = str_replace("<br>&nbsp;<br>\n" , "\n\n" , $event["msg_text"]);

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['valid_user'] != $event['msg_poster_id']){
    print "<p><b>You do not have permission to view this resource!</b>"; 

} else {

	/*
	 * Build our splashtext with data returned from the event hash
	 */
	$splashtext = "Modify \"" . $event["msg_title"] . "\" for " . $event["msg_month"] . "/" . $event["msg_day"] . "/" . $event["msg_year"];
	if( $event["msg_recurring"]){ $splashtext .= " (and associated recurring events)"; }
?>
<p>
<b><?php echo $splashtext; ?></b><br>
<A HREF="<?php echo "$baseCalAdmin_URI"; ?>">Go back to admin</a>
<blockquote>
<form method="post" action="<?php echo "$baseCalAdmin_URI?mode=edited"; ?>" name="the_form">
          <input type="hidden" name="event_title" value="<?php echo $event["msg_title"]; ?>">
  <table cellspacing="0" cellpadding="1" border="0">
    <tr>
      <td colspan="3" valign="top">
	<b>Note:</b>  If wish to change the Venue/City/State, it is
	best to Delete the current event, and create a new one.
      </td>
    </tr>
    <tr> 
      <td colspan="3" valign="top">
	&nbsp;
          <input type="hidden" name="event_title" value="<?php echo $event["msg_title"]; ?>">
          <input type="hidden" name="event_where" value="<?php echo $event["msg_where"]; ?>">
          <input type="hidden" name="event_city" value="<?php echo $event["msg_city"]; ?>">
          <input type="hidden" name="event_state" value="<?php echo $event["msg_state"]; ?>">
      </td>
    </tr>
    <tr> 
      <td valign="top" nowrap> 
        <p><b>1)</b> Who?
      </td>
	<td>
	&nbsp;
	</td>
      <td valign="top"> 
        <p> 
          <input class=textbox type="text" size="25" name="event_who" value="<?php echo $event["msg_who"]; ?>" maxlength="20">
      </td>
    <tr>

    </tr>
      <td valign="top" nowrap> 
        <p><b>2)</b> Info:
      </td>
	<td>
	&nbsp;
	</td>
      <td valign="top"> 
        <p>
         <textarea class=textbox name="event_text" rows="15" cols="60" wrap="VIRTUAL"><?php echo $event["msg_text"]; ?></textarea>
      </td>
    </tr>
    <tr> 
      <td valign="top" nowrap> 
        <p><b>3)</b> Active?
      </td>
	<td>
	&nbsp;
	</td>
      <td valign="top"> 
        <p> 
          <input type="radio" name="active" value="1"<?php if($event["msg_active"]) echo " checked"; ?>>
          yes<br>
          <input type="radio" name="active" value="0"<?php if (!$event["msg_active"]) echo " checked"; ?>>
          no
          <?php echo "<input type=\"hidden\" name=\"id\" value=\"" . $event["msg_id"] . "\">\n"; ?>
          <?php echo "<input type=\"hidden\" name=\"month\" value=\"" . $event["msg_month"] . "\">\n"; ?>
          <?php echo "<input type=\"hidden\" name=\"year\" value=\"" . $event["msg_year"] . "\">\n"; ?>
          <?php if($_GET['recurring']) echo "<input type=\"hidden\" name=\"recurring\" value=\"" . $event["msg_recurring"] . "\">\n"; ?>
      </td>
    </tr>
    <tr> 
      <td valign="top">
&nbsp;	
      </td>
	<td>
	&nbsp;
	</td>
      <td  valign="top">
	<?="$submit";?>
      </td>
    </tr>
  </table>
  </form>
</blockquote>

<?
} // End login Check If()
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
