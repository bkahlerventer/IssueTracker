<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}
if ($_GET['submit'] == 'true') {
	if (is_array($_POST['addmem'])) {
		foreach ($_POST['addmem'] as $key => $val) {
			group_useradd($val,$_GET['gid']);
		}
	}
	if (is_array($_POST['delmem'])) {
		foreach ($_POST['delmem'] as $key => $val) {
			group_userdel($val,$_GET['gid']);
		}
	}
	redirect('?module=groups&action=edit_users&gid='.$_GET['gid']);
}

$links[] = array(
	'txt' => 'Back to Group Information',
	'url' => '?module=groups&action=view&gid='.$_GET['gid'],
	'img' => $_ENV['imgs']['back']
);

$members = group_members($_GET['gid']);
$_ENV['tpl']->assign('members',$members);
$sql = "SELECT userid,username FROM users ORDER BY username";
$users = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('users',$users);
Module::template('groups','edit_users.tpl');
?>
