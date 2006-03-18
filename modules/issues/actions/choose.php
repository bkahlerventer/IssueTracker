<?php
Module::check();
$ugroups = join(',',$_SESSION['groups']);
$sql = "SELECT gid,name FROM groups WHERE active='t' ";
if (!empty($_GET['start'])) {
	$sql .= $_GET['start'] != 'ALL'
		? "AND UPPER(name) LIKE '".$_GET['start']."%' " : '';
	if (!Permission::is_employee()) {
		$sql .= "AND gid IN ($ugroups) ";
	}
} else {
	$sql .= "AND gid IN ($ugroups) ";
}
$sql .= "ORDER BY name";
$groups = $_ENV['dbi']->fetch_all($sql,'array');
$count = count($groups);
for ($x = 0;$x < $count;$x++) {
	$groups[$x]['new'] = num_new_issues($groups[$x]['gid']);
	$groups[$x]['open'] = num_open_issues($groups[$x]['gid']);
	$last = last_activity($groups[$x]['gid']);
	$groups[$x]['date'] = $last['date'];
	$groups[$x]['user'] = $last['user'];
}
$_ENV['tpl']->assign('groups',$groups);
Module::template('issues','choose.tpl');
?>
