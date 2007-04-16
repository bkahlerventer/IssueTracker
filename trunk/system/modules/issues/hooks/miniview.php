<?php
Module::check();
list($registered) = fetch_status(TYPE_REGISTERED);
$closed = join(',',fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));

if (Permission::is_employee($_SESSION['userid'])) {
	$group_status = array();
	foreach ($_SESSION['groups'] as $gid) {
		$group_status[$gid]['new'] = num_new_issues($gid);
		$group_status[$gid]['open'] = num_open_issues($gid);
		for ($x = 1;$x < 5;$x++) {
			$sql = "SELECT COUNT(i.issueid) FROM issues i, issue_groups g 
					WHERE g.gid='$gid' AND i.issueid=g.issueid AND i.severity='$x' 
					AND i.status NOT IN ($closed) AND g.show_issue='t'";
			$count = $_ENV['dbi']->fetch_one($sql);
			$group_status[$gid]["sev$x"] = $count;
			unset($count);
		}
		$group_status[$gid]['name'] = group_name($gid);
		$sql = "SELECT standing FROM status_reports WHERE gid='$gid'";
		$standing = $_ENV['dbi']->fetch_one($sql);
		if (!empty($standing)) {
			$group_status[$gid]['rating'] = $standing;
		}
		$group_status[$gid]['rating'] += $group_status[$gid]['sev4'] * .5;
		$group_status[$gid]['rating'] += $group_status[$gid]['sev3'] * 1;
		$group_status[$gid]['rating'] += $group_status[$gid]['sev2'] * 2;
		$group_status[$gid]['rating'] += $group_status[$gid]['sev1'] * 3;
		if ($group_status[$gid]['rating'] >= 50) {
			$group_status[$gid]['standing'] = $_ENV['imgs']['urgent'];
		} else if ($group_status[$gid]['rating'] >= 25) {
			$group_status[$gid]['standing'] = $_ENV['imgs']['high'];
		} else {
			$group_status[$gid]['standing'] = $_ENV['imgs']['normal'];
		}
	}
	$_ENV['tpl']->assign('group_status',$group_status);
	Module::template('issues','group_status_miniview.tpl');
}

$ugroups = join(',',$_SESSION['groups']);
$sql = "SELECT issueid,summary,modified,status,gid FROM issues 
		WHERE gid IN ($ugroups) AND status NOT IN ($closed) 
		ORDER BY modified DESC LIMIT 5";
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('last_issues',$issues);

$sql = "SELECT issueid,gid,summary,modified,status FROM issues 
		WHERE opened_by='".$_SESSION['userid']."' AND status NOT IN ($closed) 
		ORDER BY modified DESC LIMIT 5";
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('opened_issues',$issues);

$sql = "SELECT t.issueid,g.gid,t.summary,t.modified,t.status FROM issues t, issue_groups g 
		WHERE g.assigned_to='".$_SESSION['userid']."' AND t.status NOT IN ($closed) 
		AND t.issueid = g.issueid ORDER BY t.modified DESC LIMIT 5";
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('assigned_issues',$issues);
Module::template('issues','miniview.tpl');
?>
