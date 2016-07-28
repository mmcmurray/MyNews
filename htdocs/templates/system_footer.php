<?php
/* $Id: system_footer.php 464 2004-08-27 07:39:28Z alien $ */
/********************************************************************/
/*
 * Parse out the template
 */
$tpl->pparse('out', 'layout');
/********************************************************************/

echo "\n" . '<hr>' . "\n";

// Close the timer
require($myNewsConf['path']['sys']['index'] . '/include/libs/timer/timer-foot.php');
?>
        <br />
        <a href="http://mynews.alienated.org/">Powered by MyNews</a>
    </body>
</html>
