<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo _LANGUAGE; ?>">
<?php
if ( $my->id ) {
	initEditor();
}
mosShowHead();
?>
<link href="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/css/template_css.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo $mosConfig_live_site;?>/images/favicon.ico" />
</head>
<body id="page_bg">
    <div id="container">
        <div class="top">
            <div class="topleft"></div>
            <div class="topright"></div>
            <div style="padding: 20px;" align="right">
                <?php mosLoadModules ( 'user4' ); ?>
            </div>
        </div>
<!-- //
        <div class="logo"></div>
        <div class="top"></div>
        <div class="search_outer">
            <div class="search_inner">
            </div>
        </div>
// -->
        <div id="navbar">
            <div class="nav_inner">
<?php mosLoadModules ( 'top' ); ?>
            </div>
        </div>
        <div id="wrapper" class="clearfix">
            <div id="mainbody">
<?php if (mosCountModules('user1') || mosCountModules('user2')) { ?>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr valign="top">
<?php if (mosCountModules('user1')) { ?><td width="50%">
<?php mosLoadModules('user1',-2); ?></td>
<?php } ?>
<?php if (mosCountModules('user1') && mosCountModules('user2')) { ?>
                        <td class="greyline"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="template by joomlashack" class="" height="50" width="20" /></td>
<?php } ?>
<?php if (mosCountModules('user2')) { ?><td width="50%">
<?php mosLoadModules('user2',-2); ?></td>
<?php } ?>
                    </tr>
                </table>
<?php } ?>
<?php if (mosCountModules('user1') && mosCountModules('user2')) { ?>
                <div class="maindivider"></div>
<?php } ?>
<?php mosMainBody(); ?>
            </div>
            <div id="leftcol"><?php mosLoadModules ( 'left' ); ?></div>
        </div>
        <div class="bottom">
            <div class="botleft"></div>
            <div class="botright"></div>
        </div>
    </div>
</body>
</html>
