<?php
Module::check();
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Alerts']['Current Alerts'] = '?module=alerts';
	if (Permission::check('create_alerts')) {
		$_ENV['menu']['Alerts']['New Alert'] = '?module=alerts&action=new';
	}
}
?>
