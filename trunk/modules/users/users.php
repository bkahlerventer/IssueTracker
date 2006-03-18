<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}

// Update a user's active status
if (!empty($_GET['uid']) and !empty($_GET['active'])) {
	$update['active'] = $_GET['active'];
	$_ENV['dbi']->update('users',$update,"WHERE userid='".$_GET['uid']."'");
	unset($update);
}

$links[] = array(
	'txt' => 'Back to Administration',
	'url' => '?module=admin',
	'img' => $_ENV['imgs']['back']
);
$links[] = array(
	'txt' => 'Create Users',
	'url' => '?module=users&action=new',
	'img' => $_ENV['imgs']['new_user']
);

if ($_GET['start'] == '') {
	$_GET['start'] = 'A';
}

$sql = "SELECT userid,username,email,first_name,last_name,active FROM users ";
if ($_GET['search'] == 'true' and !empty($_POST['criteria'])) {
	$search = 'WHERE (';
	if ($_POST['username'] == 'on') {
		$search .= $search != 'WHERE (' ? 'OR ' : '';
		$search .= "LOWER(username) LIKE LOWER('".$_POST['criteria']."') ";
	}
	if ($_POST['firstname'] == 'on') {
		$search .= $search != 'WHERE (' ? 'OR ' : '';
		$search .= "LOWER(first_name) LIKE LOWER('".$_POST['criteria']."') ";
	}
	if ($_POST['lastname'] == 'on') {
		$search .= $search != 'WHERE (' ? 'OR ' : '';
		$search .= "LOWER(last_name) LIKE LOWER('".$_POST['criteria']."') ";
	}
	if ($_POST['email'] == 'on') {
		$search .= $search != 'WHERE (' ? 'OR ' : '';
		$search .= "LOWER(email) LIKE LOWER('".$_POST['criteria']."') ";
	}
	$sql .= $search.') ';
} else {
	if ($_GET['start'] != 'ALL') {
		$sql .= "WHERE UPPER(username) LIKE '".$_GET['start']."%' ";
	}
}
$sql .= 'ORDER BY username';
$users = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('users',$users);
Module::template('users','users.tpl');
?>
