<?
/* $Id: modules.php 463 2004-08-27 07:38:57Z alien $ */

$title  = 'Modules:';

$moduleKeys = explode(':',$myNewsConf['modules']['main']);
foreach($moduleKeys as $moduleKey){
    $modName= $myNewsModule['name'][$moduleKey];
    $output.= "\n\t" . '&nbsp;&nbsp;&nbsp;&middot;&nbsp;';
    $output.= "\n\t" . '<a href="' . $myNewsConf['path']['web']['modules'] . $myNewsModule['scripts'][$moduleKey] . '">' . $modName . '</a>';
    $output.= "\n\t" . '<br />';
}
?>
