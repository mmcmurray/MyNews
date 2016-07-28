<?
session_name('AdminSessID');
session_start();
//if(session_is_registered('valid_user')){
if(isset($_SESSION['valid_user'])){
    header('Login: ' . $_SESSION['valid_user']);
} else {
    $loginURI = $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['login'] . '?return_to=' . $_SERVER['REQUEST_URI'];
    header("Location: " . $loginURI);
}

?>
