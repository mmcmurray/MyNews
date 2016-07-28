<?php
$TITLE = $myNewsConf['default']['sitename'] . " Admin : Calendar : Add New Event";
$baseCalAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];
$submit = 			$myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<script language="JavaScript">
<!--

function isOK(){

  with(document.the_form){
    if( recurring[1].checked && recurring_duration.value == "" ){
      alert("Please enter the duration for recurrence.");
      return false;
    }

    if( event_title.value == "none" && new_title.value == "" ){
      alert("Please enter a title for this event.");
      return false;
    }

    if( event_who.value == "" ){
      alert("Please fill in the \"who\" field.");
      return false;
    }
  }

  return true;
}

//-->
</script>

<p><b>Add a New Event</b><br>
<A HREF="<?=$baseCalAdmin_URI?>">Go back to admin</a>
<blockquote>
<form method="post" action="<?=$baseCalAdmin_URI?>?mode=posted" name="the_form" onSubmit="return isOK()">
  <table cellspacing="0" cellpadding="0" border="0" width=85%>
    <tr> 
      <td height="135" valign="top"> 
        <p><b>1)</b>
	  I would like to post an event on:
	</p>
        <p> 

<?
print "\t\t<select class=textbox name=\"month\">\n";

$month = "1";
while ($month < 13) {
  $monname = date( "M", mktime(0,0,0,$month,1,2000) );
  // select current date
  if( $monname == date("M") )
     print "\t\t\t<option value=\"$month\" selected>$monname</option>\n";
  else
     print "\t\t\t<option value=\"$month\">$monname</option>\n";

  $month++;
}

print "\t\t</select>\n\n";

// create day droplist
print "\t\t<select class=textbox name=\"day\">\n";

$day = "1";
while ($day < 32) {
   // select current date
   if( $day == date("d") )
      print "\t\t\t<option value=\"$day\" selected>$day</option>\n";
   else
      print "\t\t\t<option value=\"$day\">$day</option>\n";

   $day++;
};

print "\t\t</select>\n\n";
?>
		<select class=textbox name="year">
			<option value="<? print date('Y') -1; ?>"><? print date('Y') -1; ?></option>
			<option value="<? print date('Y'); ?>" selected><? print date('Y'); ?></option>
			<option value="<? print date('Y') +1; ?>"><? print date('Y') +1; ?></option>
			<option value="<? print date('Y') +2; ?>"><? print date('Y') +2; ?></option>

		</select>
        </p>
      </td>
      <td width="10" height="135" valign="top"></td>
      <td width="300" height="135" valign="top"> 
        <p><b>2)</b>
	  Select the type of event. This will be what
	  people see on the calendar.
	</p>
        <p> 
            <select class=textbox name=event_title>
            <option value="none">Create new type
<?
		$type_query = mysql_query("select distinct(msg_title) as msgTitle from " . $myNewsConf['db']['table']['calendar'] . " order by msg_title");
		while($row = mysql_fetch_assoc($type_query)) {
			echo "<option>" . stripslashes($row['msgTitle']) . "\n";
		}
		mysql_free_result($type_query);

?>
            </select> 

	  <br />
	  <br />
	  <b>(note):</b> Only fill in the following box if you are
	   creating a new event type.
	  <br />
          <input class=textbox type="text" name="new_title" maxlength="12">
        </p>
      </td>
    </tr>
    <tr> 
      <td height="65" valign="top"> 

        <p><b>3a)</b> 
	  Title: <i title="Who?/Name?/etc..">(required)</i>
        </p>
        <p> 
          <input class="textbox" type="text" name="event_who" maxlength="20">
      </td>
      <td width="10" height="65" valign="top"></td>
      <td height="65" valign="top"> 

        <p><b>3b)</b> 
	  Where: <i title="Venue/Destination/etc..">(optional)</i>
        </p>
        <p> 
          <input class="textbox" type="text" name="event_where" maxlength="20">
      </td>

    </tr>
    <tr> 
      <td height="65" valign="top"> 

        <p><b>4a)</b> 
	  City: <i>(optional)</i>
        </p>
        <p> 
          <input class="textbox" type="text" name="event_city" maxlength="20">
      </td>
      <td width="10" height="65" valign="top"></td>
      <td height="65" valign="top"> 

        <p><b>4b)</b> 
	  State: <i>(optional)</i>
        </p>
        <p> 
		  <input class="textbox" type="text" name="event_state" maxlength="2" size="3">
      </td>

    </tr>

    <tr> 
      <td height="230" valign="top"> 

        <p><b>5a)</b>
	  Is this going to be a recurring event?
	</p>
        <p> 
          <input type="radio" name="recurring" value="0" checked>
          no<br>
          <input type="radio" name="recurring" value="1">
          yes</p>
      </td>
      <td width="10" height="230" valign="top"></td>
      <td width="300" height="230" valign="top"> 

        <p><b>5b)</b>
	  If it is recurring, specify daily, weekly, bi-weekly, or monthly.
	</p>
        <p> 
          <input type="radio" name="recurring_type" value="weekly" checked>
          weekly<br>
          <input type="radio" name="recurring_type" value="bi-weekly">
          bi-weekly<br>
          <input type="radio" name="recurring_type" value="monthly">
          monthly<br>
          <input type="radio" name="recurring_type" value="daily">
          daily<br>
        <p>
	  If weekly or bi-weekly, specify the number of weeks for it
	  to keep recurring. If monthly, specify the number of months.
	  (If you choose "bi-weekly" and put in an odd number of weeks,
	  the computer will round down.)
	</p>
        <p> 
          <input class=textbox type="text" name="recurring_duration" size="2" maxlength="2">
          duration</p>

      </td>
    </tr>
    <tr> 
    
      <td colspan=3 height="180" valign="top"> 
        <p><b>6)</b>
	   Enter the copy for the event. This will be what people see
	   when they click on an event link.
	</p>
        <p> 
          <textarea class=textbox name="event_text" rows="10" cols="50" wrap="VIRTUAL"></textarea>
        </p>
      </td>
    </tr>
    <tr>
      <td colspan=3 width="200" height="60" valign="top">
<input type="hidden" name="admin_id" value="<?=$_SESSION['valid_user']?>">
	<br />
	<?=$submit?>
</td>
    </tr>
  </table>

  </form>
</blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>

