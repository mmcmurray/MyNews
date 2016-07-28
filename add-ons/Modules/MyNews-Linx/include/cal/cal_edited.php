<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'] . "?mode=edit_list&year=" . $_POST['year'] . "&month=" . $_POST['month']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Calendar : Modified : " . $_POST['event_who']?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
<?php

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
    print "<p><b>You do not have permission to view this resource!</b>"; 

} else {

	/*
	 * convert two carriage returns to html break.
	 */

	if($_POST['recurring']){

		$query = "UPDATE " . $myNewsConf['db']['table']['calendar'] . " 
		SET
			msg_who =		'" . addslashes($_POST['event_who'])	. "',
			msg_text =		'" . addslashes($_POST['event_text'])	. "',
			msg_active =	'" . addslashes($_POST['active'])		. "'
		WHERE
			msg_recurring = '" . $_POST['recurring'] . "'";

		$result = mysql_query($query) or die(mysql_error());
		if( !$result ){
			echo mysql_error() . ": " . mysql_errno();
		}
	} else {
		$query = "UPDATE " . $myNewsConf['db']['table']['calendar'] . " 
		SET
			msg_who =		'" . addslashes($_POST['event_who'])	. "',
			msg_text =		'" . addslashes($_POST['event_text'])	. "',
			msg_active =	'" . addslashes($_POST['active'])		. "'
		WHERE
			msg_id='" . $_POST['id'] . "'";

		$result = mysql_query($query) or die(mysql_error());
		if( !$result ){
			echo mysql_error() . ": " . mysql_errno();
		}
	}
} // End Login Check If()
?>
		<br />
		<br />
		The event <b><?php echo $_POST['event_who']?></b> has been updated.
		<br />
		The text for this event is:<br>
		<blockquote>
			<p>
			<?php echo nl2br($_POST['event_text'])?>
		</blockquote>
	</body>
</html>
