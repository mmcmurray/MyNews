<?
header('Content-type: image/png');

$im     = imagecreate(63,16);

$white  = ImageColorAllocate($im, 255,255,255);
$black  = ImageColorAllocate($im, 0,0,0); 

ImageString ($im, 3, 10, 0,  $_GET['text'], $black);

ImagePng($im);
ImageDestroy($im);
?>
