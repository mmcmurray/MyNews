<?php

/*******************************************************************/
function fgalAdmin(){
/**
 * This function outputs the list of actions available to be performed
 * to administer the fgal module.
 */
global $myNewsConf, $myNewsModule;

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : ' . $myNewsModule['admin']['name']['fgal'] . ' : Select Tool';

    // Generate the page content.
    $output = <<<HTML
    <p>
	<ul>
	    <li><a href="{$_SERVER['PHP_SELF']}?mode=add">Add New Image</a></li>
	    <li><a href="{$_SERVER['PHP_SELF']}?mode=list_images">Modify/Delete Image</a></li>
        <!-- //
	    <li><a href="{$_SERVER['PHP_SELF']}?mode=list_albums">Modify/Delete Albums</a></li>
	    <li><a href="{$_SERVER['PHP_SELF']}?mode=list_tags">Modify/Delete Tags</a></li>
        // -->
	</ul>
HTML;

    // Build the return Array.
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalAdd(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // Add some javascript checking.
    $jscript= <<<HTML
        <script language="JavaScript">
        <!--

        function isOK(){
          with(document.post){
            if(album.value == 'select'){
              alert("Please select an Album for this image. \\n Or enter a new Album name");
              return false;
            }

            if(album.value == 'none' && new_album.value == '' ){
              alert("Please enter a new Album name.");
              return false;
            }
          }
          return true;
        }

        //-->
        </script>
HTML;

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Add New Image';

    // Generate the page content.
    $output = "\n" . '<p>';
    $output.= "\n" . '<form action="' . $_SERVER['PHP_SELF'] . '?mode=added" enctype="multipart/form-data" method="post" name="post" onSubmit="return isOK()">';
    $output.= "\n" . '<table border="0" cellpadding="0" cellspacing="0">';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td><u>Album:<u></td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <select name="album" OnChange="if(this.value) { this.form.new_album.value = \'\'} else { this.select(); }">';
    $output.= "\n" . '        <option value="select">-- Select</option>';
    $output.= "\n" . '        <option value="none">-- Create</option>';

    // Loop through the available albums.
    $albums = fgalListAlbums($_SESSION['valid_user']);
    if($albums){
        foreach($albums as $album){
            $output.= "\n" . '        <option value="' . $album . '">' . $album . '</option>';
        }
    }

    $output.= "\n" . '      </select> ';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td>&nbsp;</td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="text" name="new_album" value="<new album name>">';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td><u>Filename:</u></td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />';
    $output.= "\n" . '      <input type="file" name="localfile" id="localfile" />';
    $output.= "\n" . '      <input type="hidden" name="action" value="upload" />';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td><u>Title:</u></td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="text" name="title" size="36">';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td valign="top"><u>Description:</u>&nbsp;&nbsp;</td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <textarea name="desc" rows="10" cols="36"></textarea>';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td>&nbsp;</td>';
    $output.= "\n" . '    <td><input type="checkbox" name="add" /> Add another photo.</td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td>&nbsp;</td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="hidden" name="author" value="' . $_SESSION['valid_user'] . '">';
    $output.= "\n" . '      ' . $myNewsConf['button']['submit'];
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';
    $output.= "\n" . '<form>';

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalAdded(){
/**
 * Take the $_POST data from fgalAdd() and inserts the image into the database.
 * Returns an error response to the user if the addition failed, otherwise, it
 * returns a success message.
 */
global $myNewsConf, $myNewsModule;

    // We need to determine whether or not a "new" album is being created.
    // If so, we need to make sure that's what's inserted into the DB.
    if($_POST['album'] == 'none'){
        $_POST['album'] = $_POST['new_album'];
    } 

    // Clean up the name of the album so we can use it to sort the images on the filesystem.
    $cAlbum = fgalCleanAlbumName($_POST['album']);

    // Define the "final" home on the filesystem for the image.
    $fpath  = $myNewsModule['path']['sys']['fgalimg'] . '/' . $_POST['author'] . '/fgal/' . $cAlbum . '/';

    // Create a new instance of the class
    $upload = new uploader;

    // OPTIONAL: set the max filesize of uploadable files in bytes
    $upload->max_filesize($_POST['MAX_FILE_SIZE']);

    // UPLOAD the file
    if ($upload->upload('localfile', 'image', '')) {
        $success = $upload->save_file($fpath,3);
    }

    // If there is an error uploading or placing the file, tell the user.
    if($upload->errors) {
        while(list($key, $var) = each($upload->errors)) {
            $error .= $var;
            mnError('add',500,$error);
        }
        return false;
    }

    // Now that the image upload has succeeded, let's try to insert it into the database.
    $db = mynews_connect();

    // Define the image name, based on the "Clean Album Name" + Image Name.
    $cImage = $cAlbum . '/' . $_FILES['localfile']['name'];

    // Build and execute the query.
    $query  = 'INSERT into ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' values
       ("",
        "' . addslashes($cImage)            . '",
        "' . addslashes($_POST['author'])   . '",
        "' . addslashes($_POST['album'])    . '",
        "' . addslashes($_POST['title'])    . '",
        "' . addslashes($_POST['desc'])     . '",
        "' . date('Y-m-d H:i:s')            . '")';

    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr){
        // If there is a SQL error, we need to remove the file we just uploaded and toss
        // the error back to the user.
        @unlink($fpath . $_FILES['localfile']['name']);
        return false;
    }

    // Build the image thumbnail(s) and display the result up completion.
    $imgDetails = fgalShowThumb($_SESSION['valid_user'], $cImage);
    $imgThumb   = $imgDetails[0];
    $imgZoom    = $imgDetails[1];
    $imgOrig    = $imgDetails[2];
    $imgRes     = $imgDetails[3];

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Image Added';

    // Generate the page content.
    $output = "\n" . '<div style="margin-left: 5px">';
    $output.= "\n" . '  <p>';
    $output.= "\n" . '    <div style="float: left;">';
    $output.= "\n" . '      <a href="' . $imgZoom . '" rel="lightbox[random]" title="' . $lbtitle . '">' . $imgThumb . '</a>';
    $output.= "\n" . '    </div>';
    $output.= "\n" . '    <b>Success:</b> <i>' . $_FILES['localfile']['name'] . '</i> added to the <b>' . $_POST['album'] . '</b> album.';
    $output.= "\n" . '    <br />';
    if(!empty($_POST['title'])){
        $output.= "\n" . '    <br />';
        $output.= "\n" . '    <b>Title:</b> ' . $_POST['title'];
    }
    if(!empty($_POST['desc'])){
        $output.= "\n" . '    <br />';
        $output.= "\n" . '    <b>Description:</b> ' . $_POST['desc'];
    }
    $output.= "\n" . '  </p>';
    $output.= "\n" . '</div>';

    // Set a meta refresh.
    if($_POST['add']){
        $meta   = '<meta http-equiv="Refresh" content="2; url=' . $_SERVER['PHP_SELF'] . '?mode=add">';
    } else {
        $meta   = '<meta http-equiv="Refresh" content="2; url=' . $_SERVER['PHP_SELF'] . '">';
    }

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalImageList(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // if $_GET['view'] is unset, we'll set it to "mine"
    // Meaning that we'll only be showing images for the
    // particular user.
    if(!$_GET['view']) $_GET['view'] = 'my.images';

    // Define the page specific javascript.
    $jscript    = <<<EOT
    <script language="JavaScript" type="text/javascript">
    <!--

    function confirmDelete(){
         where_to= confirm("Do you really want to delete the selected image(s)?"); 
        if (where_to== true) { 
            return true;
        } else {  
            return false;
        }
    }

    //-->
    </script>
EOT;
   

    // Define which view we present, based on the user type.
    // Authors/Editors can only see their own pics.  Admin(s) can
    // do anything to anyone's pics.
    if(strtolower($_SESSION['status']) == 'admin'){
        $xtra1  = '';
        $xtra2  = '';
        if($_GET['view'] == 'my.images')    $xtra1 = 'style="font-weight: bold; text-decoration: underline;"';
        if($_GET['view'] == 'all.images')   $xtra2 = 'style="font-weight: bold; text-decoration: underline;"';

        $output.= "\n" . '<p>';
        $output.= "\n" . '<a ' . $xtra1 . ' href="' . $_SERVER['PHP_SELF']  . '?mode=list_images&amp;view=my.images">My Images</a>';
        $output.= "\n" . '&nbsp;/&nbsp;';
        $output.= "\n" . '<a ' . $xtra2 . ' href="' . $_SERVER['PHP_SELF']  . '?mode=list_images&amp;view=all.images">All Images</a>';
        $output.= "\n" . '</p>';
    } else {
        $output.= "\n" . '<br />';
    }

    // Continue to output the page content.
    $output.= "\n" . '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '?mode=delete" name="the_form" onSubmit="return confirmDelete()">';
    $output.= "\n" . '<table width="99%" border="0">';

    // If $_GET['show'] is not set, we set it to 0
    if(!$_GET['show']) $_GET['show'] = 0;

    // Open a database connection.
    $db = mynews_connect();
    
    // Determine the AND clause, based on what $_GET['view'] is set to.
    $aClause = '';
    if($_GET['view'] == 'my.images') $aClause = 'AND author = "' . $_SESSION['valid_user'] . '"';

    // Build / Execute the query.
    $query  = '
        SELECT
            pid,
            filename,
            title,
            author,
            unix_timestamp(date) as timestamp
        FROM
            ' . $myNewsModule['db']['tbl']['fgal']['images'] . '
        WHERE (1)
        ' . $aClause . '
        ORDER by date desc
        LIMIT ' . $_GET['show'] . ', 10';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // Loop through our results and display a delete/edit link.
    $i  = 0;
    while($row = mysql_fetch_assoc($result)){
        // Extract column returned column titles into vars.
        extract($row);

        // Get the image details for each image.
        $details= fgalShowThumb($author, $filename);

        $xtra   = '';
        if($i%2) $xtra = ' class="alt"';
        $output.= "\n" . '  ' . row_place($i);

        // Output each row of the table.
        $output.= "\n" . '    <td width="50%" valign="top">';
        $output.= "\n" . '      <table class="t1" width="100%" border="0">';
        $output.= "\n" . '        <tr>';
        $output.= "\n" . '          <td width="50%"><div style="padding: 5px;"><a href="' . $_SERVER['PHP_SELF'] . '?mode=edit&amp;id=' . $pid . '">' . $details[0] . '</a></div></td>';
        $output.= "\n" . '          <td width="50%" valign="top" align="right">';
        $output.= "\n" . '              <table border="0">';
        $output.= "\n" . '                <tr>';
        $output.= "\n" . '                  <td><b><u>User:</u></b></td><td>' . $author . '</td>';
        $output.= "\n" . '                </tr>';
        $output.= "\n" . '                <tr>';
        $output.= "\n" . '                  <td><b><u>Delete:</u></b></td><td align="center"><input type="checkbox" name="del[' . $pid . ']" /></td>';
        $output.= "\n" . '                </tr>';
        $output.= "\n" . '              </table>';
        $output.= "\n" . '          </td>';
        $output.= "\n" . '        </tr>';
        $output.= "\n" . '        <tr>';
        $output.= "\n" . '          <td colspan="2"><a href="' . $_SERVER['PHP_SELF'] . '?mode=edit&amp;id=' . $pid . '">' . $title . '</a></td>';
        $output.= "\n" . '        </tr>';
        $output.= "\n" . '      </table>';
        $output.= "\n" . '    </td>';
        $i++;
    }

    // Close out the HTML table.
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td colspan="2">';
    $output.= "\n" . '      ' . $myNewsConf['button']['submit'];
    $output.= "\n" . '      <input type="hidden" name="show" value="' . $_GET['show'] . '" />';
    $output.= "\n" . '      <input type="hidden" name="view" value="' . $_GET['view'] . '" />';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';
    $output.= "\n" . '</form>';

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Modify / Delete Image';

    // Build our page navigation.
    $query  = 'SELECT count(*) as total FROM ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' WHERE (1) ' . $aClause;
    $link   = $_SERVER['PHP_SELF'] . '?mode=list_images&amp;view=' . $_GET['view'] . '&amp;show=';
    $output.= buildNav($query,$link,$_GET['show'],10);

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalDelete(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // Return an error if the "del[]" hash is empty.  We can't delete anything if we don't have
    // a list.
    if(empty($_POST['del'])){
        mnError('add',102,'You must select one or more images for deletion.');
        return false;
    }

    // Build a comma seperated list of id's for us to delete.
    $idlist = implode(',',array_keys($_POST['del']));

    $output = "\n" . '<p>';

    // Establish a database connection.
    $db = mynews_connect();

    // First we need to gather some information from the database record, so we can delete the
    // images from the filesystem.
    $query  = '
        SELECT
            author,
            filename
        FROM
            ' . $myNewsModule['db']['tbl']['fgal']['images'] . '
        WHERE
            pid in(' . $idlist . ')';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // Loop through the results, and delete the file(s) selected.
    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $delete = fgalRemoveImage($author,$filename);
        if(!$delete) return false;
        $output.= "\n" . 'Successfully Removed: ' . $filename . '<br />';
    }

    // Now that we've passed everything, we can delete all of the records from the database.
    $query  = '
        DELETE
        FROM
            ' . $myNewsModule['db']['tbl']['fgal']['images'] . '
        WHERE
            pid in(' . $idlist . ')';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Deletion Successful';

    // Build the META refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $_SERVER['PHP_SELF'] . '?mode=list_images&amp;view=' . $_POST['view'] . '&amp;show=' . $_POST['show'] . '">';
    
    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalEdit($id){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // Open a database connection.
    $db = mynews_connect();

    // Build and execute the query.
    $query  = '
            SELECT
                *
            FROM
                ' . $myNewsModule['db']['tbl']['fgal']['images'] . '
            WHERE
                pid = ' . addslashes($id) . '
            LIMIT 1';
    $result = mysql_query($query);
    $sqlErr = myNewsChkSqlErr($result, $query);
    if($sqlErr) return false;

    // Get the row of the $result and turn the columns into vars.
    $row = mysql_fetch_assoc($result);
    extract($row);

    // Generate the page specific Javascript.
    $jscript    = <<<HTML
    <script language="JavaScript" type="text/javascript">
    <!--

    function isOK(){
      with(document.post){
        if(section.value == 'select'){
          alert("Please choose a section for this item. \\n or enter a new section name");
          return false;
        }

        if(section.value == 'none' && new_section.value == '' ){
          alert("Please enter a new section name.");
          return false;
        }
      }
      return true;
    }

    //-->
    </script>
HTML;

    // Get the Image information.
    $details= fgalShowThumb($author,$filename);

    // Generate the page content.
    $output.= "\n" . '<br />';
    $output.= "\n" . '<div style="float: left;">';
    $output.= "\n" . '<form action="' . $_SERVER['PHP_SELF'] . '?mode=edited" method="POST" name="the_form" onSubmit="return isOkay();">';
    $output.= "\n" . '<table border="0">';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td rowspan="5" valign="top">' . $details[0] . '</td>';
    $output.= "\n" . '    <td valign="top"><b><u>Title:</u></b></td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="text" name="title" value="' . $title . '" size="40" />';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td valign="top"><b><u>Album:</u></b></td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <select name="album" OnChange="if(this.value) { this.form.new_album.value = \'\'} else { this.select(); }">';
    $output.= "\n" . '        <option value="none">-- Create</option>';

    // Loop through the available albums.
    $albums = fgalListAlbums($author);
    if($albums){
        foreach($albums as $name){
            $check  = '';
            if($name == $album) $check = ' selected';
            $output.= "\n" . '        <option value="' . $name . '"' . $check . '>' . $name . '</option>';
        }
    }

    $output.= "\n" . '      </select>';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td>&nbsp;</td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <input type="text" name="new_album" value="<new album name>">';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td valign="top"><b><u>Description:</u></b>&nbsp;&nbsp;</td>';
    $output.= "\n" . '    <td>';
    $output.= "\n" . '      <textarea name="descr" rows="5" cols="50">' . $descr . '</textarea>';
    $output.= "\n" . '      <input type="hidden" name="id" value="' . $id . '" />';
    $output.= "\n" . '      <input type="hidden" name="author" value="' . $author . '" />';
    $output.= "\n" . '    </td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '  <tr>';
    $output.= "\n" . '    <td>&nbsp;</td>';
    $output.= "\n" . '    <td>' . $myNewsConf['button']['submit'] . '</td>';
    $output.= "\n" . '  </tr>';
    $output.= "\n" . '</table>';
    $output.= "\n" . '</form>';
    $output.= "\n" . '</div>';

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : <a href="' . $_SERVER['PHP_SELF'] . '?mode=list_images">Modify</a> : ' . $title;

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalEdited(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // We need to determine whether the user is creating a "new" album or not.
    if($_POST['album'] == 'none') $_POST['album'] = $_POST['new_album'];

    // Connect to the database.
    $db = mynews_connect();

    // Build and execute the update query.
    $query  = '
        UPDATE ' . $myNewsModule['db']['tbl']['fgal']['images'] . '
        SET
            title = "' . addslashes($_POST['title']) . '",
            album = "' . addslashes($_POST['album']) . '",
            descr = "' . addslashes($_POST['descr']) . '" 
        WHERE
            pid   =  ' . addslashes($_POST['id']) . '
        AND author= "' . addslashes($_POST['author']) . '"';
    $result = mysql_query($query);
    if(myNewsChkSqlErr($result, $query)) return false;

    // Generate the page content.
    $output = "\n" . '<p>';
    $output.= "\n" . '  ' . $_POST['title'] . ' was successfully updated.';
    $output.= "\n" . '</p>';

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Select Tool';

    // Build the META refresh.
    $meta   = '<meta http-equiv="Refresh" content="2; url=' . $_SERVER['PHP_SELF'] . '?mode=list_images">';

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalAlbumList(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Select Tool';

    // Generate the page content.
    $output = 'blah';

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalTagList(){
/**
 * Stub Function, for copying.
 */
global $myNewsConf, $myNewsModule;

    // Define the page title.
    $title  = '<a href="' . ADWEB_ROOT . '">Site Administration</a> : <a href="' . $_SERVER['PHP_SELF'] . '">' . $myNewsModule['admin']['name']['fgal'] . '</a> : Select Tool';

    // Generate the page content.
    $output = 'blah';

    // Build the return Array.
    $returnArray['meta']    = $meta;
    $returnArray['jscript'] = $jscript;
    $returnArray['title']   = $title;
    $returnArray['content'] = $output;

return $returnArray;
}
/*******************************************************************/
function fgalListAlbums($user=false){
/**
 * This function gets a list of albums for a given user and returns
 * an array of those albums.
 */
global $myNewsConf, $myNewsModule;

    // Establish DB connection.
    $db = mynews_connect();

    // Set the where clause to limit by user, if a user is defined.
    $clause = '';
    if($user) $clause = 'AND author = "' . addslashes($user) . '"';

    // Build / Excute the query.
    $query  = 'SELECT distinct(album) from ' . $myNewsModule['db']['tbl']['fgal']['images'] . ' WHERE (1) ' . $clause;
    $result = mysql_query($query);

    // Check for DB errors and return if they occur. 
    if(myNewsChkSqlErr($result,$query)) return false;

    while($row = mysql_fetch_assoc($result)){
        extract($row);
        $albumList[] = $album;
    }

return $albumList;
}
/*******************************************************************/
function fgalRemoveImage($author,$filename){
/**
 * Removes an image (unlink) from the filesystem.  This is used in coordination with fgalDelete();
 */
global $myNewsConf, $myNewsModule;

    // Build the full path name to the image.  We'll do this for each file (original, normal, thumbnail).
    $path   = $myNewsModule['path']['sys']['fgalimg'] . '/' . $author . '/fgal/';

    // See if the image has a '/' in it.  If so, we need to split the base and filename out.
    if(strstr($filename,'/')) list($base, $filename) = split('/',$filename);

    if($base){
        $files[]= $path . $base . '/' . $filename;
        $files[]= $path . $base . '/thumbs/' . $filename;
        $files[]= $path . $base . '/normal/' . $filename;
    } else {
        $files[]= $path . '' . $filename;
        $files[]= $path . 'thumbs/' . $filename;
        $files[]= $path . 'normal/' . $filename;
    }

    foreach($files as $file){
        if(!unlink($file)){
            mnError('add',1,'Unable to remove ' . $filename);
            return false;
        }
    }

return true;
}
/*******************************************************************/
?>
