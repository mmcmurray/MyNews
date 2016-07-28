<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar'] . "?mode=delete_list&month=" . $_GET['month'] . "&year=" . $_GET['year']?>">
<html>
	<HEAD>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Deleted : " . stripslashes($_GET['title']); ?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
<p>
<?php

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
	print "<p><b>You do not have permission to view this resource!</b>"; 
} else {

	if( isset($_GET['recurring']) ){
        $squery = mysql_query('SELECT msg_id FROM ' . $myNewsConf['db']['table']['calendar'] . ' WHERE msg_recurring = "' . $_GET['recurring'] . '"');
        WHILE ($row = mysql_fetch_assoc($squery)){
            $artnr  = $row['msg_id'];

            $dquery = 'DELETE FROM ' . $myNewsConf['db']['table']['comments'] . ' WHERE artnr = ' . $artnr . ' AND type = "cal"';
            $delete = mysql_query($dquery);
        }

        $query  = "DELETE FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_recurring = '" . $_GET['recurring'] . "'";
        $msg    = 'recurring';
	} else {
		$query  = "DELETE FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_id = " . $_GET['id'];
		$msg    = '';

        $dquery = 'DELETE FROM ' . $myNewsConf['db']['table']['comments'] . ' WHERE artnr = ' . $_GET['id'] . ' AND type = "cal"';
        $delete = mysql_query($dquery);
	}

	$result = mysql_query($query);
	if( !$result ){
		echo mysql_error() . ": " . mysql_errno();
	}
?>
<br />
<br />
The <?php echo $msg?> event <b><?php echo $_GET['title']?></b> has been removed.
<?
} // End Login Check If()
?>
	</body>
</html>
