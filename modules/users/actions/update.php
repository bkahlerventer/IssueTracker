<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}

// check for errors
if (empty($_POST['uname'])){
	push_error('Please enter a username');
} else if (empty($_POST['email'])) {
	push_error('Please enter a email address');
}

if (!errors()) {
	$update['username'] = $_POST['uname'];
	$update['first_name'] = $_POST['first'];
	$update['last_name'] = $_POST['last'];
	$update['email'] = $_POST['email'];
	if (Permission::is_admin()) {
		$update['admin'] = $_POST['admin'] == 'on' ? 't' : 'f';
	}
	if (!Permission::is_admin($_GET['uid']) and $update['admin'] == 't') {
		logger("User ".username($_GET['uid'])." granted admin privileges!",'admin');
		admin_notify("User ".username($_GET['uid'])." granted admin privileges");
	} elseif (Permission::is_admin($_GET['uid']) and $update['admin'] == 'f') {
		logger("User ".username($_GET['uid'])." admin privileges removed",'admin');
	}
	$update['active'] = $_POST['active'] == 'on' ? 't' : 'f';
	if (user_active($_GET['uid']) and $update['active'] == 'f') {
		logger("User ".username($_GET['uid'])." disabled",'users');
	} elseif (!user_active($_GET['uid']) and $update['active'] == 't') {
		logger("User ".username($_GET['uid'])." activated",'users');
	}
	$_ENV['dbi']->update('users',$update,"WHERE userid='".$_GET['uid']."'");
	$_ENV['dbi']->delete('user_permission',array('userid',$_GET['uid']));
	if (is_array($_POST['permissions'])) {
		foreach ($_POST['permissions'] as $val) {
			$insert['userid'] = $_GET['uid'];
			$insert['permid'] = $val;
			$_ENV['dbi']->insert('user_permissions',$insert);
			unset($insert);
		}
	}
	if ($_POST['employee'] == 'on') {
		group_useradd($_GET['uid'],_EMPLOYEES_);
	} else if (is_employee()) {
		group_userdel($_GET['uid'],_EMPLOYEES_);
	}
	if (is_array($_POST['add_groups'])) {
		foreach ($_POST['add_groups'] as $key => $val) {
			group_useradd($_GET['uid'],$val);
		}
	}
	if (is_array($_POST['del_groups'])) {
		foreach ($_POST['del_groups'] as $key => $val) {
			group_userdel($_GET['uid'],$val);
		}
	}
	redirect('?module=users&action=view&uid='.$_GET['uid']);
}
?>
