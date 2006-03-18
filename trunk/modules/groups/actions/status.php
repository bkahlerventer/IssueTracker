<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}
if (!empty($_POST['info']) and $_GET['update'] == 'true') {
	if (permission_check('technician',$_GET['gid']) or is_manager()) {
		$insert['gid'] = $_GET['gid'];
		$insert['userid'] = $_SESSION['userid'];
		$insert['date_entered'] = time();
		$insert['info'] = addslashes($_POST['info']);
		$insert['standing'] = $_POST['stand'];
		$dbi->insert('status_reports',$insert);
		redirect('?module=groups&action=status');
	} else {
		redirect('?module=groups&action=status');
	}
}

if ($_GET['history'] == 'true'
and (permission_check('technician',$_GET['gid']) or is_manager())) {
	$sql = "SELECT userid,date_entered,info,standing FROM status_reports 
			WHERE gid='".$_GET['gid']."' ORDER BY date_entered DESC";
	$history = $_ENV['dbi']->fetch_all($sql,'array');
	$_ENV['tpl']->assign('history',$history);
	Module::template('groups','status/history.tpl');
}

if (preg_match('/^[0-9]+$/',$_GET['gid'])) {
	Module::template('groups','status/update.tpl');
} else {
	$ugroups = join(',',$_SESSION['groups']);
	$sql = "SELECT gid,name,end_date FROM groups 
			WHERE status_reports = 't' ";
	$sql .= !Permission::is_manager() ? "AND gid IN ($ugroups) " : '';
	$sql .= "ORDER BY name";
	$groups = $_ENV['dbi']->fetch_all($sql,'array');
	if (is_array($groups)) {
		foreach ($groups as $group) {
			$sql = "SELECT userid,date_entered,info,standing 
					FROM status_reports WHERE gid='".$group['gid']."' 
					ORDER BY date_entered DESC LIMIT 1";
			$info = $_ENV['dbi']->fetch_row($sql,"array");
			$summary[] = array_merge($group,$info);
		}
	}
	$_ENV['tpl']->assign('groups',$summary);
	Module::template('groups','status/summary.tpl');
}
?>
