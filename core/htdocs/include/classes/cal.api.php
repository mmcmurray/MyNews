<?php
/* $Id: cal.api.php 498 2005-09-20 04:32:09Z alien $ */
$SECONDS_PER_DAY = 86400;

/*
 * function to get event title and text
 */
function get_event_info($id) {
global $myNewsConf;

    mynews_connect();
    $query = "SELECT msg_title,msg_text,msg_day,msg_year,msg_month FROM " . $myNewsConf['db']['table']['calendar'] . " WHERE msg_id='$id'";
    $result = mysql_query($query);
    if( !$result ) {
        $output =  "
        Host = " . $myNewsConf['db']['hostname']    . " <br>
        User = " . $myNewsConf['db']['dbUser']      . " <br>
        Pass = " . $myNewsConf['db']['dbPass']      . " <br>";
        $output = mysql_error() . ": " . mysql_errno();
    }

    $message_hash = mysql_fetch_assoc($result);

    mysql_free_result($result);
    return $message_hash;
}


/*************************************************/

class month {
    var $months_hash = array(
        '1'  => 'January',
        '01' => 'January',
        '2'  => 'February',
        '02' => 'February',
        '3'  => 'March',
        '03' => 'March',
        '4'  => 'April',
        '04' => 'April',
        '5'  => 'May',
        '05' => 'May',
        '6'  => 'June',
        '06' => 'June',
        '7'  => 'July',
        '07' => 'July',
        '8'  => 'August',
        '08' => 'August',
        '9'  => 'September',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December' );

    var $month_name;
    var $month_number;
    var $year;
    var $month_data;
    var $nextmonth;
    var $nextyear;
    var $prevmonth;
    var $prevyear;

/*
 * constructor function
 */
    function month( $thismonth = '' , $thisyear = '' ) {
    global $myNewsConf;
        if( !$thismonth ) {
            $thismonth = date('m');
        }

        if( !$thisyear ) {
            $thisyear = date('Y');
        }

        $this->month_name   = $this->months_hash[$thismonth];
        $this->month_number = $thismonth;
        $this->year         = $thisyear;

        $this->nextmonth    = sprintf('%02d',$this->month_number+1);
        $this->prevmonth    = sprintf('%02d',$this->month_number-1);
        $this->nextyear     = $this->prevyear = $thisyear;

        if( $this->month_number == '12' ) {
            $this->nextmonth  = '01';
            $this->nextyear   = $thisyear + 1;
        }
        if( $this->month_number == '01' ) {
            $this->prevmonth  = '12';
            $this->prevyear   = $thisyear - 1;
        }

        /*
         * month data
         */

        mynews_connect();
        $query  = "
                SELECT
                    distinct(type),
                    count(eid) as count,
                    day,
                    month,
                    year
                FROM " . $myNewsConf['db']['table']['calendar'] . "
                WHERE
                    month   = '" . $this->month_number . "'
                AND
                    year    = '" . $this->year ."'
                AND
                    active  = 1
                group by
                    type,
                    day
                ORDER BY day";
        $result = mysql_query($query);
        $sqlErr.= myNewsChkSqlErr($result,$query);

        // Return with an error if it exists.
        $errorArray['error'] = $sqlErr;
        if($sqlErr) return $errorArray;

        while ($tmp = mysql_fetch_assoc($result)) {
            if( strcmp( $tmp['type'] , '' ) != 0 ){
                $this->month_data[$tmp['day']]['event_day'][]   = $tmp['day'];
                $this->month_data[$tmp['day']]['event_month'][] = $tmp['month'];
                $this->month_data[$tmp['day']]['event_year'][]  = $tmp['year'];
                $this->month_data[$tmp['day']]['event_count'][] = $tmp['count'];
                $this->month_data[$tmp['day']]['event_title'][] = $tmp['type'];
            }
        } // End while()
    } // End month()


    // obvious function
    function print_month_name() {
        $output = $this->month_name;
        return $output;
    }

    // obvious function
    function print_year() {
        $output = $this->year;
        return $output;
    }

    // obvious function
    function print_datestring() {
        $output = $this->month_name . ', ' . $this->year;
        return $output;
    }

    /*
     * returns the number of days for a given
     * month and year. Months go 1-12 and
     * years are numeric such as "1999"
     */
    function days_in_month( $month, $year ) {
        // older versions of php don't support "t" in the date() function,
        // so I have to do this really kludgy thing.
        if( $month == '01' ) {
            $days_in_month = 31;
        }

        // have to handle leap year
        if( $month == '02' && $year % 4 == 0 && ($year % 100 != 0 || $year % 1000 == 0) ) {
            $days_in_month = 29;
        }
        else if( $month == '02' ) {
            $days_in_month = 28;
        }

        if( $month == '03' ) {
            $days_in_month = 31;
        }
        if( $month == '04' ) {
            $days_in_month = 30;
        }
        if( $month == '05' ) {
            $days_in_month = 31;
        }
        if( $month == '06' ) {
            $days_in_month = 30;
        }
        if( $month == '07' ) {
            $days_in_month = 31;
        }
        if( $month == '08' ) {
            $days_in_month = 31;
        }
        if( $month == '09' ) {
            $days_in_month = 30;
        }
        if( $month == '10' ) {
            $days_in_month = 31;
        }
        if( $month == '11' ) {
            $days_in_month = 30;
        }
        if( $month == '12' ) {
            $days_in_month = 31;
        }
        return $days_in_month;
    } // End days_in_month();

    /* this one's for internal use */
    function _get_date_by_counter($i,$month,$year) {

        $first_day = date('w' , mktime(0,0,0,$month,1,$year));
        //$days_in_month = date("t" , mktime(0,0,0,$month,1,$year));
        // older versions of php don't support "t" in the date() function,
        // so I have to do this really kludgy thing.
        if( $month == '01' ) {
            $days_in_month = 31;
        }

        // have to handle leap year
        if( $month == '02' && $year % 4 == 0 && ($year % 100 != 0 || $year % 1000 == 0) ) {
            $days_in_month = 29;
        }
        else if( $month == '02' ) {
            $days_in_month = 28;
        }

        if( $month == '03' ) {
            $days_in_month = 31;
        }
        if( $month == '04' ) {
            $days_in_month = 30;
        }
        if( $month == '05' ) {
            $days_in_month = 31;
        }
        if( $month == '06' ) {
            $days_in_month = 30;
        }
        if( $month == '07' ) {
            $days_in_month = 31;
        }
        if( $month == '08' ) {
            $days_in_month = 31;
        }
        if( $month == '09' ) {
            $days_in_month = 30;
        }
        if( $month == '10' ) {
            $days_in_month = 31;
        }
        if( $month == '11' ) {
            $days_in_month = 30;
        }
        if( $month == '12' ) {
            $days_in_month = 31;
        }

        if( $i < $first_day ) {
            return '&nbsp;';
        }
        if( $i >= $days_in_month+$first_day ) {
            return '&nbsp;';
        }
        return ($i+1-$first_day);
    } // End _get_date_by_counter();


    /*
     * this is the big cahuna function,
     * draws a calendar.
     */
    function draw($draw_array = '') {
    global $myNewsConf;
    $baseEvent_URI = $myNewsConf['path']['web']['index'] . $myNewsConf['scripts']['event'];

    /*
     * this is a long section which simply gets
     * the parameters which are used in the drawing
     * of the calendar. It's not pretty, but it's
     * simple to understand and modify so I'm
     * running with it.
     */


    if(!isset($draw_array['table_width']) ) {
        $table_width = '100';
    } else {
        $table_width = $draw_array['table_width'];
    }

    if(!isset($draw_array['table_height']) ) {
        $table_height = '100';
    } else {
        $table_height = $draw_array['table_height'];
    }

    if(!isset($draw_array['table_border']) ) {
        $table_border = '0';
    } else {
        $table_border = $draw_array['table_border'];
    }

    if(!isset($draw_array['row_align']) ) {
        $row_align  = 'align="left"';
    } else {
        $row_align  = $draw_array['row_align'];
    }

    if(!isset($draw_array['row_valign']) ) {
        $row_valign = 'valign="top"';
    } else {
        $row_valign = $draw_array['row_valign'];
    }

    /*
     * end of "getting drawing parameters section.
     */


    /***************************************************/

    /* adjust if width is specified in pixels */
    if( eregi("px",$table_width) ) {
        $table_width = eregi_replace("px" , "" , $table_width);
    } else if( $table_width ) {
        $table_width = $table_width . "%";
    }

    /*
     * for some reason, it seems that we have to handle height
     * a little bit differently. It should always be in pixels
     */

    $table_height = eregi_replace("[^[:digit:]]" , "" , $table_height);
    if( !ereg("^[[:digit:]]+$" , $table_height ) ) {
        $table_height = "250";
    }


    /*
     * we need to know how many rows are going to be in this table
     */


    if( $this->days_in_month($this->month_number,$this->year) == 28 && date("w" , mktime(0,0,0,2,1,$this->year)) == 0 ) {
        $num_of_rows = 4;
    }
    else if( $this->days_in_month($this->month_number,$this->year) == 30 && date("w" , mktime(0,0,0,$this->month_number,1,$this->year)) > 5 ) {
        $num_of_rows = 6;
    }
    else if( $this->days_in_month($this->month_number,$this->year) == 31 && date("w" , mktime(0,0,0,$this->month_number,1,$this->year)) > 4 ) {
        $num_of_rows = 6;
    }
    else {
        $num_of_rows = 5;
    }

    /* start printout of main calendar table */
    $output     = '<!-- begin calendar printout -->' . "\n";
    $output    .= '<table width="' . $table_width . '" height="' . $table_height . '" border="' . $table_border . '">' . "\n";

    /*
     * we need to figure out the cell height and width for each of these.
     */

    $dates_cell_height = 'height="' . ceil($table_height / ($num_of_rows + 1)) . '"';

    /* deal with widths given in percentages or in pixels */
    if( ereg( "%" , $table_width ) ) {
        $dates_cell_width = 'width="' . sprintf( "%.3f" , eregi_replace("%","",$table_width)/7 ) . "%" . '"';
    } else {
        $dates_cell_width = 'width="' . ceil( $table_width / 7 ) . '"';
    }

    /*
     * this prints out the top row, which has the names of the
     * days of the week. I consider it a distinct sort of thing.
     */

    $output    .= <<<EOT
    <tr height="35">
      <th><b>Sun.</b></th>
      <th><b>Mon.</b></th>
      <th><b>Tues.</b></th>
      <th><b>Wed.</b></th>
      <th><b>Thur.</b></th>
      <th><b>Fri.</b></th>
      <th><b>Sat.</b></th>
    </tr>

EOT;


    /* Check for unwanted event types.  If the exist, don't print them out. */
    $suppressEventTypes = strtolower($myNewsConf['admin']['cal']['suppress']);
    $suppressEventTypes = explode(':',$suppressEventTypes);

    /* We need to set what "today" is. */
    $today = mktime(0,0,0,date('m'),date('d'),date('Y'));

    /*
     * $extra will contain the CSS class each cell should be considered a part of 
     */
    $extra = 'class="day"';

    /*
     * now print out all the cells for the days of the
     * month. This is the "heart" of this function.
     */
    for( $i=0 ; $i < $num_of_rows*7 ; $i++ ) {
        /* start first row */
        if( $i==0 ) {
            $output    .= "    <tr>\n";
        }
        /* break into a new row at the appropriate places */ 
        if( $i%7 == 0 && $i != 0) {
            $output    .= "    </tr>\n";
            $output    .= "    <tr>\n";
        }

        /*
         * get the current day
         */
        $theday = $this->_get_date_by_counter($i,$this->month_number, $this->year);
        //$thedate= mktime(0,0,0,$this->month_number,($i+1),$this->year);
        $thedate= mktime(0,0,0,$this->month_number,($theday),$this->year);

        if($today == $thedate){
            $extra = 'class="today"';
        } else {
            $extra = 'class="day"';
        }

        /*
         * if there's an event for this day, get it.
         * otherwise, set to "" string
         */
        if(isset($this->month_data[$theday]['event_title'][0])) {
            for( $j=0 ; $j <  count($this->month_data[$theday]['event_title']) ; $j++ ) {
                $trans  = '';
                if ($this->month_data[$theday]['event_count'][$j] > 1) $trans = 's';
       
                if (!in_array(strtolower($this->month_data[$theday]['event_title'][$j]),$suppressEventTypes)){
                    $theevent .=  '&nbsp;&nbsp;<small>('
                                . $this->month_data[$theday]['event_count'][$j] .
                              ')</small>&nbsp;<a href="'
                                . $baseEvent_URI .
                              '?title='
                                . urlencode($this->month_data[$theday]['event_title'][$j]) .
                              '&day='
                                . $this->month_data[$theday]['event_day'][$j] . 
                              '&month='
                                . $this->month_data[$theday]['event_month'][$j] .
                              '&year='
                                . $this->month_data[$theday]['event_year'][$j] .
                              '">'
                                . $this->month_data[$theday]['event_title'][$j]
                                . $trans .
                              '</a><br>';
                }
            } // End for();
        }

        $output .= '      <td ' . $extra . ' ' . $row_align . ' ' . $row_valign . ' ' . $dates_cell_height . ' ' . $dates_cell_width . '>';
        $output .= '<b>' . $theday . '</b>';
        $output .= '<br />';
        $output .= $theevent;
        $output .= '</td>' . "\n";

        /* be sure to clear out $theevent */
        $theevent = '';
        /* close the last row */
        if( $i == $num_of_rows*7-1 ) {
            $output .= '    </tr>' . "\n";
        }
    } // End for();


    $output .= "</table>\n";
    $output .= "<!-- end calendar printout -->\n"; /* end of calendar printout */

    return $output;
  } /* end draw function */

} /*** end of class "month" ***/

?>
