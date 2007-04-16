<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}

if (!empty($_GET['active']) and Permission::check('update_group',$_GET['gid'])) {
	$update['active'] = $_GET['active'];
	$dbi->update('groups',$update,"WHERE gid='".$_GET['gid']."'");
	unset($update);
}

$links[] = array(
	'txt' => 'Back to Administration',
	'url' => '?module=admin',
	'img' => $_ENV['imgs']['back']
);
$links[] = array(
	'txt' => 'Create Group',
	'url' => '?module=groups&action=new',
	'img' => $_ENV['imgs']['new_group']
);

if ($_GET['start'] == '') {
	$type = 'Default';
} else {
	$type = $_GET['start'];
}

$sql = "SELECT gid,name,active FROM groups ";
if ($_GET['search'] == 'true' and !empty($_POST['criteria'])) {
	$sql .= "WHERE LOWER(name) LIKE LOWER('".$_POST['criteria']."') ";
} else if ($_GET['start'] != '') {
	if ($_GET['start'] != 'ALL') {
		$sql .= "WHERE UPPER(name) LIKE '".$_GET['start']."%' ";
	}
} else {
	$sql_groups = user_groups($_SESSION['userid'],TRUE);
	$sql .= "WHERE gid IN ($sql_groups) ";
}

$sql .= "ORDER BY name";
$groups = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('groups',$groups);
Module::template('groups','groups.tpl');
?>
