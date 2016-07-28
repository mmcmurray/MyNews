<?php
/* $Id: template.functions.php 460 2004-08-18 22:19:09Z alien $ */

/*******************************************************************/
function makebox ($title, $content, $type='box'){
    global $myNewsConf;
    $t = new Template($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template']);

    switch($type){
        case 'content':
            $t->set_file(array(
                'box'  => 'contentbox.tpl'
            ));
            break;
        case 'box':
            $t->set_file(array(
                'box'  => 'box.tpl'
            ));
            break;
    }

    $t->set_var('title', $title);
    $t->set_var('content', $content);

    return $t->parse('out', 'box'); 
}
/*******************************************************************/
function makecaption ($content, $align="left", $width="150"){
    global $myNewsConf;
    $t = new Template($myNewsConf['path']['sys']['index'] . '/templates/' . $myNewsConf['default']['template']);

    $t->set_file(array(
        'caption'  => 'caption.tpl'
    ));

    $t->set_var('content', $content);
    $t->set_var('align', $align);
    $t->set_var('width', $width);

    return $t->parse('out', 'caption'); 
}
/*******************************************************************/
function addBlock($element, $blockString, $place=1){
    $blockArray = explode(':', $blockString);
    switch($place){
        case true:
            array_unshift($blockArray, $element);
            $blockString = implode(':', $blockArray);
            break;
        case false:
            array_push($blockArray, $element);
            $blockString = implode(':', $blockArray);
            break;
    }
    return $blockString;
}
/*******************************************************************/
/** mynews_sidebar()
 * goes through the list of modules, and creates sidebar on the main page.
 * Parameters: ($position) = where on the page it is to be displayed.
 * Returns: none
 */
function mynews_sidebar($position,$width=175){
    global $myNewsConf;
    global $myNewsModule;

    $continue = 1;
    if(isset($myNewsConf['blocks']['exempt'][$position]) && $myNewsConf['blocks']['exempt'][$position] != '') {
        $excludeArray = explode(':',$myNewsConf['blocks']['exempt'][$position]);

        foreach($excludeArray as $value) {
            if(eregi($value,$_SERVER['REQUEST_URI'])) {
                $continue = '0';
            }
        }
    }

    $return = '';
    if (isset($myNewsConf['blocks'][$position]) && $myNewsConf['blocks'][$position] != '' && $continue == 1) {
        $return.= '<td valign="top" width="' . $width . '">';
        $blocks = explode(':',$myNewsConf['blocks'][$position]);
        foreach($blocks as $block) {
            $error  = '';
            $title  = '';
            $output = '';

            include($myNewsConf['path']['sys']['index'] . '/include/blocks/' . $block . '.php');

            if($error){
                $return.= makebox('Error:',$error);
            } else {
                $return.= makebox($title, $output);
            }
        }
        $return .= '</td>';
        return $return;
    }
}
?>
