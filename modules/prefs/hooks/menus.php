<?php
Module::check();
$_ENV['menu']['Preferences'] = array(
	'url' => '?module=prefs',
	'sub' => array()
);
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Preferences']['sub']['Group Preferences'] = '?module=prefs&action=group';
}
if (is_writable(_THEMES_)
and !preg_match('/(4.7)|(4.8)/',$_SERVER['HTTP_USER_AGENT'])) {
	$_ENV['menu']['Preferences']['sub']['Themes & Colors'] = '?module=prefs&action=style';
}
?>
