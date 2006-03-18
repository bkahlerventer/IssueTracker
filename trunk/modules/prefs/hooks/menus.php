<?php
Module::check();
$_ENV['menu']['Preferences'] = array(
	'url' => '?module=prefs',
	'sub' => array()
);
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Preferences']['sub']['Group Preferences'] = '?module=prefs&action=group';
}
?>
