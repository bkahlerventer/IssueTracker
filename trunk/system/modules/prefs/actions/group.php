<?php
Module::check();
if ($_GET['submit'] == 'true') {
	foreach ($_SESSION['groups'] as $key => $val) {
		if ($_POST[('notemail'.$val)] == 'on') {
			notify_add($_SESSION['userid'],$val,'E');
		} else {
			notify_del($_SESSION['userid'],$val,'E');
		}
		if ($_POST[('notsms'.$val)] == 'on') {
			notify_add($_SESSION['userid'],$val,'S');
		} else {
			notify_del($_SESSION['userid'],$val,'S');
		}
		$update['show_group'] = $_POST[('show'.$val)] == 'on' ? 't' : 'f';
		$update['severity'] = $_POST[('pty'.$val)];
		$_ENV['dbi']->update('group_users',$update,"WHERE userid='".$_SESSION['userid']."' AND gid='$val'");
		unset($update);
	}
}

$ugroups = join(',',$_SESSION['groups']);
$sql = "SELECT g.gid,g.name,u.show_group,u.severity FROM group_users u, groups g 
		WHERE u.gid IN ($ugroups) AND u.userid='".$_SESSION['userid']."' 
		AND g.gid=u.gid ORDER BY g.name";
$groups = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('groups',$groups);
$_ENV['tpl']->assign('severities',$severities);
Module::template('prefs','group.tpl');
?>
