<?

define('APP_ROOT', dirname(__FILE__));
require_once(APP_ROOT . '/../include/config.inc.php');

/*
 * Require Template Classes and functions
 */
include(MYNEWS_ROOT . '/include/classes/template.class.php');
include(MYNEWS_ROOT . '/templates/template.functions.php');
/*************** Begin Code *****************************************/
include(MODULE_ROOT . '/lib/fgal.lib.php');
// Do some pre-processing of the GPC requests.
$_POST  = cleanUpGPC($_POST);
$_GET   = cleanUpGPC($_GET);

$baseAlbum_URI  = $_SERVER['PHP_SELF'];

if($myNewsModule['default']['fgal']['user']){
    $_GET['mode']   = 'list_album';
    $_GET['author'] = $myNewsModule['default']['fgal']['user'];
}

// If $_GET['mode'] is not set, let's go ahead and set it to the default.
if(!$_GET['mode']) $_GET['mode'] = 'list_auth';

switch($_GET['mode']){
    case 'list_auth':
        $contentHash    = fgalShowUserList();
        break;
    case 'list_album':
        $contentHash    = fgalShowAlbumList(addslashes($_GET['author']));
        break;
    case 'show_album':
        $contentHash    = fgalShowAlbum(addslashes($_GET['author']),addslashes($_GET['album']),$_GET['show']);
        break;
    case 'show_image':
        // We need to add the fgalzooming.php block to the left column block list.
        $contentHash    = fgalShowImg($_GET['id']);
        $commentHash    = comments($_GET['id'],0,'fgal');
        break;
}

// Build out content based on $contentHash
$_error     = mnError('print');
$_meta      = $contentHash['meta'];
$_title     = $contentHash['title'];
$_notice    = $contentHash['notice'];
$_iframe    = $contentHash['iframe'];
$_content   = $contentHash['content'];
$_comment   = $commentHash['content'];

// We only want to build the content box if content exist
if($_error)   $_error   = makebox('Error:',$_error, 'content');
if($_notice)  $_notice  = makebox('Note:', $_notice, 'content');
if($_content) $_content = makebox($_title, $_content, 'content');
if($_comment) $_comment = makebox($commentHash['title'],$_comment,'content');

/*
 * Require the header file
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_header.php');

    /*
     * Insert our content into the template
     */
    $tpl->set_var('error',$_error);
    $tpl->set_var('main_content', $_content);
    $tpl->set_var('comments', $_comment);

/*
 * Require the footer file and end the timer.
 */
include($myNewsConf['path']['sys']['index'] . '/templates/system_footer.php');
?>
