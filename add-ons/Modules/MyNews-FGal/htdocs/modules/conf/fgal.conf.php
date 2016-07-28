<?php

$myNewsModule['path']['sys']['fgalimg'] = MODULE_ROOT . '/userdata';
$myNewsModule['path']['web']['fgalimg'] = MODWEB_ROOT . 'userdata';

// Note:  The following config key only needs to be set if you want to force
//        fgal to display only one user.
$myNewsModule['default']['fgal']['user']    = NULL;

// Turn on the "details" or show_image view.  Security through obscurity.  The user will have to put
// forth a little effort to steal the images, or find their location to directly link to.
$myNewsModule['default']['fgal']['details'] = true;

// Define how many columns of images you want per row.
$myNewsModule['default']['fgal']['cols']    = 4;

// Define the number of thumbnail images you want per page.
$myNewsModule['default']['fgal']['rows']    = 3;

// Need to define the default height for thumbnails (in pixels).
$myNewsModule['default']['fgal']['thumbs']['height'] = '100';
$myNewsModule['default']['fgal']['normal']['height'] = '480';
$myNewsModule['default']['fgal']['normal']['width']  = '640';

$myNewsModule['db']['tbl']['fgal']['images']= 'mnfgal_images';
$myNewsModule['db']['tbl']['fgal']['tags']  = 'mnfgal_tags';

$myNewsModule['name']['fgal']               = 'Foto Gallery';
$myNewsModule['scripts']['fgal']            = 'fgal.php';

$myNewsModule['admin']['name']['fgal']      = 'Module: Foto Gallery';
$myNewsModule['adminScripts']['fgal']       = 'fgaladmin.php';

// You don't need to change any of the following code.  This determines how many thumbnail
// images should be displayed, based on your 'rows' and 'cols' settings.
extract($myNewsModule['default']['fgal']);
$myNewsModule['default']['fgal']['limit']   = ($rows * $cols);

// Don't change the following.
$myNewsConf['default']['uri']['fgal']   = MODWEB_ROOT . $myNewsModule['scripts']['fgal'] . '?mode=show_image&id={ID}';
?>
