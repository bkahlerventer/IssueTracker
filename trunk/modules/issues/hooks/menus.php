<?php
Module::check();
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Issues']['Current Issues'] = '?module=issues';
	$_ENV['menu']['Issues']['New Issue'] = '?module=issues&action=new';
	$_ENV['menu']['Issues']['My Opened'] = '?module=issues&action=my_open';
	$_ENV['menu']['Issues']['My Assigned'] = '?module=issues&action=my_assigned';
	$_ENV['menu']['Issues']['Search Issues'] = '?module=issues&action=search';
}
?>
