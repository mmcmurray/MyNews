<?
/* $Id: cal.php 501 2005-09-20 04:44:08Z alien $ */

$contentHash = buildCal(date('m'), date('Y'));
$output = $contentHash['content'];
$title  = $contentHash['title'];

/*******************************************************************/
function buildCal($month,$year) {
global $myNewsConf;

    // Let's go ahead and create a hash of all events for the current month
    mynews_connect();

    $query  = '
        SELECT
            *
        FROM ' . $myNewsConf['db']['table']['calendar'] .'
        WHERE
            month = ' . $month . '
        AND
            year = ' . $year . '
        AND
            active = 1';
    $result = mysql_query($query);
    $sqlErr.= myNewsChkSqlErr($result, $query);

    // Return with an error if it exists.
    $errorArray['content'] = $sqlErr;
    if($sqlErr) return $errorArray;

    while($data = mysql_fetch_assoc($result)){
        extract($data);
        $dayHash[$day] = true;
    }

    // Create array containing abbreviations of days of week.
    $daysOfWeek = array('Su','Mo','Tu','We','Th','Fr','Sa');

    // What is the first day of the month in question?
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    // How many days does this month contain?
    $numberDays = date('t',$firstDayOfMonth);

    // Retrieve some information about the first day of the
    // month in question.
    $dateComponents = getdate($firstDayOfMonth);

    // What is the name of the month in question?
    $monthName = $dateComponents['month'];

    // What is the index value (0-6) of the first day of the
    // month in question.
    $dayOfWeek = $dateComponents['wday'];

    // Create the table tag opener and day headers
    $calendar   = "\n\n\t" . '<!-- Begin Calendar Table -->';
    $calendar  .= "\n\t" . '<table class="calendar" align="center">';

	$today		= date('m/d/Y');
	$rawdate	= str2date($month . '/1/' . $year);
	$rawtoday	= str2date($today);

    $calendar  .= "\n\t\t" . '<tr>';

    // Create the calendar headers
    foreach($daysOfWeek as $day) {
          $calendar .= "\n\t\t\t" . '<th class="cal_header">' . $day . '</th>';
     }

    /* Create the rest of the calendar */

    // Initiate the day counter, starting with the 1st.

    $currentDay = 1;
    $calendar  .= "\n\t\t" . '</tr><tr>';

    // The variable $dayOfWeek is used to ensure that the calendar
    // display consists of exactly 7 columns.
    if ($dayOfWeek > 0) { $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>"; }

    while ($currentDay <= $numberDays) {

        // Seventh column (Saturday) reached. Start a new row.
        if ($dayOfWeek == 7) {
            $dayOfWeek  = 0;
            $calendar  .= "\n\t\t" . '</tr><tr>';
        }

        $date		= $month . '/' . $currentDay . '/' . $year;
        $calURI		= $myNewsConf['scripts']['event'] . '?title=&day=' . $currentDay . '&month=' . $month . '&year=' . $year;

        $rawdate	= str2date($date);

        // Here we check to see if our date exists in the hash
        // and if so, we'll make it a link
        if($dayHash[$currentDay] == true){
            $href   = '<a href="' . $calURI . '">' . $currentDay . '</a>';
        } else {
            $href   = $currentDay;
        }

        // Now we need to see what background our day <td> is going to have.
        if ($rawdate == $rawtoday) $calendar .= "\n\t\t\t" . '<td class="today" align="center">' . $href . '</td>';
        elseif ($dayHash[$currentDay] == true) $calendar .= "\n\t\t\t" . '<td class="linkedday" align="center">' . $href . '</td>';
        else $calendar .= "\n\t\t\t" . '<td class="day" align="center">' . $href . '</td>';

        // Increment counters
        $currentDay++;
        $dayOfWeek++;

    }

     // Complete the row of the last week in month, if necessary
    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        $calendar .= "\n\t\t\t" . '<td colspan="' . $remainingDays . '">&nbsp;</td>';
    }

    $calendar  .= "\n\t" . '</table>';
    $calendar  .= "\n\t" . '<!-- End Calendar Table -->' . "\n\n";

    $returnHash['content']  = $calendar;
    $returnHash['title']    = 'Events: ' . $monthName;

    return $returnHash;
}
/*******************************************************************/
function str2date($in){
	$t = split("/",$in);
	if (count($t)!=3) $t = split("-",$in);
	if (count($t)!=3) $t = split(" ",$in);

	if (count($t)!=3) return -1;

	if (!is_numeric($t[0])) return -1;
	if (!is_numeric($t[1])) return -2;
	if (!is_numeric($t[2])) return -3;

	if ($t[2]<1902 || $t[2]>2037) return -3;

    return mktime (0,0,0, $t[0], $t[1], $t[2]);
}
/*******************************************************************/
?>
