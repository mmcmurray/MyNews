<?
$TITLE =			$myNewsConf['default']['sitename'] . " Admin : Calendar : Modify : Select Month";
$baseCalAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'];
$submit =			$myNewsConf['button']['submit'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");
?>

<p><b>Edit an Event</b><br>
<A HREF="<?php echo "$baseCalAdmin_URI"; ?>">Go back to admin</a>
<blockquote>
<form method="post" action="<?php echo "$baseCalAdmin_URI?mode=edit_list"; ?>" name="the_form">
  <table cellspacing="0" cellpadding="0" border="0">
    <tr> 
      <td width="200" valign="top"> 
        <p>Use this interface to <b>modify</b>
         an event by picking a month and year in which your event occurs. If it's a recurring
         event which spans several months, just pick any of the appropriate months.</p>
      </td>
      <td width="20" valign="top"></td>
      <td width="285" valign="top"> 
        <p>I would like to <b>modify</b> an event during:</p>
        <p> 
		<select name="month">
<?
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
?>
		</select>
		<select name="year">
			<option value="<? print date('Y') -1; ?>"><? print date('Y') -1; ?></option>        
			<option value="<? print date('Y'); ?>" selected><? print date('Y'); ?></option>     
			<option value="<? print date('Y') +1; ?>"><? print date('Y') +1; ?></option>        
			<option value="<? print date('Y') +2; ?>"><? print date('Y') +2; ?></option>        
		</select>
		<br />
		<br />
		<?=$submit?>
        </p>
      </td>
    </tr>
  </table>
</form>
</blockquote>

<?
section_footer('Admin','content');
include($myNewsConf['foot']);
?>
