<?php
Module::check();
if (!Permission::is_employee()) {
	rediect();
}
if ($_GET['submit'] == 'true') {
	if (is_array($_POST['add_email'])) {
		foreach ($_POST['add_email'] as $key => $val) {
			notify_add($val,$_GET['gid'],'E');
		}	
	}
	if (is_array($_POST['del_email'])) {
		foreach ($_POST['del_email'] as $key => $val) {
			notify_del($val,$_GET['gid'],'E');
		}
	}
	if (is_array($_POST['add_sms'])) {
		foreach ($_POST['add_sms'] as $key => $val) {
			notify_add($val,$_GET['gid'],'S');
		}
	}
	if (is_array($_POST['del_sms'])) {
		foreach ($_POST['del_sms'] as $key => $val) {
			notify_del($val,$_GET['gid'],'S');
		}
	}
	redirect("?module=groups&action=view&gid=".$_GET['gid']);
}

$links[] = array(
	'txt' => 'Back to Group Information',
	'url' => '?module=groups&action=view&gid='.$_GET['gid'],
	'img' => $_ENV['imgs']['back']
);

$members = group_members($_GET['gid']);
$notify_email = notify_list($_GET['gid'],'E');
$notify_sms = notify_list($_GET['gid'],'S');
$_ENV['tpl']->assign('members',$members);
$_ENV['tpl']->assign('notify_email',$notify_email);
$_ENV['tpl']->assign('notify_sms',$notify_sms);
Module::template('groups','edit_notify.tpl');
?>
