<?php
/* $Id: week_events.php 439 2004-08-10 04:06:48Z alien $ */

$title = 'Events:';

include($myNewsConf['path']['sys']['index'] . '/include/classes/date_calc.php');

$show_year  = Date_Calc::dateNow('%Y');
$show_month = Date_Calc::dateNow('%m');
$show_day   = Date_Calc::dateNow('%e');

$week_cal   = Date_Calc::getCalendarWeek($show_day,$show_month,$show_year,'%E');

$buildSQL   = '';
for($row = 0; $row < 7; $row++) {
	$week_year  = Date_Calc::daysToDate(($week_cal[$row]),"%Y");
	$week_month = Date_Calc::daysToDate(($week_cal[$row]),"%m");
	$week_day   = Date_Calc::daysToDate(($week_cal[$row]),"%e");

    $buildSQL  .= ("
            day     = '$week_day' AND
            month   = '$week_month' AND
            year    = '$week_year'
                  ");
    if($row != 6){
        $buildSQL .= 'OR';
    }


    // Here we fill in the hash with dummy data for each date in the week.
    $day_of_week    = Date_Calc::dateFormat($week_day,$week_month,$week_year,"%A. %m/%e");
    $dayHash[$day_of_week]['_']['_']   = '';

} //End for()

// Connect to the database.
mynews_connect();

// Build and execute our query.
$query  = '
    SELECT
        distinct(type),
        count(eid) as count,
        day,
        month,
        year
    FROM ' . $myNewsConf['db']['table']['calendar'] . '
    WHERE
         ' . $buildSQL . '
        AND
        active  = 1
    GROUP BY
        type,
        day';

$result =  mysql_query($query);
$error  =  myNewsChkSqlErr($result,$query);

// Return with an error if it exists.
if($error) return;

while($sql_row = mysql_fetch_assoc($result)) {
    extract($sql_row);

    if(empty($type)) $title = ''; $eventURI = '';

    $day_of_week    = Date_Calc::dateFormat($day,$month,$year,"%A. %m/%e");
    $eventURI       = '?title=' . urlencode($type) . '&day=' . $day . '&month=' . $month . '&year=' . $year;
    $dayHash[$day_of_week][$eventURI][$type]    = $count;
}

$newDateKeys    = array_keys($dayHash);
foreach($newDateKeys as $newDateKey){
    $output.= "\n\t" . '&nbsp;&nbsp;&nbsp;&middot;&nbsp;';
    $output.= "\n\t" .  $newDateKey;
    $output.= "\n\t" . '<br />';

    $newUriKeys   = array_keys($dayHash[$newDateKey]);
    foreach($newUriKeys as $newUriKey){
        // If the dummy data is the key, we don't do anything with it.
        if($newUriKey != '_'){
            $newTitleKeys = array_keys($dayHash[$newDateKey][$newUriKey]);
            foreach($newTitleKeys as $newTitleKey){
                $baseEventURI   = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['event'];
                    $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;';
                    $output.= "\n\t\t" . '&nbsp;&nbsp;&nbsp;&nbsp;';
                    $output.= "\n\t\t" . '<a href ="' . $baseEventURI . $newUriKey . '">' . $newTitleKey . '</a>';
                    $output.= "\n\t\t" . '<small>(' . $dayHash[$newDateKey][$newUriKey][$newTitleKey] . ')</small>';
                    $output.= "\n\t\t" . '<br />';
            }
        }
    }
}
?>
