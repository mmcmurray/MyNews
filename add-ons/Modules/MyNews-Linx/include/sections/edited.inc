<meta http-equiv="Refresh" content="2; url=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['news']?>">
<html>
	<head>
		<title><?php echo $myNewsConf['default']['sitename'] . " Admin : Updated : Sections";?></title>
		<?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
	</head>
	<body>
		<p>
		<br />
		<br />
		<ul>
<?
if ($_SESSION['status'] != 'Admin'){
    print "<p><b>You must be and Admin to View this Resource</b>";
} else {
	if(!$_POST){
		print "\t\t\t<li>You must use the post method for this update\n";
	} else {
		$query =	mysql_query("SELECT secid,section from " . $myNewsConf['db']['table']['sections'] . " ORDER by section") or die(mysql_error());
		while($row = mysql_fetch_assoc($query)){
			$sid =		$row['secid'];
			$section =	$row['section'];

			if ($_POST[$sid] == 'on'){
				$set = 1;
				print "\t\t\t<li><b>" . $section . "</b> (" . $sid . ") is now a frontpage section\n";
			} else {
				$set = 0;
			}

			$insert =	mysql_query("UPDATE " . $myNewsConf['db']['table']['sections'] . " SET front = " . $set . " WHERE secid = " . $sid) or die(mysql_error());
		}
	}
}
?>
		</ul>
	</body>
</html>
