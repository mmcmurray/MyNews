<?


define('APP_ROOT', dirname(__FILE__));
require(APP_ROOT . '/../include/config.inc.php');

define('MYNEWS_ROOT', $myNewsConf['path']['sys']['index']);
require_once(MYNEWS_ROOT . '/include/libs/lib.inc.php');
require_once(MYNEWS_ROOT . '/include/libs/blocks.lib.php');
require_once(MYNEWS_ROOT . '/include/libs/timer/timer-head.php');

define('ADMIN_ROOT', $myNewsConf['path']['sys']['admin']);
require_once(ADMIN_ROOT . '/include/admin.lib.php');

// Check to see if we are authenticated.
$auth   = isAuth();

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.inc.class');
include(MYNEWS_ROOT . '/templates/template.functions.php');

/*************** Begin Code *****************************************/
include(MYNEWS_ROOT . '/modules/lib/tix.lib.php');
include(MYNEWS_ROOT . '/modules/lib/os.lib.php');

// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

if(!isset($_GET['mode'])) $_GET['mode'] = 'list';
if(!isset($_GET['view'])) $_GET['view'] = 'group.open';

// We need to get the extra SESSION information for this module.
$errorArray = tixGetUserInfo();
// Display an error if the previous call fails.
if($errorArray) $_GET['mode'] = 'error';

// We need to add the administration block
$myNewsConf['blocks']['left'] = addBlock('tixadmin', $myNewsConf['blocks']['left'], 1);

switch($_GET['mode']){
    case 'list':
        $contentHash    = tixList($_GET['view'], $_GET['project'], $_GET['sort']);
        break;
    case 'add':
        $contentHash    = tixAdd();
        break;
    case 'edit':
        $contentHash    = tixEdit($_GET['id']);
        break;
    case 'edited':
        $contentHash    = tixEdited($_POST['mod']['id']);
        break;
    case 'info':
        echo tixInfo($_GET['view'],$_GET['id']);
        return;
        break;
    case 'getNotes':
        $contentHash    = tixGetMods($_GET['id']);
        echo $contentHash['content'];
        return;
        break;
    case 'getOS':
        $contentHash    = osGet($_GET['id']);
        echo $contentHash['content'];
        return;
        break;
    case 'osAdd':
        $contentHash    = osAdd($_GET['id'], $_GET['project']);
        break;
    case 'osAdded':
        $contentHash    = osAdded();
        break;
    case 'error':
        $contentHash    = $errorArray;
        break;
    case 'search':
        $contentHash    = tixSearch();
        break;
    case 'searchResults':
        $contentHash    = tixSearchResults();
        break;
    default:
        $contentHash['title']   = 'Error:';
        $contentHash['content'] = myNewsError(102,'You failed to pass the appropriate arguments');
        break;
}

// build our content based on $contentHash
$_error     = $contentHash['error'];
$_meta      = $contentHash['meta'];
$_jscript   = $contentHash['jscript'];
$_title     = $contentHash['title'];
$_content   = $contentHash['content'];

// We only want to build the content boxes if content exists for them.
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
/********************************************************************/
?>
