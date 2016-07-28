<?
$TITLE =				$myNewsConf['default']['sitename'] . " Admin : Album : Select Tool";
$baseAdmin_URI =		$myNewsConf['path']['web']['admin'];
$baseAlbumAdmin_URI = 	$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['album'];

include($myNewsConf['head']);
section_header('Admin','100%','content');
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

print <<<EOT
	<p><b>What would you like to do?</b><br>
<A HREF="$baseAdmin_URI">Go back to <i>Main</i> admin</a>
	 <ul>
	  <li><a href="$baseAlbumAdmin_URI?mode=album_add">Add Photo to Album</a>
	  <li><a href="$baseAlbumAdmin_URI?mode=album_select">Select Album to Modify</a>
	 </ul>
EOT;

section_footer('Admin','content');
include($myNewsConf['foot']);
?>
