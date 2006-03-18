<?php
Module::check();
if ($_SESSION['group_count'] > 0) {
	$_ENV['menu']['Issues'] = array(
		'url' => '?module=issues',
		'sub' => array(
			'New Issue' => '?module=issues&action=new',
			'My Opened' => '?module=issues&action=my_open',
			'My Assigned' => '?module=issues&action=my_assigned',
			'Search' => '?module=issues&action=search'
		)
	);
}
?>
