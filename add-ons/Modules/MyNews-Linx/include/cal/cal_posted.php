<?
if ($_POST['event_title'] == 'none') {
    $event_title = $_POST['new_title'];
} else {
    $event_title = $_POST['event_title'];
}
?>
<meta http-equiv="Refresh" content="2; URL=<?php echo $myNewsConf['path']['web']['admin'] . $myNewsConf['adminScripts']['calendar']?>">
<html>
    <head>
        <title><?php echo $myNewsConf['default']['sitename'] . " Admin : Calendar : Posted : " . $event_title?></title>
        <?php include($myNewsConf['path']['sys']['index'] . "/include/themes/" . $myNewsConf['default']['theme'] . "/css.tmpl"); ?>
    </head>
    <body>
        <p>
<?php
require($myNewsConf['path']['sys']['admin'] . "/include/login_check.inc");

if ($_SESSION['status'] != 'Admin' && $_SESSION['status'] != 'Editor' && $_SESSION['status'] != 'Author'){
    print "<p><b>You do not have permission to view this resource!</b>"; 

} else {

    /*
     * timestamp for event, or for first
     * occurence of recurring
     */
    $initial_time = mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

    /*
     * convert two carriage returns to html break.
     */

    if( $_POST['recurring'] ){

        $md5string = $event_title . $initial_time;
        $md5hash = md5($md5string);

        $recurring_type =    $_POST['recurring_type'];
        switch ($recurring_type) {
            case "weekly":

                for( $i=0 ; $i < $_POST['recurring_duration'] ; $i++ ){

                $insmonth = date('m' , $initial_time + $i*7*$SECONDS_PER_DAY);
                $insday   = date('d' , $initial_time + $i*7*$SECONDS_PER_DAY);
                $insyear  = date('Y' , $initial_time + $i*7*$SECONDS_PER_DAY);

                $query = "INSERT INTO " . $myNewsConf['db']['table']['calendar'] . "
                    (msg_id, msg_month, msg_day, msg_year, msg_title, msg_who, msg_where, msg_city, msg_state, msg_text, msg_poster_id, msg_recurring, msg_active)
                VALUES
                    ('NULL' ,
                     '$insmonth' ,
                     '$insday' ,
                     '$insyear' ,
                     '" . addslashes($event_title)          . "' ,
                     '" . addslashes($_POST['event_who'])   . "' ,
                     '" . addslashes($_POST['event_where']) . "' ,
                     '" . addslashes($_POST['event_city'])  . "' ,
                     '" . addslashes($_POST['event_state']) . "' ,
                     '" . addslashes($_POST['event_text'])  . "' ,
                     '" . addslashes($_POST['admin_id'])    . "' ,
                     '$md5hash',
                     '1')";

                $result = mysql_query($query);
                if( !$result ){
                    echo mysql_error() . ": " . mysql_errno();
                }
                                                     
                $msg .= "INSERT for " . date("M d Y",$initial_time + $i*7*$SECONDS_PER_DAY) . "<br>\n";
            }
            break;
            case "bi-weekly":

                $_POST['recurring_duration'] % 2 == 0 ? $_POST['recurring_duration'] = $_POST['recurring_duration'] / 2 : $_POST['recurring_duration'] = ($_POST['recurring_duration'] - 1) / 2;

                for( $i=0 ; $i < $_POST['recurring_duration'] ; $i++ ){


                $insmonth = date('m' , $initial_time + $i*2*7*$SECONDS_PER_DAY);
                $insday   = date('d' , $initial_time + $i*2*7*$SECONDS_PER_DAY);
                $insyear  = date('Y' , $initial_time + $i*2*7*$SECONDS_PER_DAY);

                $query = "INSERT INTO " . $myNewsConf['db']['table']['calendar'] . " 
                    (msg_id, msg_month, msg_day, msg_year, msg_title, msg_who, msg_where, msg_city, msg_state, msg_text, msg_poster_id, msg_recurring, msg_active)
                VALUES 
                    ('NULL',
                     '$insmonth',
                     '$insday',
                     '$insyear',
                     '" . addslashes($event_title)          . "',
                     '" . addslashes($_POST['event_who'])   . "',
                     '" . addslashes($_POST['event_where']) . "',
                     '" . addslashes($_POST['event_city'])  . "',
                     '" . addslashes($_POST['event_state']) . "',
                     '" . addslashes($_POST['event_text'])  . "',
                     '" . addslashes($_POST['admin_id'])    . "',
                     '$md5hash',
                     '1')";

                $result = mysql_query($query);
                if( !$result ){
                    echo mysql_error() . ": " . mysql_errno();
                }

                $msg .= "INSERT for " . date("M d Y",$initial_time + $i*2*7*$SECONDS_PER_DAY) . "<br>\n";
            }
            break;
            case "monthly":
                for( $i=0 ; $i < $_POST['recurring_duration'] ; $i++ ){


                $insmonth   = date('m' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));
                $insday     = date('d' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));
                $insyear    = date('Y' , mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year']));

                $query = "INSERT INTO " . $myNewsConf['db']['table']['calendar'] . " 
                    (msg_id, msg_month, msg_day, msg_year, msg_title, msg_who, msg_where, msg_city, msg_state, msg_text, msg_poster_id, msg_recurring, msg_active)
                VALUES 
                    ('NULL',
                     '$insmonth',
                     '$insday',
                     '$insyear',
                     '" . addslashes($event_title)          . "',
                     '" . addslashes($_POST['event_who'])   . "',
                     '" . addslashes($_POST['event_where']) . "',
                     '" . addslashes($_POST['event_city'])  . "',
                     '" . addslashes($_POST['event_state']) . "',
                     '" . addslashes($_POST['event_text'])  . "',
                     '" . addslashes($_POST['admin_id'])    . "',
                     '$md5hash',
                     '1')";
        
                $result = mysql_query($query);
                if( !$result ){
                    echo mysql_error() . ": " . mysql_errno();
                }

                $msg .= "INSERT for " . date("M d Y",mktime(0,0,0,$_POST['month']+$i,$_POST['day'],$_POST['year'])) . "<br>\n";
            }
            break;
            case "daily":
                for( $i=0 ; $i < $_POST['recurring_duration'] ; $i++ ){


                $insmonth   = date('m' , mktime(0,0,0,$_POST['month'],$_POST['day']+$i,$_POST['year']));
                $insday     = date('d' , mktime(0,0,0,$_POST['month'],$_POST['day']+$i,$_POST['year']));
                $insyear    = date('Y' , mktime(0,0,0,$_POST['month'],$_POST['day']+$i,$_POST['year']));

                $query = "INSERT INTO " . $myNewsConf['db']['table']['calendar'] . " 
                    (msg_id, msg_month, msg_day, msg_year, msg_title, msg_who, msg_where, msg_city, msg_state, msg_text, msg_poster_id, msg_recurring, msg_active)
                VALUES 
                    ('NULL',
                     '$insmonth',
                     '$insday',
                     '$insyear',
                     '" . addslashes($event_title)          . "',
                     '" . addslashes($_POST['event_who'])   . "',
                     '" . addslashes($_POST['event_where']) . "',
                     '" . addslashes($_POST['event_city'])  . "',
                     '" . addslashes($_POST['event_state']) . "',
                     '" . addslashes($_POST['event_text'])  . "',
                     '" . addslashes($_POST['admin_id'])    . "',
                     '$md5hash',
                     '1')";
        
                $result = mysql_query($query);
                if( !$result ){
                    echo mysql_error() . ": " . mysql_errno();
                }

                $msg .= "INSERT for " . date("M d Y",mktime(0,0,0,$_POST['month'],$_POST['day']+$i,$_POST['year'])) . "<br>\n";
            }
            break;
        }

    } else {

        $md5hash = '';
        $insmonth   = date('m' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
        $insday     = date('d' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
        $insyear    = date('Y' , mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']));
       
        $query = "INSERT INTO " . $myNewsConf['db']['table']['calendar'] . " 
            (msg_id , msg_month , msg_day , msg_year , msg_title , msg_who , msg_where , msg_city , msg_state , msg_text , msg_poster_id , msg_recurring , msg_active)
        VALUES 
            ('NULL',
             '$insmonth',
             '$insday',
             '$insyear',
             '" . addslashes($event_title)          . "',
             '" . addslashes($_POST['event_who'])   . "',
             '" . addslashes($_POST['event_where']) . "',
             '" . addslashes($_POST['event_city'])  . "',
             '" . addslashes($_POST['event_state']) . "',
             '" . addslashes($_POST['event_text'])  . "',
             '" . addslashes($_POST['admin_id'])    . "',
             '$md5hash',
             '1')";

        $result = mysql_query($query);
        if( !$result ){
            echo mysql_error() . ": " . mysql_errno();
        }
    }
?>
        <br />
        <br />
        The event <b><?=$event_title?></b> has been added.
        <br />
        <br />
        The text for this event is:
        <br />
        <br />
        <blockquote>
            <p><?php echo nl2br($_POST['event_text'])?>
        </blockquote>
        <p>

<?php
    if( $_POST['recurring'] ) {
        echo "It is a recurring event which will appear ";

        if(eregi("bi" , $_POST['recurring_type'])){
            echo "bi-weekly.<br>\n";
        }
        elseif(eregi("weekly" , $_POST['recurring_type'])){
            echo "weekly.<br>\n";
        }
        elseif(eregi("daily" , $_POST['recurring_type'])){
            echo "daily.<br>\n";
        } else {
            echo "monthly.<br>\n";
        }
    }

    $mail_event_types   = explode(':',$myNewsConf['admin']['cal']['list']);
    if(in_array(strtolower($event_title),$mail_event_types)){
?>

        <br />
        <?=$event_title?> in notify list.  Sending notification to:  <?=$myNewsConf['admin']['cal']['mail']?>

<?
        if($_POST['event_text'] == ''){ 
            $reason = "None Given";
        } else {
            $reason = wordwrap($_POST['event_text'], 70);
        }

        $eventUrl   = $myNewsConf['default']['siteurl'] . "/" . $myNewsConf['scripts']['event'] . "/" . urlencode($event_title) . "/" . $_POST['day'] . "/" . $_POST['month'] . "/" . $_POST['year'] . "/";
        
        /*
         * Here we check to see if it is a daily recurring event, and if so, set the $recDate var to be the last day
         * of the recurring event.
         */
        if(isset($_POST['recurring']) && $_POST['recurring_type'] == 'daily'){
            $recTime    = mktime(0,0,0,$insmonth,$insday,$insyear);
            $recDate    = date($myNewsConf['format']['date']['nice'], $recTime);
            $recTrans   = ' - ' . $recDate;
        } else {
            $recTrans = '';
        }

        $reqDate    = date($myNewsConf['format']['date']['nice'], $initial_time);
        
        $header     = 'From: ' . $myNewsConf['default']['sitename'] . ' Admin <'. $myNewsConf['admin']['site']['mail'] . ">\nX-Mailer: PHP/" . phpversion();  
        $subject    = "New " . $event_title . " event.";
        $body       = "There is a new " . $event_title . " event on the " . $myNewsConf['default']['sitename'] . " website.\n\n";
        $body      .= "Submitted by: " . $_SESSION['fullname'] . ' (' . $_SESSION['valid_user'] . ')' . "\n";
        $body      .= "Date Submitted: " . date($myNewsConf['format']['date']['nice']) . "\n";
        $body      .= "Date Requested: " . $reqDate . $recTrans . "\n\n";
        $body      .= "Reason: \n" . $reason . "\n\n";
        $body      .= "You can view this event at:\n";
        $body      .= $eventUrl;
    
        $recipients = $myNewsConf['admin']['cal']['mail'] . ',' . $_SESSION['email'];
        mail($recipients, $subject, $body, $header);
    } else {
?>
        <br />
        <?=$event_title?> not in notify list.  No notification sent.
<?
    }
} // End Login Check If()
?>
    </body>
</html>
