<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}
for ($x = 1;$x < 11;$x++) {
	if (!empty($_POST['username'.$x]) and !empty($_POST['email'.$x])) {
		$admin = $_POST['admin'.$x] == 'on' ? 't' : 'f';
		$user = array();
		$user['username'] = $_POST['username'.$x];
		$user['email'] = $_POST['email'.$x];
		if (!empty($_POST['first'.$x])) {
			$user['first'] = $_POST['first'.$x];
		}
		if (!empty($_POST['last'.$x])) {
			$user['last'] = $_POST['last'.$x];
		}
		$user['admin'] = $admin;
		$userid = create_user($user);
		if (!is_null($userid)) {
			push_error("User ".$_POST['username'.$x]." created successfully.");
			if ($_POST['emp'.$x] == 'on') {
				group_useradd($userid,_EMPLOYEES_);
			}
			$redirect = TRUE;
		} else {
			push_error("User ".$_POST['username'.$x]." was not created.");
		}
	}
}
if ($redirect) {
	redirect('?module=users');
}
Module::template('users','new.tpl');
?>
