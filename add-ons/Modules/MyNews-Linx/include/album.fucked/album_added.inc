<?
$baseAlbumAdmin_URI =	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];
?>

<meta http-equiv="Refresh" content="4; URL=<?php echo "$baseAlbumAdmin_URI"; ?>">
<html>
	<head>
	<title><?=$myNewsConf['default']['sitename'] . " Admin : Album : Added"?></title>
<?php 
include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); 

?>
	</head>
	<body>
<?

if(!filetype($myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'])) {
	if (!@mkdir($myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'],0777)) {
		print ('
				<p>
				<b>Error!</b>
				<br/>
				Unable to create ' . $myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . ' - check permissions
				<br/>
			</body>
		</html>
		');
		exit;
	}
}

if (!filetype($myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . '/album')){
	if (!@mkdir($myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . '/album',0777)){
		print ('
			<p>
			<b>Error!</b>
			<br />
			Unable to create ' . $myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . '/album/ - check permissions
			<br />
			</body>
		</html>
		');
		exit;
	}
} 

if ($_FILES['file']['name'] == ''){
	print ("
			<p>
			<b>Error!</b>
			<br />
			You must select an image file to upload
		</body>
	</html>
	");
	exit;
} else {

    $filetype = get_file_type(get_file_ext($_FILES['file']['name']));
    if ($filetype != 'image'){
        print ('	
                <p>
                <b>Error!</b>
                <br />
                Unsupported Filetype.  Only PNG/JPG/JPEG images.
                <br />
                Your filename was: ' . $_FILES['file']['name'] . '
            </body>
        </html>
        ');
        exit;
    }

	$dest = $myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . '/album/' . $_FILES['file']['name'];

	//if(@exec('cp ' .  $file $dest) != 0) {
	if(!copy($_FILES['file']['tmp_name'],$dest)) 
	{
		echo "Unable to create $dest - check permissions<br>\n";
        	echo '<pre>';
        	print_r($_FILES);
        	echo '</pre>';
		exit;
	}

	unlink($_FILES['file']['tmp_name']);

	if($_POST['album'] == 'none'){
		if(!$_POST['new_album'] || $_POST['new_album'] == '<new album name>'){
			echo "<b>You must specify an album name</b>\n";
			echo "\t</body>\n";
			echo "</html>\n";
			die;
		} else {
			$_POST['album'] = $_POST['new_album'];
		}
	}

	$ret =	mysql_query("insert into " . $myNewsConf['db']['table']['album'] . " values 
		('',
		 '" . $_FILES['file']['name']               . "',
		 '" . addslashes($_POST['author'])		. "',
		 '" . addslashes($_POST['album'])		. "',
		 '" . addslashes($_POST['desc'])		. "',
		 '" . addslashes($_POST['extended'])	. "',
		 '" . date('Y-m-d H:i:s')						. "')");

	if($ret){
		$size = getimagesize($myNewsConf['path']['sys']['album'] . '/' . $_SESSION['valid_user'] . '/album/' . $_FILES['file']['name']);
		chmod($dest, 0666);
		print ("
				<p>
			The photo <i>" . $_POST['desc'] . "</i> has been added to the " . $_POST['album'] . " album.
		");
		
		$page='received';
	} else {
		echo "Unable to insert the photo in the database ($ret)<P>\n";
	}
}
?>
	</body>
</html>

<?
/*
 *
 */
function get_file_ext($file_name) {
	ereg( ".*\.([a-zA-z0-9]{0,5})$", $file_name, $regs );
	$ext = ".$regs[1]";
	return( $ext ); 
}

function get_file_type($f_ext) {
    $image_exts = array ('.jpg', '.png', '.jpeg', '.JPG', '.PNG', '.JPEG');
    $text_exts  = array ();
    $arr_size   = sizeof($image_exts);
    for($gft_count=0;$gft_count<$arr_size;$gft_count++){
        if ($image_exts[$gft_count] == $f_ext) {
            $file_type = 'image';
        }
    }
    $arr_size = sizeof($text_exts);
    for ($gft_count=0;$gft_count<$arr_size;$gft_count++){
        if ($text_exts[$gft_count] == $f_ext) {
            $file_type = 'text'; 
        }
    }
    if (!isset($file_type) || $file_type == '') { $file_type = 'unknown'; }
    return( $file_type );
}
/*
 *
 */
?>
