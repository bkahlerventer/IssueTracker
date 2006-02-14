<?php
/* $Id: menus.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Preferences
*/
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}

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
