<?
define('MODULE_ROOT', $myNewsConf['path']['sys']['modules']);
define('MODWEB_ROOT', $myNewsConf['path']['web']['modules']);

$myNewsModule['fman2']['dir']['shared']     = MODULE_ROOT . '/userdata/shared/';
$myNewsModule['fman2']['wdir']['shared']    = MODWEB_ROOT . 'userdata/shared/';
$myNewsModule['fman2']['dir']['personal']   = MODULE_ROOT . '/userdata/' . $_SESSION['valid_user'] . '/';
$myNewsModule['fman2']['wdir']['personal']  = MODWEB_ROOT . 'userdata/' . $_SESSION['valid_user'] . '/';
                                                            /* The subdir name. This file has to be located 
                                                               one step above this. Don't start with a slash */
$myNewsModule['fman2']['maxsize']           = 10000000;     /* Maximum upload size in bytes */
$myNewsModule['fman2']['dateformat']        = "Y/m/d G:i:s";/* The date format used for displaying last file change.
                                                               See <http://www.php.net/date> for other options. */
$myNewsModule['fman2']['disableMimeCheck']  = FALSE;        /* TRUE disables check on MIME types. Only file endings are
                                                               checked. Set to FALSE by default for security reasons. */
$myNewsModule['fman2']['allowAllFiles']     = TRUE;         /* If set to TRUE there are no restrictions as to what 
                                                               kind of files are allowed for uploading. This is not 
                                                               encouraged and the default is FALSE for security 
                                                               reasons. */

$myNewsModule['scripts']['display']         = 'display.php';
$myNewsModule['adminScripts']['fman2']      = 'fman2admin.php';  /* Your file upload admin script */
$myNewsModule['admin']['name']['fman2']     = 'Module: File Manager II';
                                                            /* Title for adminScripts */

/* List of allowed file endings and their MIME-types. Both file ending and MIME-type has to match before the file is allowed for upload. */
$allowedfile['jpg'] = "image/jpeg";                         /* JPEG image file (.jpg) */
$allowedfile['jpeg']= "image/jpeg";                         /* JPEG image file (.jpeg) */
$allowedfile['jpe'] = "image/jpeg";                         /* JPEG image file (.jpe) */
$allowedfile['gif'] = "image/gif";                          /* GIF image file */
$allowedfile['png'] = "image/png";                          /* PNG image file */
$allowedfile['tif'] = "image/tif";                          /* TIFF image file (.tif) */
$allowedfile['tiff']= "image/tiff";                         /* TIFF image file (.tiff) */
$allowedfile['html']= "text/html";                          /* HTML file (.html) */
$allowedfile['htm'] = "text/html";                          /* HTML file (.htm) */
$allowedfile['css'] = "text/css";                           /* CSS file (.css) */
$allowedfile['xml'] = "text/xml";                           /* XML file (.xml) */
$allowedfile['txt'] = "text/plain";                         /* Regular text file */
$allowedfile['doc'] = "application/msword";                 /* MS Word document */
$allowedfile['rtf'] = "application/rtf";                    /* RTF document */
$allowedfile['pdf'] = "application/pdf";                    /* PDF document */
$allowedfile['pot'] = "application/mspowerpoint";           /* MS PowerPoint document (.pot) */
$allowedfile['pps'] = "application/mspowerpoint";           /* MS PowerPoint document (.pps) */
$allowedfile['ppt'] = "application/mspowerpoint";           /* MS PowerPoint document (.ppt) */
$allowedfile['ppz'] = "application/mspowerpoint";           /* MS PowerPoint document (.ppz) */
$allowedfile['xls'] = "application/x-excel";                /* MS Excel document */

$allowedAlternate['jpg']    = "image/pjpeg";                /* JPEG image file alternate (.jpg) */
$allowedAlternate['jpeg']   = "image/pjpeg";                /* JPEG image file alternate (.jpeg) */
$allowedAlternate['jpe']    = "image/pjpeg";                /* JPEG image file alternate (.jpe) */
$allowedAlternate['png']    = "image/x-png";                /* PNG image file alternate. */

/* End setup information */

/* Start language information */

$msg['en']['title']         = 'FileManager II:<small> %VAR1% </small> ';
                                                            /* The headline above the dashed line. */
$msg['en']['menuHome']      = '<b>&crarr;</b> Home';        /* Menu item for 'Home' link. */
$msg['en']['menuReload']    = '<b>&#64;</b> Reload';        /* Menu item for 'Reload' link. */
$msg['en']['menuUp']        = '<b>&uArr;</b> Parent Directory';
                                                            /* Menu item for 'Up' link. */
$msg['en']['tableFile']     = 'File';                       /* Table header for the 'File' column. */
$msg['en']['tableOptions']  = 'Options';                    /* Table header for the 'Options column. */
$msg['en']['tableSize']     = 'Size';
$msg['en']['tableDate']     = 'Last change';                /* Table header for the 'Last change' column. */
$msg['en']['tableFoot']     = '%VAR1% file(s), %VAR2%';     /* Table footer. */
$msg['en']['textFileDel']   = 'File deleted:<br /><small>%VAR1%</small>';
                                                            /* Message when file is deleted. */
$msg['en']['textUp']        = 'File uploaded:<br /><small>%VAR1%</small>';
                                                            /* Message when a file has been uploaded. */
$msg['en']['textDirDel']    = 'Directory removed:<br /><small>%VAR1%</small>';
                                                            /* Message when directory is deleted. */
$msg['en']['textNewDir']    = 'Directory created:<br /><small>%VAR1%</small>';
                                                            /* Message when directory is created. */
$msg['en']['textNewFile']   = 'File created:<br /><small>%VAR1%</small>';
                                                            /* Message when file is created. */
$msg['en']['textRen']       = 'File renamed:<br /><small>%VAR1%</small> <b>-&gt;</b> <small>%VAR2%</small>';
                                                            /* Message when file is renamed. */
$msg['en']['textFile']      = '<b>%VAR1% file(s)</b>';      /* Text after directory name. */
$msg['en']['textDirectory'] = 'directory';                  /* Text for directory. */
$msg['en']['textDirEmpty']  = 'Current directory is empty.';
                                                            /* Text when directory is empty. */
$msg['en']['textConfirm']   = 'Do you really want to delete this file?';
                                                            /* Message for confirmation box when clicking 'delete'. */
$msg['en']['titleListFiles']= 'List files in %VAR1%.';      /* Tooltip when hovering directory name. */
$msg['en']['titleOpenFile'] = 'Open %VAR1% in the browser.';/* Tooltip when hovering file name. */
$msg['en']['titleDel']      = 'Delete %VAR1%';              /* Tooltip for delete link. */
$msg['en']['titleRen']      = 'Rename %VAR1%';              /* Tooltip for rename link. */
$msg['en']['linkRename']    = 'rename';                     /* 'Rename' link for files and directories. */
$msg['en']['linkDelete']    = 'delete';                     /* 'Delete' link for files and directories. */
$msg['en']['errNoShow']     = 'You are not allowed to view the source of this file.';
                                                            /* Error when showing source isn't allowed. */
$msg['en']['errDirNotDel']  = 'Directory not deleted:<br /><small>%VAR1%</small><br /><br />Possible error: Directory not empty.';
                                                            /* Error if directory can't be deleted. */
$msg['en']['errFileNotDel'] = 'File not deleted:<br /><small>%VAR1%</small>';
                                                            /* Error if file can't be deleted. */
$msg['en']['errUp0']        = 'Files of the type %VAR1% and the ending .%VAR2% are not allowed for upload.';
                                                            /* Error when uploading illegal file. */
$msg['en']['errUp1']        = 'The file was too large.';    /* Error when file is larger than allowed. */
$msg['en']['errUp2']        = 'Only a part of the file was uploaded. Please try again.';
                                                            /* Error when upload is only partial. */
$msg['en']['errUp3']        = 'No file was uploaded. Please try again.';    /* Error when no file is uploaded. */
$msg['en']['errUnknown']    = 'Unknown error.';             /* Unknown error. */
$msg['en']['errNoDir']      = 'Directory not created:<br /><small>%VAR1%</small>';
                                                            /* Error when directory can't be created. */
$msg['en']['errNoFile0']    = 'File %VAR1% not created.';   /* Error when file can't be created. */
$msg['en']['errNoFile1']    = 'File %VAR1% not created. File ending .%VAR2% not allowed.';
                                                            /* Error when new file ending isn't allowed. */
$msg['en']['errNoMove']     = 'File not moved. You cannot move files outside the designated starting directory.';
                                                            /* Error when trying to move files outside starting directory. */
$msg['en']['errNoRen0']     = 'File could not be renamed:<br /><small>%VAR1%</small><br /><br />Possible reason: File or Directory name already exists.';
                                                            /* Error when file can't be renamed. */
$msg['en']['errNoRen1']     = 'You do not have permissions to rename:<br /><small>%VAR1%</small>';
                                                            /* Error when you don't have permission to rename file. */
$msg['en']['errNoRen2']     = 'File <small>%VAR1%</small> could not be renamed. New file ending (.%VAR2%) not allowed.';
                                                            /* Error when renaming and new file ending is illegal. */
/* End language information */
?>
