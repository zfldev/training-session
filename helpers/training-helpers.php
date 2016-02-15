<?php
/**
 * Display Dates and Times
 * will get the date from post meta and return it in the dateformat of the wp installation
 * @param $post_id, string with $fieldname (custom meta field), bool for $show_date and $show_time
 * @return string with date and/or time
 */
function get_date_from_meta( $post_id, $fieldname, $show_date, $show_time, $dateformat, $timeformat){
	$timestamp = get_post_meta($post_id, $fieldname, true);
	return get_date_from_value($timestamp, $show_date, $show_time, $dateformat, $timeformat);
}

/**
 * Display Dates and Times from a timestamp
 * will get the date from post meta and return it in the dateformat of the wp installation
 * @param $value (custom meta field), bool for $show_date and $show_time
 * @return string with date and/or time
 */
function get_date_from_value($timestamp, $show_date, $show_time, $dateformat, $timeformat){
	if(! isset($dateformat)){
	$dateformat=get_option('date_format');
	}
	if(! isset($timeformat)){
	$timeformat=get_option('time_format');
	}
	
	if ( is_bool ($show_date) AND is_bool ($show_time) ) {		
		if ($show_date && $show_time) {
			$format =  $dateformat . ' ' . $timeformat;
			return esc_textarea( date($format, $timestamp ) );
		}
		elseif ($show_date && !$show_time) {
			$format =  $dateformat;
			return esc_textarea( date($format, $timestamp ) );
		} elseif ($show_time && !$show_date) {
			$format =  $timeformat;
			return esc_textarea( date($format, $timestamp ) );
		}
	} elseif ( is_string ($show_date) OR is_string ($show_time)  ){
		if (is_string ($show_date) && is_string ($show_time) ) {
			$format =  $show_date . ' ' . $show_time;
			return esc_textarea( date($format, $timestamp ) );
		}
		elseif ( is_string ($show_date) AND !is_string($show_time) ) {
			$format =  $show_date;
			return esc_textarea( date($format, $timestamp ) );
		} elseif ( !is_string ($show_date) AND is_string($show_time) ) {
			$format =  $show_time;
			return esc_textarea( date($format, $timestamp ) );
		}
	
	
	} else {
	return FALSE;	
	}
return false;
}

/**
 * Save Dates and Times
 * will save the date and time based on wp date format as timestamp to the datbase
 * @param strings with $date and $time
 * @return int with timestamp
 */
function create_date_for_db($date, $time, $dateformat, $timeformat) {
if(! isset($dateformat)){
$dateformat=get_option('date_format');
}
if(! isset($timeformat)){
$timeformat=get_option('time_format');
}
$format =  $dateformat . ' ' . $timeformat;
$timestamp = date_timestamp_get(date_create_from_format( $format , sanitize_text_field($date) . ' ' . sanitize_text_field($time)));
$timestamp = (int) $timestamp;
return $timestamp;
}
function create_formated_date_for_db($date, $time, $dateformat, $timeformat) {
$format =  $timeformat . ' ' . $dateformat;
$timestamp = date_timestamp_get(date_create_from_format( $format , sanitize_text_field($date) . ' ' . sanitize_text_field($time)));
$timestamp = (int) $timestamp;
return $timestamp;
}

/**
 * Return the label for referees
 * @param string with raw value
 * @return string with huma readable label
 */
function referee_label($value) {
	$label = __('Referee', 'TrainingTD');
	if ('female' == $value) {
	$label = __('Female referee', 'TrainingTD');
	}
	if ('male' == $value) {
	$label = __('Male referee', 'TrainingTD');
	}
	if ('many' == $value) {
	$label = __('Referees', 'TrainingTD');
	}
return $label;
}

/**
 * Replacing the seperator in a date or time by the default value and return the string. 
 * Many people entering values with wrong seperators therefore we are replacing the seperator by the default or submitted seperator
 * by this function.
 * @param string $value and string $seperator
 * @return string $value
 */
 
function replaceSeperator($value, $seperator) {
	$replace = array(",",".",";",":","/","-");
	foreach ( $replace as $search){
	$value = str_replace($search, $seperator, $value);
	}
return $value;
}

function isDateOld( $timestamp ) {
	if ( $timestamp < ( time() - 360000 ) ){
	return true; // Dates should not be older than 100 Days
	}
return false;	
}


/**
 * Last Training Session Date
 * Check in which field the last date of the training is stored = find the biggest timestamp
 * @param post_id
 * @return field fey name
 */
function get_last_training_date($post_id) {

	
return $field_key;
}


/**
 * Preparing the html output for dates and times
 * 
 * @param 
 * @return
 */
function html_dates_out($custom, $dateformat, $timeformat, $datemode, $showtime, $showdate) {
	$datehtml = '';
	$start_date_out = '';
	$end_date_out = '';
	$start_time_out = '';
	$end_time_out = '';
	$period_first_date_printed = false;
	$period_first_time_printed = false;
	$first_date_printed = false;
	$first_time_printed = false;
	foreach ( $custom as $key => $value) {
		if (strpos($key, '_training_day_') === 0) {
			$daycount =  substr($key, 14, 1); // cutting the daynumber out of the key
			$case1 = '_training_day_' . $daycount;
			$case2 = $case1 . '_end';
			switch ($key) {
					case $case1:
					if ($showdate == '1' AND $datemode == 'all' ) {
						$datehtml .= '<span class="tsdate">' . get_date_from_value($value[0], true, false, $dateformat, $timeformat) . '</span></br>';
					}
					if ($showdate == '1' AND $datemode == 'first' AND $first_date_printed == false) {
						$datehtml .= '<span class="tsdate">' . get_date_from_value($custom['_training_day_1'][0], true, false, $dateformat, $timeformat) . '</span></br>';
						$first_date_printed = true;
					}
					if ($showdate == '1' AND $datemode == 'period' AND $period_first_date_printed == false) {
						$start_date_out = '<span class="tsdate">' . get_date_from_value($custom['_training_day_1'][0], true, false, $dateformat, $timeformat) . '</span>';
							if ($showtime == '1') { $end_time_out = ' <span class="time">' . get_date_from_value($custom[$case2][0], false, true, $dateformat, $timeformat) . ' Uhr</span>'; }
						$period_first_date_printed = true;
					} elseif ($showdate == '1' AND $datemode == 'period' AND $period_first_date_printed == true) {
						if ( $value[0] > $custom['_training_day_1'][0]) { // Check if the enddate is older than the first date
							$end_date_out = '</br> <span class="tsdate">' . get_date_from_value($value[0], true, false, $dateformat, $timeformat) . '</span>';
							if ($showtime == '1') { $end_time_out = ' <span class="time">' . get_date_from_value($custom[$case2][0], false, true, $dateformat, $timeformat) . ' Uhr</span>'; }
						}
					}
					break;
					case $case2:
					if ($showtime == '1' AND $datemode =='all' ) {
						$datehtml.= '<span class="tstime">' .get_date_from_value($custom[$case1][0], false, true, $dateformat, $timeformat) . ' – ' . get_date_from_value($value[0], false, true, $dateformat, $timeformat) . ' ' . 'Uhr' . '</span></br>';
					}
					if ($showtime == '1' AND $datemode =='first' AND $first_time_printed == false) {
						$datehtml.= '<span class="tstime">' .get_date_from_value($custom['_training_day_1'][0], false, true, $dateformat, $timeformat) . ' – ' . get_date_from_value($custom['_training_day_1_end'][0], false, true, $dateformat, $timeformat) . ' ' . 'Uhr' . '</span></br>';
						$first_time_printed = true;
					}
					if ($showtime == '1' AND $datemode == 'period' AND $period_first_time_printed == false) {
						$start_time_out = '<span class="tstime">' . get_date_from_value($custom['_training_day_1'][0], false, true, $dateformat, $timeformat) . ' Uhr </span>';
						$period_first_time_printed = true;
					}
				
					break;
				}
		} 
	
	}

	if ($datemode == 'period') {
	$datehtml = $start_date_out . ' ' . $start_time_out . '</br>' . 'bis' . $end_date_out . $end_time_out;
	}
return $datehtml;
}

/**
 * geocode address to gps
 * 
 * @param 
 * @return
 */

function geocode_training_address($street, $postalcode, $city, $country){
	$returndata = array();
		$resp = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($street) . "," . urlencode($postalcode) . "," . urlencode($city) . "," . urlencode($country) . "&sensor=false");
		if ( 200 == $resp['response']['code'] ) {
			$body = $resp['body'];
			$data = json_decode($body);
			if($data->status=="OK"){
				$returndata[0] = $data->results[0]->geometry->location->lat;
				$returndata[1] = $data->results[0]->geometry->location->lng;
			}
		}
	return $returndata;
}

