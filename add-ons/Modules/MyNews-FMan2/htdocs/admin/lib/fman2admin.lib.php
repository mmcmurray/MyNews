<?php
/* 
   File Thingie version 1.41 - Andreas Haugstrup Pedersen <andreas@solitude.dk> October 1st, 2003
   The newest version of File Thingie can be found at <http://www.solitude.dk/filethingie/>
   Comments, suggestions etc. are welcome and encouraged at the above e-mail.
   
   LICENSE INFORMATION:
   This work is licensed under the Creative Commons Attribution-NoDerivs-NonCommercial.
   To view a copy of this license, visit <http://creativecommons.org/licenses/by-nd-nc/1.0/>
   If you want to use File Thingie for a commercial work please contact me at <andreas@solitude.dk>
   
   KNOWN ISSUES IN THIS VERSION:
   - You cannot rename file which has a single prime (') in the name.

   Changelog for version 1.41:
   - Option for allowing all file types for upload added.
   - Fixed two typos that made some error messages not display.
   - Fixed rare bug where users could delete files they weren't supposed to.

*/
/*******************************************************************/
function getExt ($name) {
	// This function returns the file ending without the "."
	if (strstr($name, '.')) {
		$ext = str_replace('.', '', strrchr($name, '.'));
	} else {
		$ext = '';
	}
	return $ext;
}
/*******************************************************************/
function checkFileType ($type, $ext) {
	// This function checks whether the type and file ending is in the list of allowed files.
    global $myNewsModule;
	global $allowedfile, $allowedAlternate;
    $disableMimeCheck   = $myNewsModule['fman2']['disableMimeCheck'];
    $allowAllFiles      = $myNewsModule['fman2']['allowAllFiles'];

	if ($allowAllFiles == TRUE) {
		return TRUE;
	} else {
		$ext = strtolower($ext);
		if ($disableMimeCheck == FALSE) {
			foreach ($allowedfile as $currentext => $currenttype) {
				if ($ext == strtolower($currentext) && $type == strtolower($currenttype)) {
					return TRUE;
					break;
				}
			}
			foreach ($allowedAlternate as $currentext => $currenttype) {
				if ($ext == strtolower($currentext) && $type == strtolower($currenttype)) {
					return TRUE;
					break;
				}
			}
		} else {
			if (array_key_exists($ext, $allowedfile) || array_key_exists($ext, $allowedAlternate)) {
				return TRUE;
			}
		}
	}
}
/*******************************************************************/
function outputAcceptedFiles($allowedfile) {
	// This function returns a comma-seperated list of allowed file types for use in the HTML form.
	$allowedfile = array_unique($allowedfile);
	foreach ($allowedfile as $mimetype) {
		$formaccept = "{$formaccept}, {$mimetype}";
	}
	$formaccept = substr($formaccept, 2);
return $formaccept;
}
/*******************************************************************/
function buildMenu($uplink, $reloadlink, $helplink) {
	// This functions outputs the menu.
	global $msg;
	if (IsSet($_GET['subdir'])) {
        if (strstr($_GET['subdir'], '/')) {
            $uplink = substr($_GET['subdir'], 0, strrpos($_GET['subdir'], '/'));
            $uplink = '?repos=' . $_GET['repos'] . '&amp;action=list&amp;subdir=' . $uplink;
        } else {
            $uplink = '?repos=' . $_GET['repos'] . '&amp;action=list';
        }
		$uplink = "\n" . '&middot; <a href="' . $_SERVER['PHP_SELF'] . $uplink . '">' . printMsg('menuUp') . '</a>';
	} else {
		$uplink = '';
	}
    $output.= "\n" . '<center>';
    $output.= "\n" . '<a href="' . $_SERVER['PHP_SELF'] . '?repos=' . $_GET['repos'] . '">' . printMsg('menuHome') . '</a>';
    $output.= "\n" . '&middot;';
    $output.= "\n" . '<a href="' . $_SERVER['PHP_SELF'] . $reloadlink . '">' . printMsg('menuReload') . '</a>';
    $output.= "\n" . $uplink;
    $output.= "\n" . '</center>';
return $output;
}
/*******************************************************************/
function renameFile ($name, $dir) {
	// This function handles renaming of files.
    global $myNewsModule;
    global $allowedfile, $msg;

    $allowAllFiles  = $myNewsModule['fman2']['allowAllFiles'];
	$oldfile = stripslashes($_POST['oldfile']);
	$newfile = stripslashes($_POST['newfile']);

    if (array_key_exists(getExt($name), $allowedfile) || is_dir($dir . '/' . $oldfile) || $allowAllFiles == TRUE) {
        if (is_writeable($dir . '/' . $oldfile)) {
            if (@rename($dir . '/' . $oldfile, $dir . '/' . $newfile)) {
                $r['note'] .= '<blockquote>' . printMsg('textRen', $oldfile, $newfile) . '</blockquote>';
            } else {
                $r['error'].= '<blockquote>' . printMsg('errNoRen0', $oldfile) . '</blockquote>';
            }
        } else {
            $r['error'].= '<blockquote>' . printMsg('errNoRen1', $oldfile) . '</blockquote>'; 
        }
    } else {
        $r['error'].= '<blockquote>' . printMsg('errRenNo2', $oldfile, getExt($newfile)) . '</blockquote>';
    }
return $r;
}
/*******************************************************************/
function printCssValue ($value, $isColour = TRUE) {
	// This function outputs values used in the stylesheet.
	$value = rtrim($value, ";");
	if ($isColour == TRUE && $value[0] != "#") {
		$value = "#{$value}";
	}
	echo $value;
}
/*******************************************************************/
function printMsg ($msgType) {
	// This function prints a message.
	global $msg;
    $currentLang    = 'en';
	if (IsSet($msg[$currentLang][$msgType])) {
		$currentmsg = $msg[$currentLang][$msgType];
	} else {
		$currentmsg = "Message \"{$msgType}\" not found.";
	}
	if (func_num_args() != 1) {
		for ($i=1;$i<func_num_args();$i++) {
			$replace = func_get_arg($i);
			$currentmsg = str_replace ("%VAR{$i}%", $replace, $currentmsg);
		}
	}
	return $currentmsg;
}
/*******************************************************************/
function fileAdmin(){
global $msg, $myNewsModule, $allowedfile;

    if(!$_GET['repos']) $_GET['repos'] = 'shared';
    switch($_GET['repos']){
        case 'shared':
            $repos  = 'repos=shared';
            $dir    = $myNewsModule['fman2']['dir']['shared'];
            $wdir   = $myNewsModule['fman2']['wdir']['shared'];
            $xtra1  = 'style="font-weight: bold; text-decoration: underline;"';
            $xtra2  = '';
            break;
        case 'personal':
            $repos  = 'repos=personal';
            $dir    = $myNewsModule['fman2']['dir']['personal'];
            $wdir   = $myNewsModule['fman2']['wdir']['personal'];
            $xtra1  = '';
            $xtra2  = 'style="font-weight: bold; text-decoration: underline;"';
            break;
            break;
    }

    // We make sure the user can't delete/rename files outside File Thingie.
    if (IsSet($_GET['file'])) {
        if (strstr($_GET['file'], '..')) {
            $_GET['file'] = str_replace('..', '', $_GET['file']);
        }
    }
    if (IsSet($_POST["action"])) {
        $action = $_POST["action"];
    } else {
        $action = $_GET["action"];
    }
    if (IsSet($_GET["sort"])) {
        $_SESSION["sort"] = $_GET["sort"];
    }

    if (IsSet($_GET['subdir'])) {
    // If we are in a subdirectory the action value for forms are changed and the link to move one directory up is defined.
        $originaldir= $dir;
        $dir        = $dir . '/' . $_GET['subdir'];
        $wdir       = $wdir . $_GET['subdir'] . '/';
        $formaction = $_SERVER['PHP_SELF'] . '?' . $repos . '&subdir=' . $_GET['subdir'];
        $reloadlink = '?' . $repos . '&amp;action=list&amp;subdir=' . $_GET['subdir'];
        $subdirlink = '&amp;subdir=' . $_GET['subdir'];
    } else {
        $formaction = $_SERVER['PHP_SELF'] . '?' . $repos;
        $uplink     = '';
        $reloadlink = '?' . $repos . '&amp;action=list';
        $subdirlink = '';
    }

    $delConfirm = printMsg('textConfirm');
    $jscript    = <<<HTML
        <script type="text/javascript">
        function skift(obj) {
            if (obj.className=='ikkeklikket') {
                obj.className = 'klikket';
                document.getElementById(obj.id + '.ipt').focus();
            }
            else if (obj.className=='klikket') {
                obj.className = 'ikkeklikket';
            }
        }
        </script>
        <script type="text/javascript">
        function checkDelete() {
            var value = confirm("{$delConfirm}");
            if (value == true) {
                return true;
            } else {
                return false;
            }
        }
        </script>
HTML;

    $style  = <<<HTML
        <style type="text/css">
            .klikket form, .ikkeklikket span {
                display:inline;
            }
            .ikkeklikket form, .klikket span {
                display:none;
            }
            a.renamelink {
                cursor:pointer;
            }
        </style>
HTML;

    $title  = printMsg('title', $wdir);
    $output.= "\n" . '<p />';
    $output.= "\n" . '&nbsp;&nbsp;<b>&raquo; Repository:</b>';
    $output.= "\n" . '&nbsp;';
     
    $output.= "\n" . '<a ' . $xtra1 . ' href="' . $_SERVER['PHP_SELF'] . '?repos=shared">Shared</a>';
    $output.= "\n" . '&nbsp;/&nbsp;';
    $output.= "\n" . '<a ' . $xtra2 . ' href="' . $_SERVER['PHP_SELF'] . '?repos=personal">Personal</a>';
    $output.= "\n" . '<p />';

    // Output the upload and "create directory" form.
    $output.= uploadForm($formaction);

    // Let's go ahead and case the $action var and process as necessary.
    switch($action){
        case 'delete':
            // If we are to delete a file or directory we bring forward the flaming sword.
            $file = stripslashes($dir . '/' . $_GET['file']);
            $nfile= stripslashes($wdir . $_GET['file']);
            if (is_dir($file)) {
                if (!@rmdir($file)) {
                    $error .= '<blockquote>' . printMsg('errDirNotDel', $nfile) . '</blockquote>';
                } else {
                    $notice.= '<blockquote>' . printMsg('textDirDel', $nfile) . '</blockquote>';
                }
            } else {
                if (!@unlink($file)) {
                    $error .= '<blockquote>' . printMsg('errFileNotDel', $nfile) . '</blockquote>';
                } else {
                    $notice.= '<blockquote>' . printMsg('textFileDel', $nfile) . '</blockquote>';
                }
            }
            break;
        case 'upload':
            $tmp_name = $_FILES['localfile']['tmp_name'];
            $name   = stripslashes($dir . '/' . $_FILES['localfile']['name']);
            $nname  = stripslashes($wdir . $_FILES['localfile']['name']);
            $ext    = getExt($name);

            // Create a new instance of the class
            $upload = new uploader;

            // OPTIONAL: set the max filesize of uploadable files in bytes
            $upload->max_filesize($_POST['MAX_FILE_SIZE']);

            // UPLOAD the file
            if ($upload->upload('localfile', '', '')) {
                $success = $upload->save_file($dir . '/', $_POST['omode']);
            }

            if ($success) {
                // Successful upload!
                $notice.= '<blockquote>' . printMsg('textUp', $nname) . '</blockquote>';
            } else {
                // ERROR uploading...
                if($upload->errors) {
                    while(list($key, $var) = each($upload->errors)) {
                        $error .= '<blockquote>' . $var . '</blockquote>';
                    }
                }
            }

            // If we are to upload a file we will do so.
            /*
            $tmp_name = $_FILES['localfile']['tmp_name'];
            $name   = stripslashes($dir . '/' . $_FILES['localfile']['name']);
            $nname  = stripslashes($wdir . $_FILES['localfile']['name']);
            $ext    = getExt($name);
            $type   = $_FILES['localfile']['type'];
            if ($_FILES['localfile']['error'] == 0) {
                if (checkFileType($type, getExt($name)) == TRUE) {
                    if (@move_uploaded_file($tmp_name, $name)) {
                        @chmod($name, 0777);
                        $notice.= '<blockquote>' . printMsg('textUp', $nname) . '</blockquote>';
                    }
                } else {
                        $error .= '<blockquote>' . printMsg('errUp0', $_FILES['localfile']['type'], getExt($name)) . '</blockquote>';
                }
            } else {
                switch($_FILES['localfile']['error']) {
                    case 1:
                        $currenterror = printMsg('errUp1');
                        break;
                    case 2:
                        $currenterror = printMsg('errUp1');
                        break;
                    case 3:
                        $currenterror = printMsg('errUp2');
                        break;
                    case 4:
                        $currenterror = printMsg('errUp3');
                        break;
                    default:
                        $currenterror = printMsg('errUnknown');
                        break;
                }
                $error .= '<blockquote>' . $currenterror . '</blockquote>';
            }
            */
            break;
        case 'mkdir':
            // If we are to create a dictory we will give it our best shot.
            $newdir = str_replace('../', '', stripslashes($_POST['newdir']));
            $nicedir= $wdir . $newdir;
            $newdir = $dir . '/' . $newdir;
            if ($_POST['newtype'] == 'dir') {
                $oldumask = umask(0);
                if (@mkdirs($newdir, 0775)) {
                    $notice.= '<blockquote>' . printMsg('textNewDir', $nicedir) . '</blockquote>';
                } else {
                    $error .= '<blockquote>' . printMsg('errNoDir', $nicedir) . '</blockquote>';
                }
                umask($oldumask);
            } else {
                if (array_key_exists(getExt($newdir), $allowedfile)) {
                    if (@touch($newdir)) {
                        @chmod($newdir, 0777);
                        $notice.= '<blockquote>' . printMsg('textNewFile', $newdir) . '</blockquote>';
                    } else {
                        $error .= '<blockquote>' . printMsg('errNoFile0', $newdir) . '</blockquote>';
                    }
                } else {
                    $error .= '<blockquote>' . printMsg('errNoFile1', $newdir, getExt($newdir)).'</blockquote>';
                }
            }
            break;
        case 'rename':
            if (stristr($_POST['newfile'], '/')/* && !is_dir("{$dir}/{$_POST["oldfile"]}")*/) {
                // If the new file name contains a / we try to move the file.
                if (stristr($_POST['newfile'], '../')) {
                    if (IsSet($_GET['subdir'])) {
                        // Okay, check for level.
                        $level  = substr_count($_POST['newfile'], '../');
                        if ($level <= substr_count($dir, '/')) {
                            $name   = $dir . '/' . $_POST['newfile'];
                            $rHash  = renameFile ($name, $dir);
                        } else {
                            $error .= '<blockquote>' . printMsg('errNoMove') . '</blockquote>';
                        }
                    } else {
                        $error.= '<blockquote>' . printMsg('errNoMove') . '</blockquote>';
                    }
                } else {
                    $name   = $dir . '/' . $_POST['newfile'];
                    $rHash  = renameFile ($name, $dir);
                }
            } else {
            // Else we rename the file in question.
                $name   = $dir . '/' . $_POST['newfile'];
                $rHash  = renameFile ($name, $dir);
            }
            $notice.= $rHash['note'];
            $error .= $rHash['error'];
            break;
    }


    $cDir   = showCurrDir($dir,$wdir,$subdirlink,$formaction);
    $notice.= $cDir['notice'];
    if(!$cDir['empty']){
        // Go ahead and output the the directory listing.
        $output.= "\n" . '&nbsp;&nbsp;<b>&raquo; Current Files:</b>';
        $output.= "\n" . '<p />';
        $output.= buildMenu($uplink, $reloadlink, $helplink);
        $output.= $cDir['content'];
    }
    $output.= buildMenu($uplink, $reloadlink, $helplink);
    clearstatcache();

    $returnHash['style']    = $style;
    $returnHash['jscript']  = $jscript;
    $returnHash['title']    = $title;
    $returnHash['content']  = $output;
    $returnHash['error']    = $error;
    $returnHash['notice']   = $notice;

return $returnHash;
}
/*******************************************************************/
function showCurrDir($dir, $wdir,$subdirlink,$formaction){
global $myNewsModule;
    $filelist = array();
    $sizelist = array();
    $datelist = array();
    if ($dirlink = @opendir($dir)) {
        // Creates an array with all file names in current directory.
        while (($file = readdir($dirlink)) !== false) {
            if ($file != "." && $file != "..") {
                $currentFileTime = filemtime("{$dir}/{$file}");
                $currentFileSize = filesize("{$dir}/{$file}");
                if (is_dir("{$dir}/{$file}")) {
                    $subdirs[] = $file;
                    $subdirsdatelist[$file] = $currentFileTime;
                    UnSet($currentSubdirSize);
                    if ($sublink = @opendir("{$dir}/{$file}")) {
                        while (($current = readdir($sublink)) !== false) {
                            if ($current != "." && $current != "..") {
                                $currentSubdirSize++;
                            }
                        }
                        closedir($sublink);
                    } else {
                        $currentSubdirSize = "XXX";
                    }
                    $subdirssizelist[$file] = $currentSubdirSize;
                } else {
                    $filelist[] = $file;
                    $datelist[$file] = $currentFileTime;
                    $sizelist[$file] = $currentFileSize;
                }
            }
        }
    closedir($dirlink);
    }
    $filenum = sizeof($filelist)+sizeof($subdirs);
    if (count($filelist) != 0 || is_array($subdirs)) {

        //$output.= printTableHeader();
        $output.= "\n" . '<table class="t1" border="0" width="99%" cellspacing="0">';
        $output.= "\n\t" . '<tr>';
        $output.= "\n\t\t" . '<th class="list">' . printMsg('tableOptions') . ':</th>';
        $output.= "\n\t\t" . '<th class="list">' . printMsg('tableFile') . ':</th>';
        $output.= "\n\t\t" . '<th class="list">' . printMsg('tableSize') . ':</th>';
        $output.= "\n\t\t" . '<th class="list">' . printMsg('tableDate') . ':</th>';
        $output.= "\n\t" . '</tr>';


        if ($_SESSION["sort"] == "date") {
            $filelist = array();
            arsort($datelist);
            foreach ($datelist as $file => $currentFileTime) {
                $filelist[] = $file;
            }
            if (is_array($subdirs)) {
                asort($subdirsdatelist);
                foreach ($subdirsdatelist as $currentSubdir => $currentFileTime) {
                    array_unshift($filelist, $currentSubdir);
                }
            }
        } elseif ($_SESSION["sort"] == "size") {
            $filelist = array();
            asort($sizelist);
            foreach ($sizelist as $file => $currentFileSize) {
                $filelist[] = $file;
            }
            if (is_array($subdirs)) {
                arsort($subdirssizelist);
                foreach ($subdirssizelist as $currentSubdir => $currentFileSize) {
                    array_unshift($filelist, $currentSubdir);
                }
            }
        } else {
            sort($filelist);
            if (is_array($subdirs)) {
                rsort($subdirs);
                for ($i = 0; $i < sizeof($subdirs); $i++) {
                    array_unshift($filelist, $subdirs[$i]);
                }
            }
        }
        $i = 0;
        foreach ($filelist as $file) {
            if (is_dir("{$dir}/{$file}")) {
                if (IsSet($subdirssizelist[$file])) {
                    $size = $subdirssizelist[$file];
                }
                if (!IsSet($size)) {
                    $size = '0';
                }
                $size = printMsg('textFile',$size);
                if (!isset($_GET['subdir'])) {
                    $filelink = '<a href="' . $_SERVER['PHP_SELF'] . '?repos=' . $_GET['repos'] . '&amp;action=list&amp;subdir=' . $file . '" title="' . printMsg('titleListFiles', $file) . '" class="dir">&lt;' . $file . '&gt;</a>';
                } else {
                    $filelink = '<a href="' . $_SERVER['PHP_SELF'] . '?repos=' . $_GET['repos'] . '&amp;action=list&amp;subdir=' . $_GET['subdir'] . '/' . $file . '" title="' . printMsg('titleListFiles', $file) . '" class="dir">&lt;' . $file . '&gt;</a>';
                }
            } else {
                $size = filesize("{$dir}/{$file}");
                $totalsize = $totalsize + $size;
                $size   = mkNiceFsize($size);

                if (is_readable($dir . '/' . $file)) {
                    if(isset($myNewsModule['scripts']['display'])){
                        $filelink = '<a href="' . MODWEB_ROOT . $myNewsModule['scripts']['display'] . '?file=' . $wdir . $file . '" title="' . printMsg('titleOpenFile', $file) . '">' . $file . '</a>';
                    } else {
                        $filelink = '<a href="' . $wdir . $file . '" title="' . printMsg('titleOpenFile', $file) . '">' . $file . '</a>';
                    }
                } else {
                    $filelink = $file;
                }
            }
            if (is_writeable($dir . '/' . $file)) {
                $delete = '(<a onclick="skift(document.getElementById(\'' . $file . '\')); return false;" class="renamelink" title="' . printMsg('titleRen', $file) . '" href="#">' . printMsg('linkRename') . '</a>';
                $delete.= ' | ';
                $delete.= '<a onclick="if(checkDelete() == true) {return true;} else {return false;}" href="' . $_SERVER['PHP_SELF'] . '?repos=' . $_GET['repos'] . '&amp;action=delete&amp;file=' . $file . $subdirlink . '" title="' . printMsg('titleDel', $file) . '">' . printMsg('linkDelete') . '</a>)';
                $rename = '<input type="text" name="newfile" value="' . $file . '" size="'.(strlen($file)+3).'" /><input class="button" type="submit" value="OK" />';
                if (IsSet($_GET['subdir'])) {
                    $rename = '<input type="hidden" name="subdir" value="' . $_GET['subdir'] . '" /> ' . $rename;
                }
            } else {
                $delete = '';
                $rename = '<input type="text" name="newfile" disabled="disabled" />';
                $rename.= '<input type="submit" value="rename" disabled="disabled" />';
            }
            $xtra   = '';
            if ($i % 2 == 0) $xtra = 'class="alt"';
            $i++;
            $output.= "\n\t" . '<tr valign="center" class="ikkeklikket" id="' . $file . '">';
            $output.= "\n\t\t" . '<td ' . $xtra . ' width="15%" nowrap align="center">' . $delete . '</td>';
            $output.= "\n\t\t" . '<td ' . $xtra . '><span>&nbsp;&nbsp;&nbsp;' . $filelink . '</span>';
            $output.= "\n\t\t\t" . '&nbsp;&nbsp;';
            $output.= "\n\t\t\t" . '<form action="' . $formaction . '" method="post" id="' . $file . '.ipt">';
            $output.= "\n\t\t\t\t" . '<input type="hidden" name="action" value="rename" />';
            $output.= "\n\t\t\t\t" . '<input type="hidden" name="oldfile" value="' . $file . '" />';
            $output.= "\n\t\t\t" . $rename;
            $output.= "\n\t\t\t" . '</form>';
            $output.= "\n\t\t" . '</td>';
            $output.= "\n\t\t" . '<td ' . $xtra . ' width="10%" nowrap align="right">' . $size . '&nbsp;&nbsp;&nbsp;</td>';
            $output.= "\n\t\t" . '<td ' . $xtra . ' width="10%" nowrap align="center">' . date($myNewsModule['fman2']['dateformat'], filemtime("{$dir}/{$file}")) . '</td>';
            $output.= "\n\t" . '</tr>';
            Unset($size);
        }
        $output.= "\n\t" . '<tr>';
        $output.= "\n\t\t" . '<td colspan="3" class="bottom small">&nbsp;&nbsp;&nbsp;'.printMsg('tableFoot', $filenum, mkNiceFsize($totalsize)).'</td>';
        $output.= "\n\t" . '</tr>';
        $output.= "\n" . '</table>';
        $empty  = false;
    } else {
        $notice.= '<blockquote>' . printMsg('textDirEmpty') . '</blockquote>';
        $empty  = true;
    }

    $rHash['empty']     = $empty;
    $rHash['notice']    = $notice;
    $rHash['content']   = $output;

return $rHash;
}
/*******************************************************************/
function uploadForm($formaction){
global $myNewsConf, $myNewsModule, $allowedfile;

    $output.= "\n" . '&nbsp;&nbsp;<b>&raquo; Add New:</b>';
    $output.= "\n" . '<blockquote>';
    $output.= "\n" . '<form action="' . $formaction . '" method="post" enctype="multipart/form-data" accept="' . outputAcceptedFiles($allowedfile) . '">';
    $output.= "\n" . '<table border="0" width="75%" cellpadding="0" cellspacing="0">';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td align="left">';
    $output.= "\n\t\t\t" . '<u>File:</u>';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t\t" . '<td>';
    $output.= "\n\t\t\t" . '<input type="hidden" name="MAX_FILE_SIZE" value="' . $myNewsModule['fman2']['maxsize'] . '" />';
    $output.= "\n\t\t\t" . '<input type="file" name="localfile" id="localfile" />';
    $output.= "\n\t\t\t" . '<input type="hidden" name="action" value="upload" />';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t" . '</tr>';

    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td colspan="2" valign="top">&nbsp;</td>';
    $output.= "\n\t" . '</tr>';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t" . '<u>Mode:</u>';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t\t" . '<td valign="top">';
    $output.= "\n\t\t\t\t" . displayHelp('fman2','formmode1',$myNewsConf['button']['help']);
    $output.= "\n\t\t\t\t" . '<input type="radio" name="omode" value="0" checked> <small>Create archive file with incremental extension.</small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . displayHelp('fman2','formmode2',$myNewsConf['button']['help']);
    $output.= "\n\t\t\t\t" . '<input type="radio" name="omode" value="2"> <small>Create new file with incrememtal extension.</small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . displayHelp('fman2','formmode3',$myNewsConf['button']['help']);
    $output.= "\n\t\t\t\t" . '<input type="radio" name="omode" value="1"> <small>Overwrite the file.</small>';
    $output.= "\n\t\t\t\t" . '<br />';
    $output.= "\n\t\t\t\t" . displayHelp('fman2','formmode4',$myNewsConf['button']['help']);
    $output.= "\n\t\t\t\t" . '<input type="radio" name="omode" value="3"> <small>Do nothing.  (highest protection)</small>';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t" . '</tr>';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t" . '<td><input class="button" type="submit" value="Upload" /></td>';
    $output.= "\n\t" . '</tr>';



    $output.= "\n" . '</form>';
    $output.= "\n" . '<form action="' . $formaction . '" method="post">';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td colspan="2">&nbsp;</td>';
    $output.= "\n\t" . '</tr>';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td>';
    $output.= "\n\t\t\t" . '<u>Directory:</u>&nbsp;&nbsp;';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t\t" . '<td>';
    $output.= "\n\t\t\t" . '<input type="hidden" id="newtypedir" name="newtype" value="dir" />';
    $output.= "\n\t\t\t" . '<input size="30" name="newdir" id="newdir" />';
    $output.= "\n\t\t\t" . '<input type="hidden" name="action" value="mkdir" />';
    $output.= "\n\t\t" . '</td>';
    $output.= "\n\t" . '</tr>';
    $output.= "\n\t" . '<tr>';
    $output.= "\n\t\t" . '<td>&nbsp;</td>';
    $output.= "\n\t\t" . '<td><input class="button" type="submit" value="Create" /></td>';
    $output.= "\n\t" . '</tr>';
    $output.= "\n" . '</table>';
    $output.= "\n" . '</form>';
    $output.= "\n" . '</blockquote>';
 
return $output;
}
/*******************************************************************/
function mkdirs($dir, $mode = FS_RIGHTS_D) {
/**
 * Create a new directory, and the whole path.
 *
 * If  the  parent  directory  does  not exists, we will create it,
 * etc.
 *
 * @param string the directory to create
 * @param int the mode to apply on the directory
 * @return bool return true on success, false else
 */
    $stack = array(basename($dir));
    $path = null;
    while ( ($d = dirname($dir) ) ) {
        if ( !is_dir($d) ) {
            $stack[] = basename($d);
            $dir = $d;
        } else {
            $path = $d;
            break;
        }
    }

    if (( $path = realpath($path)) === false ) return false;
 
    $created = array();
    for ( $n = count($stack) - 1; $n >= 0; $n-- ) {
        $s = $path . '/'. $stack[$n];                                     
        if ( !mkdir($s, $mode) ) {
            for ( $m = count($created) - 1; $m >= 0; $m-- )
                rmdir($created[$m]);
            return false;
        }
        $created[] = $s;     
        $path = $s;
    }
    return true;
}
/*******************************************************************/
?>
