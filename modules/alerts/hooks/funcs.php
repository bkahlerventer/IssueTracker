<?php
/* $Id: funcs.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Alerts
*/
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}

/**
* Gets title for alert
*
* @param integer $aid Id of alert to pull title for
* @return string $title
*/
function alert_title($aid) {
	global $dbi;

	$sql = "SELECT title FROM alerts WHERE aid='$aid'";
	$title = $dbi->fetch_one($sql);
	return $title;
}

/**
* Pulls an array of alerts for the given group,
* if gid is omitted then pull for global alerts
*
* @param integer $gid Id of group to pull alerts for
* @return array
*/
function alerts($gid = null) {
	global $dbi;

	// make sure to initialize $alerts as an array
	$alerts = array();

	if (empty($gid)) {
		$sql = "SELECT aid,title FROM alerts 
				WHERE is_global='t' ORDER BY aid DESC";
	} else {
		$sql = "SELECT a.aid,a.title FROM alerts a,alert_permissions p 
				WHERE a.aid=p.aid AND p.gid='$gid' ORDER BY a.aid DESC";
	}
	$alerts = $dbi->fetch_all($sql,'array');
	return $alerts;
}

/**
* Determine if a user can see an alert
*
* @param integer $aid Id of alert
* @return boolean
*/
function can_view_alert($aid) {
	global $dbi;
	
	$sql = "SELECT aid FROM alerts 
			WHERE aid='$aid' AND is_global='t'";
	$aid = $dbi->fetch_one($sql);
	if (!empty(is_integer($aid)) {
		return TRUE;
	}

	$sql = "SELECT gid FROM alert_permissions WHERE aid='$aid'";
	$gids = $dbi->fetch_all($sql,'array');
	if (is_array($gids)) {
		foreach ($gids as $gid) {
			if (in_array($gid,$_SESSION['groups'])) {
				return TRUE;
			}
		}
	}
	return FALSE;
}
?>
