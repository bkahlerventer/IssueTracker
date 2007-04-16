<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}
if (!empty($_GET['uid']) and $_GET['newpass'] == 'TRUE') {
	reset_passwd($_GET['uid']);
}

$links[] = array(
	'txt' => 'Reset Password',
	'url' => "?module=users&action=view&uid=".$_GET['uid']."&newpass=TRUE"
);

$sql = "SELECT userid,username,first_name,last_name,email,active,admin 
		FROM users WHERE userid='".$_GET['uid']."'";
$user = $_ENV['dbi']->fetch_row($sql,'array');
$_ENV['tpl']->assign('user',$user);

$sql = "SELECT permid,permission FROM permissions 
		WHERE user_perm = 't' ORDER BY permission";
$permissions = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('permissions',$permissions);
$user_groups = user_groups($_GET['uid']);
$_ENV['tpl']->assign('user_groups',$user_groups);
$ugroups = join(',',$user_groups);

$sql = "SELECT permid FROM user_permissions WHERE userid='".$_GET['uid']."'";
$user_perms = $_ENV['dbi']->fetch_all($sql);
if (!is_array($user_perms)) {
	$user_perms = array();
}
$_ENV['tpl']->assign('user_perms',$user_perms);

$sql = "SELECT gid,name FROM groups ";
if (!empty($ugroups)) {
	$sql .= "WHERE gid NOT IN ($ugroups) ";
}
$sql .= "ORDER BY name";
$groups = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('groups',$groups);
Module::template('users','view.tpl');
?>
