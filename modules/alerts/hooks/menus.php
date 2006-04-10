<?php
Module::check();
if ($_SESSION['group_count'] > 0) {
	$_ENV['imgs']['menu']['Alerts'] = IMGDIR.'alert.png';
	$_ENV['menu']['Alerts']['Current Alerts'] = '?module=alerts';
	$_ENV['imgs']['menu']['Current Alerts'] = IMGDIR.'alert.png';
	if (Permission::check('create_alerts')) {
		$_ENV['menu']['Alerts']['New Alert'] = '?module=alerts&action=new';
		$_ENV['imgs']['menu']['New Alert'] = IMGDIR.'new_alert.png';
	}
}
?>
