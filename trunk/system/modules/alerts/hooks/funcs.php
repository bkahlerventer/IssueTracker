<?php
Module::check();

/**
* Gets title for alert
*
* @param integer $aid Id of alert to pull title for
* @return string $title
*/
function alert_title($aid) {
	$sql = "SELECT title FROM alerts WHERE aid='$aid'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Pulls an array of alerts for the given group,
* if gid is omitted then pull for global alerts
*
* @param integer $gid Id of group to pull alerts for
* @return array
*/
function alerts($gid = null) {
	# make sure to initialize $alerts as an array
	$alerts = array();

	if (empty($gid)) {
		$sql = "SELECT aid,title FROM alerts 
				WHERE is_global='t' ORDER BY aid DESC";
	} else {
		$sql = "SELECT a.aid,a.title FROM alerts a,alert_permissions p 
				WHERE a.aid=p.aid AND p.gid='$gid' ORDER BY a.aid DESC";
	}
	return $_ENV['dbi']->fetch_all($sql,'array');
}

/**
* Determine if a user can see an alert
*
* @param integer $aid Id of alert
* @return boolean
*/
function can_view_alert($aid) {
	$sql = "SELECT aid FROM alerts 
			WHERE aid='$aid' AND is_global='t'";
	$aid = $_ENV['dbi']->fetch_one($sql);
	if (is_integer($aid)) {
		return TRUE;
	}

	$sql = "SELECT gid FROM alert_permissions WHERE aid='$aid'";
	$gids = $_ENV['dbi']->fetch_all($sql,'array');
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
