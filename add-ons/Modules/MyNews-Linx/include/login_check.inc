        <p class="posted">
        Logged in as <?=$_SESSION['valid_user']?> with status of <?=$_SESSION['status']?>.  &nbsp;&nbsp;&nbsp;
        (
        <A HREF="<?=$myNewsConf['path']['web']['admin']?><?=$myNewsConf['adminScripts']['preferences']?>">preferences</A>
        |
        <A HREF="<?=$myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['logout']?>">logout</A>
        )
