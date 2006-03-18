<?php
Module::check();
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Alerts'] = array(
		'url' => '?module=alerts',
		'sub' => array()
	);
	if (Permission::check('create_alerts')) {
		$_ENV['menu']['Alerts']['sub']['New Alert'] = '?module=alerts&action=new';
	}
}
?>
