<?php

/********************************************************************/
function fgalShowUserList(){
/**
 * Looks at images in the database and gets a list of unique users
 * with image galleries and returns back a list of users with counts
 * of both albums and images.
 */
global $myNewsConf, $myNewsModule;
    $sname  = $myNewsModule['name']['fgal'];
    $title  = $sname . ': Select User';

    /* Get a list of the available albums */
    $output.= "\n" . '<table class="t2" width="99%">';
    
    $db  = mynews_connect();
    $albums = mysql_query('select count(filename) as piccount ,count(distinct album) as albumcount ,author from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' group by author');

    /* Loop through each album */
    while($row=mysql_fetch_assoc($albums)){
        extract($row);

        $href   = mkSef($_SERVER['PHP_SELF'] . '?mode=list_album&author=' . $author,NULL,false);

        $output.= "\n" . '  ' . row_place($i,2,2,' class="alt2"');
        $output.= "\n" . '    <td width="50%" nowrap>';
        $output.= "\n" . '      <div style="float: left;">';
        $output.= "\n" . '        ' . fgalRandThumb($author,NULL,$href);
        $output.= "\n" . '      </div>';
        $output.= "\n" . '      <div style="margin: 0px 5px 0px 5px; float: left;">';
        $output.= "\n" . '        <a href="' . $href .'">' . $author . '</a><br />';
        $output.= "\n" . '        <small>(<b>' . $albumcount . '</b> Albums / <b>' . $piccount . '</b> photos)</small>';
        $output.= "\n" . '      </div>';
        $output.= "\n" . '    </td>';

        $i++;
    }
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;
return $returnArray;
}
/********************************************************************/
function fgalShowAlbumList($author){
/**
 * Looks for a unique list of albums a user has assigned in the
 * database and returns back that list with counts of each image
 * attached to the album.
 */
global $myNewsConf, $myNewsModule;
    $sname  = $myNewsModule['name']['fgal'];
    $title  = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $sname . '</a>: ' . $author . ': Select Album';

    /* Get a list of the available albums */
    $output.= "\n" . '<table class="t2" border="0" width="99%">';

    $db = mynews_connect();
    $albums = mysql_query('select album,count(*) as num_albums from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' where author="' . addslashes($author) . '" group by album');
    /* Loop through each album */
    $i  = 0;
    while($row=mysql_fetch_assoc($albums)) {
        extract($row);
        $album  = stripslashes($album);
        $href   = mkSef($_SERVER['PHP_SELF'] . '?mode=show_album&author=' . $author . '&album=' . urlencode($album),NULL,false);

        $output.= "\n" . '  ' . row_place($i,2,2,' class="alt2"');
        $output.= "\n" . '    <td width="10%" nowrap>';
        $output.= "\n" . '      <a href="' . $href . '">' . $album . '</a> - <small>' . $num_albums . ' photo(s)</small>';
        $output.= "\n" . '      <br />';
        $output.= "\n" . '      ' . fgalRandThumb($author,$album,$href);
        $output.= "\n" . '    </td>';
        $i++;
    }
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;
return $returnArray;
}
/********************************************************************/
function fgalShowAlbum($author,$album,$show){
/**
 * Outputs a list of thumbnails and "titles" attached to a particular
 * album and author.
 */
global $myNewsConf, $myNewsModule;
    $sname  = $myNewsModule['name']['fgal'];

    // Go ahead and set our starting point at image 0, if $show isn't set.
    if(empty($show)) $show = 0;
    $limit  = $myNewsModule['default']['fgal']['limit'];

    // Make the Album name "normal" again.
    $album  = urldecode($album);

    // Make a database connection.
    $db = mynews_connect();

    $query  = 'select pid,filename,title,date from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' where album="' . addslashes($album) . '" and author="' . addslashes($author) . '" order by date desc limit ' . $show . ',' . $limit;
    $result = mysql_query($query);

    $output.= "\n" . '<table class="t2" width="99%" align="center">';

    while($row=mysql_fetch_assoc($result)) {
        extract($row);

        // Get the image details.  i.e. thunbmail location, fullsize image location, etc...
        $details    = fgalShowThumb($author, $filename);
        $imgThumb   = $details[0];
        $imgZoom    = $details[1];
        $imgOrig    = $details[2];
        $imgRes     = $details[3];

        // Set the "lightbox" description(s).
        $lbtitle    = 'Album: ' . addslashes($album) . '<br />' . 'Title: ' . $title;

        // Output a table with thumbnails of the images in the selected Album.
        $cols   = $myNewsModule['default']['fgal']['cols'];
        $output.= "\n" . '  ' . row_place($i,$cols,$cols,' class="alt2"');
        $output.= "\n" . '    <td valign="top">';
        $output.= "\n" . '      <a href="' . $imgZoom . '" rel="lightbox[random]" title="' . $lbtitle . '">' . $imgThumb . '</a>';
        $output.= "\n" . '      <div style="clear: both;">';
        $output.= "\n" . '        <small>' . $title . '</small>';
        if($myNewsModule['default']['fgal']['details']){
            $output.= "\n" . '        <br />';
            $output.= "\n" . '        <a class="small" href="' . mkSef($_SERVER['PHP_SELF'] . '?mode=show_image&author=' . $author . '&album=' . urlencode($album) . '&id=' . $pid,NULL,false) . '">Details...</a>';
        }
        $output.= "\n" . '      </div>';
        $output.= "\n" . '    </td>';

        flush(); /* Force web server to flush buffer */
        $i++;  //this is what makes multiple columns in the display table
    }    
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';

    $query  = ' SELECT count(*) as total FROM ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' WHERE (1) AND album = "' . addslashes($album) .'" AND author = "' . addslashes($author) . '"';
    $link   = mksef($_SERVER['PHP_SELF'] . '?mode=show_album&author=' . $author . '&album=' . urlencode($album),'show=',false);
    $output.= buildNav($query,$link,$show,$limit);

    $title  = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $sname . '</a>: <a href="' . mkSef($_SERVER['PHP_SELF'] . '?mode=list_album&author=' . $author,NULL,false) . '">' . $author . '</a>: ' . $album;

    $returnArray['title']   = $title;
    $returnArray['content'] = $output;
return $returnArray;
}
/********************************************************************/
function fgalShowImg($id){
/**
 * Outputs a page with the "original" image displayed.
 */
global $myNewsConf, $myNewsModule;
    $sname  = $myNewsModule['name']['fgal'];
    $baseComment_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['comments'];

    if(!$myNewsModule['default']['fgal']['details']){
        mnError('add',1,'Detailed Image view is disabled');
        return false;
    }

    // Make a database connection.
    $db = mynews_connect();

    $query  = 'select pid,filename,author,album,title,descr,date from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' where pid="' . addslashes($id) . '"';
    $result = mysql_query($query);
    
    // Check for SQL errors.
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // Turn the returned column's into vars.
    extract(mysql_fetch_assoc($result));

    // Get the image details for the requested image.
    $details = fgalShowThumb($author,$filename);

    $output.= "\n" . '<div class="story">';
    if($descr) $output.= "\n" . '<div class="note">' . mynews_format($descr) . '</div>';
    $output.= "\n" . '<table align="center" border="0">';
    $output.= "\n" . '  <tr>'; 
    $output.= "\n" . '    <td align="center">'; 
    $output.= "\n" . '      <div class="mat_norm"><img class="img_outline" src="' . $details[1] . '" /></div>';
    $output.= "\n" . '      <div style="clear: both;">';
    $output.= "\n" . '      <span class="note">';
    $output.= "\n" . '        <table border="0" cellspacing="0">';
    $output.= "\n" . '          <tr>';
    $output.= "\n" . '            <td><b>Resolution:</b>&nbsp;</td>';
    $output.= "\n" . '            <td>' . $details[3] . '</td>';
    $output.= "\n" . '          </tr>';
    $output.= "\n" . '          <tr>';
    $output.= "\n" . '            <td><b>File Size:</b>&nbsp;</td>';
    $output.= "\n" . '            <td>' . mkNiceFsize($details[4]) . '</td>';
    $output.= "\n" . '          </tr>';

    // Turn a "full size image" zoom link on, only if the image is larger then the one we are already displaying.
    // If so, we'll just open a new browser window to show the image in.
    list($width,$height) = split('x',$details[3]);
    if($height > $myNewsModule['default']['fgal']['normal']['height']) $zoom = true;
    if($zoom){
        $output.= "\n" . '          <tr>';
        $output.= "\n" . '            <td><b>Original:</b>&nbsp;</td>';
        $output.= "\n" . '            <td><a href="' . $details[2] . '" target="_blank">Click Here</a></td>';
        $output.= "\n" . '          </tr>';
    }
    $output.= "\n" . '        </table>';
    $output.= "\n" . '      </span>';
    $output.= "\n" . '    </div>';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';
    $output.= "\n" .' </div>';
    $output.= "\n" . '<a href="' . $baseComment_URI . '?mode=compose&tid=' . $id . '&other=fgal&parent=0&title=' . base64_encode($title) . '">[Comment]</a>';

    $title  = '<a href="' . $_SERVER['PHP_SELF'] . '">' . $sname . '</a>: <a href="' . mkSef($_SERVER['PHP_SELF'] . '?mode=list_album&author=' . $author,NULL,false) . '">' . $author . '</a>: <a href="' . mkSef($_SERVER['PHP_SELF'] . '?mode=show_album&author=' . $author . '&album=' . urlencode($album),NULL,false) . '">' . $album .':</a> ' . $title;

    $returnArray['title']   = $title;
    $returnArray['iframe']  = $image;
    $returnArray['content'] = $output;
return $returnArray;
}
/********************************************************************/
function fgalImgInfo($author, $album, $image){
/**
 * This function is for the fgalzoomimg.php block.  It takes and image and returns back
 * a table with information pertinent to the image we are currently viewing.
 */
 global $myNewsConf, $myNewsModule;

    // Decode the Image string.
    $image  = base64_decode($image);
    
    // Get the image details for the requested image.
    $details = fgalShowThumb($author,$image);

    // Turn a "full size image" zoom link on, only if the image is larger then the one we are already displaying.
    // If so, we'll just open a new browser window to show the image in.
    list($width,$height) = split('x',$details[3]);
    if($height > $myNewsModule['default']['fgal']['normal']['height']) $zoom = true;

    $output.= "\n" . '  <table border="0" cellspacing="0">';
    $output.= "\n" . '    <tr>';
    $output.= "\n" . '      <td><b>Resolution:</b>&nbsp;</td>';
    $output.= "\n" . '      <td>' . $details[3] . '</td>';
    $output.= "\n" . '    </tr>';
    $output.= "\n" . '    <tr>';
    $output.= "\n" . '      <td><b>File Size:</b>&nbsp;</td>';
    $output.= "\n" . '      <td>' . mkNiceFsize($details[4]) . '</td>';
    $output.= "\n" . '    </tr>';
    if($zoom){
        $output.= "\n" . '    <tr>';
        $output.= "\n" . '      <td><b>Original:</b>&nbsp;</td>';
        $output.= "\n" . '      <td><a href="' . $details[2] . '" target="_new">Click Here</a></td>';
        $output.= "\n" . '    </tr>';
    }
    $output.= "\n" . '  </table>';

    $returnArray['title']   = 'Image Details:';
    $returnArray['content'] = $output;
return $returnArray;
}
/********************************************************************/
function fgalRandThumb($author,$album=false,$href=false){
/**
 * This function displays a random image thumbnail from a particular
 * user's album(s).  If the album is not defined, it randomizes from
 * all the users albums.  Otherwise, it displays a random image from
 * the specified album as well.
 */
global $myNewsConf, $myNewsModule;

    // Establish the database connection.
    $db = mynews_connect();

    $wClause    = 'AND author = "' . addslashes($author) . '"';
    if($album) $wClause = $wClause . ' AND album = "' . addslashes($album) . '"';

    // Build and execute the query.
    $query  = 'SELECT filename from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' where (1) ' . $wClause . ' ORDER by rand() limit 1';
    $result = mysql_query($query);

    // Check for SQL errors.
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // If we get an emtpy row back, there is no need to continue.
    if(!$row = mysql_fetch_assoc($result)) return false;

    // turn all the returned array keys into variables.
    extract($row);

    $details = fgalShowThumb($author,$filename);
    
    if($href){
        $output.= "\n" . '<a href="' . $href . '">' . $details[0] . '</a>';
    } else {
        $output.= "\n" . $details[0];
    }

return $output;
}
/********************************************************************/
function fgalShowThumb($author,$filename){
/**
 * This function checks to see if a thumbnail for the image requested
 * exists.  If not, it attempts to run call fgalGenThum() and create
 * one.  If that fails, it returns the link to the thumbnail with a
 * a pre-defined HTML height and the full sized image.
 *
 * returns an array consisting of:
 *      0 = '<img src="$thumbnail">'
 *      1 = URI of the "zoom" image.
 *      2 = URI of the "original" image.
 *      3 = Dimensions (Resolution) of the "original" image.
 */
global $myNewsConf, $myNewsModule;

    // If $filename has a '/' in it, let's go ahead and break it up so we can handle it properly.
    $bdir   = '';
    $album  = '';
    if(strstr($filename,'/')) list($bdir, $filename) = split('/',$filename);
    if(!empty($bdir)) $album = $bdir . '/';

    // We want to grab the dimension of the "Original" image so we can return them to the user.
    $image      = $myNewsModule['path']['sys']['fgalimg'] . '/' . $author . '/fgal/' . $album . $filename;
    $fsize      = filesize($image);
    list($width, $height, $type, $attr) = getImageSize($image);
    $dimensions = $width . 'x' . $height;

    // Define the image we're concerned with.
    $tpath      = $myNewsModule['path']['sys']['fgalimg'];
    $thumb      = fgalGenThumb($tpath,$author,$filename,$bdir);
    $webPath    = $myNewsModule['path']['web']['fgalimg'] . '/' . $author;
    $imgOrig    = $myNewsModule['path']['web']['fgalimg'] . '/' . $author . '/fgal/' . $album . $filename;
    if($thumb){
        $imgZoom    = $webPath . '/fgal/' . $album . 'normal/' . $filename;
        $imgThumb   = $webPath . '/fgal/' . $album . 'thumbs/' . $filename;
        $size       = NULL;
    } else {
        $imgZoom    = $webPath . '/fgal/' . $album . $filename;
        $imgThumb   = $webPath . '/fgal/' . $album . $filename;
        $size       = 'height="' . $myNewsModule['default']['fgal']['thumbs']['height'] . '"';
    }

    $returnArray[0] = '<div class="mat_thumb"><img class="img_outline" src="' . $imgThumb . '" ' . $size . ' border="0" /></div>';
    $returnArray[1] = $imgZoom;
    $returnArray[2] = $imgOrig;
    $returnArray[3] = $dimensions;
    $returnArray[4] = $fsize;
    $returnArray[5] = $image;

return $returnArray;
}
/********************************************************************/
function fgalGenThumb($path,$author,$filename,$album=NULL) {
/**
 * This function checks for the existance of a thumbnail, and if
 * one already exists, it returns true.  Otherwise, it uses the
 * GD image library to generate a thumbnail.
 */
global $myNewsConf, $myNewsModule;

    // Define the thumbnail directory.
    $tdir   = $path . '/' . $author . '/fgal/' . $album . '/thumbs';
    if(!@filetype($tdir)) {
        if(!@mkdir($tdir,0775)) {
            mnError('add',1,'Unable to create "thumbs" directory - check permissions');
            return false;
        }
    }

    // We need to create a directory to put the "normalized" file into so we can resize the image
    // to a more "friendly" size.  We'll leave the original where it's at, but move the "friendly
    // size image into the "norm" directory.
    $ndir   = $path . '/' . $author . '/fgal/' . $album . '/normal';
    if(!@filetype($ndir)) {
        if(!@mkdir($ndir,0775)) {
            mnError('add',1,'Unable to create "normal" directory - check permissions');
            return false;
        }
    }

    // Set the pre-defined height for the "thumbnail" and "normal" images.
    $tHeight    = $myNewsModule['default']['fgal']['thumbs']['height'];
    $nHeight    = $myNewsModule['default']['fgal']['normal']['height'];

    // Generate the images.
    $thumbs = fgalGenImg($path,$author,$filename,'thumbs',$tHeight,$album);
    $normal = fgalGenImg($path,$author,$filename,'normal',$nHeight,$album);

    if(!$thumbs || !$normal){
        $error  = mnError('add',500,'Something failed miserably');
        return false;
    }
    
return true;
}
/********************************************************************/
function fgalGenImg($path,$author,$filename,$type,$height,$album=NULL){
/**
 * This function generates an image based on a provide base height.  It writes the images to the
 * provided directory and returns a true if the creation is successful.  False if the creation fails.
 */

    // Get the location of our "original" file.
    $ofile  = $path . '/' . $author . '/fgal/' . $album . '/' .  $filename;

    // Define where we're going to store the file based on what $type we're working with.
    switch($type){
        case 'thumbs':
            $nfile  = $path . '/' . $author . '/fgal/' . $album . '/thumbs/' . basename($filename);
            break;
        case 'normal':
            $nfile  = $path . '/' . $author . '/fgal/' . $album . '/normal/' . basename($filename);
            break;
    }

    if(!is_readable($nfile)) {
        // Look for the .png extension
        if(ereg("\.png$",$filename)){
            $src_img = imagecreatefrompng($ofile);
            $new_h  = $height;
            $new_h = imagesx($src_img) / (imagesy($src_img) / $height);
            if(function_exists(imagecopyresampled)){
                $dst_img = imagecreatetruecolor($new_w,$new_h);
                $white = ImageColorAllocate($dst_img, 255, 255, 255);
                imagecolortransparent($dst_img, $white);
                imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
            } else {
                $dst_img=ImageCreate($new_w,$new_h);
                $white = ImageColorAllocate($dst_img, 255, 255, 255);
                imagecolortransparent($dst_img, $white);
                imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
            }
            if(!imagepng($dst_img, $nfile,100)) return false;
            chmod ($nfile, 0664);

        // Look for the .jpg or .jpeg extension
        } elseif(ereg("\.jpe?g",$filename)){
            $src_img = imagecreatefromjpeg($ofile);
            if ( (imagesx($src_img) / (imagesy($src_img) ) ) >= 1 ){
                if( imagesy($src_img) < $height ){
                    $new_w = imagesx($src_img);
                    $new_h = imagesy($src_img);
                } else {
                   $new_h = $height;
                   $new_w = imagesx($src_img) / (imagesy($src_img) / $height);
                }
            } else {
                if( imagesy($src_img) < $height ) {
                   $new_h = imagesx($src_img);
                   $new_w = imagesy($src_img);
                } else {
                   $new_h = $height;
                   $new_w = imagesx($src_img) / (imagesy($src_img) / $height);
                }
            }

            if(function_exists(imagecopyresampled)){
                $dst_img = imagecreatetruecolor($new_w,$new_h);
                imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
            } else {
                $dst_img=ImageCreate($new_w,$new_h);
                ImageCopyResized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));
            }
            if(!imagejpeg($dst_img, $nfile,100)) return false;
            chmod ($nfile, 0664);

        // Return false if not a PNG or JPG file.
        } else { return(false); }
    }
return true;
}
/********************************************************************/
function fgalCleanAlbumName($string){
    // Replace spaces with hypens
    $string = preg_replace('/\s/e' , '_' , $string);

    // Remove non-word characters
    $string = preg_replace('/\W/e' , '' , $string);
    
return $string;
}
/********************************************************************/
?>
