<?
/*******************************************************************/
$_date       = date($myNewsConf['format']['date']['nice']);

/*******************************************************************/
$_search    = <<<HTML
        <form method="post" action="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['search']}">
            <input class="textbox" type="text" name="query" size="20">
            <br />
            {$myNewsConf['button']['search']}
        </form>
HTML;

/*******************************************************************/
$_toolbar    = <<<HTML

<a href="{$myNewsConf['path']['web']['index']}">Home</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['archive']}">Archive</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['about']}">About</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['about']}?mode=auth_list">Authors</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['hof']}">Hall of Fame</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['calendar']}">Calendar</a>
&nbsp;&middot;&nbsp;
<a href="{$myNewsConf['path']['web']['admin']}">Admin</a>
HTML;

/*******************************************************************/
$_submit     = <<<HTML
                <p>
                &nbsp;&nbsp;&nbsp;
                Got some thing to share?  Let us know!
                <br />
                &nbsp;&nbsp;&nbsp;
                <a class="dark" href="{$myNewsConf['path']['web']['index']}{$myNewsConf['scripts']['submit']}?mode=add">Submit a story!</a>
HTML;
?>
