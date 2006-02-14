<?php
/* $Id: menus.php 5 2004-08-10 01:30:40Z eroberts $ */
/**
 * @package Issue-Tracker
 * @subpackage Alerts
 */
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}
if ($_SESSION['group_count'] > 0) {
	$leftnav_menu['Alerts'] = array(
		'url' => '?module=alerts',
		'sub' => array()
	);
	if (permission_check('create_alerts')) {
		$leftnav_menu['Alerts']['sub']['New Alert'] = '?module=alerts&action=new';
	}
}
?>
