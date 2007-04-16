<?php
# Time Constants
define('_MINUTE_',60);
define('_HOUR_',_MINUTE_ * 60);
define('_DAY_',_HOUR_ * 24);
define('_WEEK_',_DAY_ * 7);
define('_MONTH_',_WEEK_ * 4);
define('_YEAR_',_WEEK_ * 52);

/**
* Just a simple function to have a central location
* where all date formats can be modified at once, might
* even through in option for users to specify their own
* timezone at some point
*
* @param integer $timestamp Unix timestamp
* @return string
*/
function date_format($timestamp = null,$showtime = TRUE) {
	if (empty($timestamp)) {
		$timestamp = time();
	}
	if ($_SESSION['prefs']['local_tz'] == 't') {
    	if ($showtime) {
			$date = gmdate($_SESSION['prefs']['date_format']." h:ia",$timestamp - ($_COOKIE['tz'] * 3600));
		} else {
			$date = gmdate($_SESSION['prefs']['date_format'],$timestamp - ($_COOKIE['tz'] * 3600));
		}
	} else {
		if ($showtime) {
			$date = date($_SESSION['prefs']['date_format']." h:ia",$timestamp);
		} else {
			$date = date($_SESSION['prefs']['date_format'],$timestamp);
		}
	}
	return $date;
}

/**
* Take a amount of time in seconds and make it humanly readable
*
* @param integer $time Amount of time in seconds
* @return string $string
*/
function time_format($time) {
	$days = (int)($time / _DAY_);
	$remainder =  $time % _DAY_;
	$hours  =  (int)($remainder / _HOUR_);
	$remainder = $remainder % _HOUR_;
	$minutes = (int)($remainder / _MINUTE_);
	$seconds = $remainder % _MINUTE_;
	$string = sprintf("%3.3d day%s %2.2d hour%s %2.2d minute%s %2.2d second%s",
		$days,$days != 1 ? "s" : "",$hours,$hours != 1 ? "s" : "",
		$minutes,$minutes != 1 ? "s" : "",$seconds,$seconds != 1 ? "s" : "");
	return $string;
}
?>
