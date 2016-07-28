<body id="page_bg">
    <div id="container">
        <div class="top">
            <div class="topleft"></div>
            <div class="topright"></div>
        </div>
        <div id="titlebar">
            <div class="logo">{main_title}</div>
            <div class="search">{search_box}</div>
        </div>
        <div id="navbar">
            <div class="nav">
                {toolbar}
            </div>
            <div class="login">
                <span class="small">{date}<br />{signon}</span>
            </div>
        </div>
        <div id="wrapper" class="clearfix">
            <div id="mainbody">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr valign="top">
                        <td width="50%">
                        </td>
                    </tr>
                </table>
                {iframe}
                {main_content}
                {error}
                {comments}
            </div>
            <div id="leftcol">{left_nav}{right_nav}</div>
        </div>
        <div class="bottom">
            <div class="botleft"></div>
            <div class="botright"></div>
        </div>
    </div>
</body>
</html>
