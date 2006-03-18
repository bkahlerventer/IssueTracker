<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}

$sql = "SELECT permsetid,name FROM permission_sets";
$result = $_ENV['dbi']->query($sql);
if ($dbi->num_rows($result) > 0) {
	$sets = array();
	while (list($id,$name) = $_ENV['dbi']->fetch($result)) {
		$sets[$id] = $name;
	}
}

$_ENV['tpl']->assign('psets',$sets);
$members = group_members($_GET['gid']);
$_ENV['tpl']->assign('members',$members);
if ($_GET['submit'] == 'true') {
	foreach ($members as $key => $val) {
		$update['perm_set'] = $_POST["perm_set_$key"];
		$_ENV['dbi']->update('group_users',$update,"WHERE userid='$key' AND gid='".$_GET['gid']."'");
		unset($update);
		logger("$val permission set for ".group_name($_GET['gid'])." set to ".$sets[$_POST["perm_set_".$key]].".",'user_permissions');
	}
	$sql = "DELETE FROM group_permissions WHERE gid='".$_GET['gid']."'";
	$_ENV['dbi']->query($sql);
	if (is_array($_POST['group_perms'])) {
		foreach ($_POST['group_perms'] as $key => $val) {
			$insert['gid'] = $_GET['gid'];
			$insert['permid'] = !empty($val) ? $val : 'NULL';
			$dbi->insert('group_permissions',$insert);
			unset($insert);
		}
	}
	redirect('?module=groups&action=view&gid='.$_GET['gid']);
}
	
$links[] = array(
	'txt' => 'Back to Group Information',
	'url' => '?module=groups&action=view&gid='.$_GET['gid'],
	'img' => $_ENV['imgs']['back']
);

$sql = "SELECT permid,permission FROM permissions WHERE group_perm='t'";
$perms = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('perms',$perms);

$sql = "SELECT permid FROM group_permissions WHERE gid='".$_GET['gid']."'";
$gperms = $_ENV['dbi']->fetch_all($sql);
$_ENV['tpl']->assign('gperms',$gperms);
Module::template('groups','edit_permissions.tpl');
?>
