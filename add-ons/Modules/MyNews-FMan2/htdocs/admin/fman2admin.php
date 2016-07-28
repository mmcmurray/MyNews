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
include(ADMIN_ROOT . '/lib/fman2admin.lib.php');
include(ADMIN_ROOT . '/classes/fileupload.class.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

// Check to see if we are authenticated.
$auth   = isAuth();

// Get our content based on which mode is selected.
if (!$_GET['mode']) $_GET['mode'] = 'main';

switch($_GET['mode']){
    case 'main':
        $contentHash    = fileAdmin();
        break;
    case 'added':
        $contentHash    = fileAdded($_POST['omode']);
        break;
    case 'deleted':
        $contentHash    = fileDeleted($_GET['filename']);
        break;
    default:
        $contentHash['error']   = myNewsError(1,'You\'re not supposed to be here');
        break;
}


// build our content based on $contentHash
$_title     = $contentHash['title'];
$_meta      = $contentHash['meta'];
$_jscript   = $contentHash['jscript'];
$_style     = $contentHash['style'];
$_notice    = $contentHash['notice'];
$_content   = $contentHash['content'];
$_error     = $contentHash['error'];

// We only want to build the boxes if content exists for them.
if($_notice) $_notice = makebox('Notice:',$_notice, 'content');
if($_error) $_error = makebox('Error:',$_error, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('notice',$_notice);
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', '');

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');

/********************************************************************/
?>
