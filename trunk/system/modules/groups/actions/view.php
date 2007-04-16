<?php
Module::check();
if (!Permission::is_employee() or !group_exists($_GET['gid'])) {
	redirect();
}

// Make sure to clean the wizard session variable
// or it could cause a nasty loop
unset($_SESSION['GROUP_WIZARD']);

// check to make sure at least one product is defined
$sql = "SELECT pid FROM group_products WHERE gid='".$_GET['gid']."'";
$count = $_ENV['dbi']->fetch_one($sql);
if ($count < 1) {
	push_error('Must have at least one product defined.');
	redirect('?module=groups&action=edit&type=products&gid='.$_GET['gid']);
}

// check to make sure at least one status is defined
$sql = "SELECT sid FROM group_statuses WHERE gid='".$_GET['gid']."'";
$count = $_ENV['dbi']->fetch_one($sql);
if ($count < 1) {
	push_error('Must have at least one status defined.');
	redirect('?module=groups&action=edit&type=statuses&gid='.$_GET['gid']);
}

$sql = "SELECT * FROM groups WHERE gid='".$_GET['gid']."'";
$group = $_ENV['dbi']->fetch_row($sql,'array');
if (is_null($group)) {
	redirect();
}
$_ENV['tpl']->assign('group',$group);

$links[] = array(
	'txt' => 'Back to Groups',
	'url' => '?module=groups',
	'img' => $_ENV['imgs']['back']
);
      
if (Permission::check('update_group',$_GET['gid'])) {
	$links[] = array(
		'txt' => 'Edit Information',
		'url' => '?module=groups&action=edit_info&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['edit']
	);
	$links[] = array(
		'txt' => 'Add/Remove Users',
		'url' => '?module=groups&action=edit_users&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['user']
	);
	$links[] = array(
		'txt' => 'Permissions',
		'url' => '?module=groups&action=edit_permissions&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['permission']
	);
	$links[] = array(
		'txt' => 'Notifications',
		'url' => '?module=groups&action=edit_notify&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['email']
	);
	$links[] = array(
		'txt' => 'Escalations',
		'url' => '?module=groups&action=edit&type=escalation&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['escalate']
	);
	$links[] = array(
		'txt' => 'Statuses',
		'url' => '?module=groups&action=edit&type=statuses&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['status']
	);
	$links[] = array(
		'txt' => 'Internal Statuses',
		'url' => '?module=groups&action=edit&type=istatuses&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['status']
	);
	$links[] = array(
		'txt' => 'Categories',
		'url' => '?module=groups&action=edit&type=categories&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['category']
	);
	$links[] = array(
		'txt' => 'Products',
		'url' => '?module=groups&action=edit&type=products&gid='.$_GET['gid'],
		'img' => $_ENV['imgs']['product']
	);
}

$members = group_members($_GET['gid']);
$_ENV['tpl']->assign('members',$members);
Module::template('groups','view.tpl');
?>
