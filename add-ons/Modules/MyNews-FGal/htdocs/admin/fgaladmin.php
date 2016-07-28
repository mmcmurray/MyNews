<?

define('APP_ROOT', dirname(__FILE__));
require_once(APP_ROOT . '/../include/config.inc.php');
require_once(ADMIN_ROOT . '/include/admin.lib.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.class.php');
include(MYNEWS_ROOT . '/templates/template.functions.php');
/*************** Begin Code *****************************************/
require_once(ADMIN_ROOT . '/lib/fgaladmin.lib.php');
require_once(ADMIN_ROOT . '/lib/fman2admin.lib.php');
require_once(MODULE_ROOT . '/lib/fgal.lib.php');
require_once(ADMIN_ROOT . '/classes/fileupload.class.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

// Check to see if we are authenticated.
$auth   = isAuth();

// We Need to make sure only allowed groups can use this utility.
$errorArray = chkPerm('Admin:Editor:Author');
if($errorArray) $_GET['mode'] = 'error';

if (!isset($_SESSION['valid_user'])) $_GET['mode'] = 'error';
if (!isset($_GET['mode'])) $_GET['mode'] = 'admin';

// initialize $contentHash
$contentHash    = array();

switch($_GET['mode']) {
	case 'admin':
        $contentHash    = fgalAdmin();
		break;
	case 'add':
        $contentHash    = fgalAdd();
		break;
	case 'added':
        $contentHash    = fgalAdded();
		break;
	case 'list_images':
        $contentHash    = fgalImageList();
		break;
    case 'delete':
        $contentHash    = fgalDelete();
        break;
    case 'edit':
        $contentHash    = fgalEdit($_GET['id']);
        break;
    case 'edited':
        $contentHash    = fgalEdited();
        break;
	case 'list_albums':
        $contentHash    = fgalAlbumList();
		break;
	case 'list_tags':
        $contentHash    = fgalTagList();
		break;
    case 'error':
        $contentHash    = $errorArray;
        break;
}


// build our content based on $contentHash
$_error     = mnError('print');
$_error    .= $contentHash['error'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];
$_jscript   = $contentHash['jscript'];
$_meta      = $contentHash['meta'];

// We only want to build the box if content exist
if($_error) $_error = makebox('Error:',$_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
