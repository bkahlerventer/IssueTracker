<?php
Module::check();
/**
* Update a user's preference
*
* @param integer $userid ID of user
* @param string $pref Preference to update
* @param mixed $value Value to assign to preference
*/
function update_preference($userid,$pref,$value) {
	$pref = trim($pref);
	$sql = "SELECT value FROM user_prefs WHERE userid='{$userid}' 
			AND preference='{$pref}'";
	$curr_value = $_ENV['dbi']->fetch_one($sql);
	if (!empty($curr_value)) {
		$update['value'] = $value;
		$_ENV['dbi']->update('user_prefs',$update,"WHERE userid='{$userid}' AND preference='{$pref}'");
		unset($update);
	} else {
		$insert['userid'] = $userid;
		$insert['preference'] = $pref;
		$insert['value'] = $value;
		$_ENV['dbi']->insert('user_prefs',$insert);
		unset($insert);
	}
	if (!empty($_SESSION['userid'])) {
		if ($_SESSION['userid'] == $userid) {
			$_SESSION['prefs'][$pref] = $value;
		}
	}
}
?>
