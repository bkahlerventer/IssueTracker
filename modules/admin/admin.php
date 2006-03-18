<?php
Module::check();
# Build array for administration buttons
if (Permission::is_employee()) {
	$buttons = array(
		array(
			'img' => $_ENV['imgs']['user'],
			'txt' => 'User Management',
			'url' => '?module=users',
			'sub' => array(
				'New User' => '?module=users&action=new'
			)
		),
		array(
			'img' => $_ENV['imgs']['group'],
			'txt' => 'Group Management',
			'url' => '?module=groups',
			'sub' => array(
				'New Group' => '?module=groups&action=new',
				'Status Reports' => '?module=groups&action=status'
			)
		)
	);
}

if (Permission::is_manager($_SESSION['userid'])) {
	$buttons[] = array(
		'img' => $_ENV['imgs']['motd'],
		'txt' => 'Message of the Day',
		'url' => '?module=admin&action=edit_motd'
	);
}

if (Permission::is_admin($_SESSION['userid'])) {
	$buttons[] = array(
		'img' => $_ENV['imgs']['debug'],
		'txt' => 'Debugging',
		'sub' => array(
			'Query Tool' => '?module=admin&action=query',
			'Switch Users' => '?module=admin&action=switch_users',
			'Toggle Debugger' => $_SESSION['debugger'] == 'on' ? '?debug=off' : '?debug=on'
		)
	);
	$buttons[] = array(
		'img' => $_ENV['imgs']['permission'],
		'txt' => 'Permissions Management',
		'url' => '?module=admin&action=permissions',
		'sub' => array(
			'Create Permission' => '?module=admin&action=permissions&subaction=new',
			'Permission Sets' => '?module=admin&action=permission_sets',
			'New Permission Set' => '?module=admin&action=permission_sets&subaction=new'
		)
	);
	/*
	$buttons[] = array(
		'img' => $_ENV['imgs']['system'],
		'txt' => 'System Configuration',
		'url' => '?module=admin&action=sysconfig'
	);
	*/
}

if (Permission::check('status_manager')) {
	$buttons[] = array(
		'img' => $_ENV['imgs']['status'],
		'txt' => 'Status Management',
		'url' => '?module=admin&action=statuses',
		'sub' => array(
			'New Status' => '?module=admin&action=statuses&subaction=new'
		)
	);
}
if (Permission::check('category_manager')) {
	$buttons[] = array(
		'img' => $_ENV['imgs']['category'],
		'txt' => 'Category Management',
		'url' => '?module=admin&action=categories',
		'sub' => array(
			'New Category' => '?module=admin&action=categories&subaction=new'
		)
	);
}
if (Permission::check('product_manager')) {
	$buttons[] = array(
		'img' => $_ENV['imgs']['product'],
		'txt' => 'Product Management',
		'url' => '?module=admin&action=products',
		'sub' => array(
			'New Product' => '?module=admin&action=products&subaction=new'
		)
	);
}

$_ENV['tpl']->assign('buttons',$buttons);
Module::template('admin','admin.tpl');
?>
