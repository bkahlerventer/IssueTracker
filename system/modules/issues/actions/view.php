<?php
Module::check();
if (empty($_GET['issueid'])) {
	redirect();
}

if (empty($_GET['gid']) and !empty($_POST['gid'])) {
	$_GET['gid'] = $_POST['gid'];
}

if (!empty($_POST['duedate']) and Permission::is_employee($_SESSION['userid'])
and issue_priv($_GET['issueid'],'technician')) {
	$parts = explode('/',$_POST['duedate']);
	$update['due_date'] = mktime(0,0,0,$parts[0],$parts[1],$parts[2]);
	$_ENV['dbi']->update('issues',$update,"WHERE issueid='".$_GET['issueid']."'");
	unset($update);
}
if (!empty($_GET['eid'])) {
	$update['private'] = $_GET['type'] == 'private' ? 't' : 'f';
	logger("Event ".$_GET['eid']." for issue ".$_GET['issueid']." set to ".$_GET['type'],"privacy"); 
	$_ENV['dbi']->update('events',$update,"WHERE eid='".$_GET['eid']."'");
	unset($update);
}
if ($_GET['update_summary'] == 'true' and issue_priv($_GET['issueid'],'technician')
and !empty($_POST['summary'])) {
	$update['summary'] = str_replace("\"","'",$_POST['summary']);
	$_ENV['dbi']->update('issues',$update,"WHERE issueid='".$_GET['issueid']."'");
	issue_log($_GET['issueid'],"Issue Summary Modified");
	unset($update);
}
if ($_GET['reopen'] == 'true'){
	reopen_issue($_GET['issueid']);
}
if (!empty($_GET['gid']) and $_GET['deescalate'] == 'true') {
	deescalate_issue($_GET['issueid'],$_GET['gid']);
}
if (!empty($_GET['toggle_subscribe'])) {
	toggle_subscribe($_GET['issueid']);
}
if (can_view_issue($_GET['issueid'])) {
	$sql = "SELECT * FROM issues WHERE issueid='".$_GET['issueid']."'";
	$issue = $_ENV['dbi']->fetch_row($sql,"array");
	$issue['problem'] = htmlspecialchars($issue['problem']);
	$_ENV['tpl']->assign('issue',$issue);

	$sql = "SELECT requester FROM issue_requesters WHERE issueid='{$_GET['issueid']}'";
	$requester = $_ENV['dbi']->fetch_one($sql);
	if (!empty($requester)) {
		$_ENV['tpl']->assign('requester',$requester);
	}
	$groups = issue_groups($_GET['issueid']);
	$_ENV['tpl']->assign('groups',$groups);

	$ugroups = array();
	foreach ($groups as $group) {
		if (in_array($group['gid'],$_SESSION['groups'])) {
			array_push($ugroups,$group);
		}
	}
	$_ENV['tpl']->assign('ugroups',$ugroups);
	if (count($ugroups) > 1 and is_null($_GET['gid'])) {
		Module::template('issues','choose_view_group.tpl');
	} else {
		update_view_tracking($_GET['issueid']);
		if (empty($_GET['gid'])) {
			if (count($ugroups) < 1) {
				$group = $groups[0]['gid'];
			} else {
				$group = $ugroups[0]['gid'];
			}
		} else {
			$group = $_GET['gid'];
		}
		if (issue_priv($_GET['issueid'],'technician')) {
			$links[] = array(
				'txt' => 'Email Issue',
				'url' => '?module=issues&action=email&issueid='.$_GET['issueid']."&gid=$group",
				'img' => $_ENV['imgs']['email']
			);
		}
		if (closed($_GET['issueid'])) {
			$links[] = array(
				'txt' => 'Reopen Issue',
				'url' => '?module=issues&action=view&issueid='.$_GET['issueid'].'&reopen=true',
				'img' => $_ENV['imgs']['show_closed']
			);
		}
		if (show_issue($_GET['issueid'],$group) and permission_check('technician',$group)
		and $group != $issue['gid']) {
			$links[] = array(
				'txt' => 'De-Escalate Issue',
				'url' => '?module=issues&action=view&deescalate=true&issueid='.$_GET['issueid'].'&gid='.$_GET['gid'],
				'img' => $_ENV['imgs']['deescalate']
			);
		}
		$links[] = array(
			'txt' => !is_subscribed($_SESSION['userid'],$_GET['issueid']) ? 'Subscribe' : 'Unsubscribe',
			'url' => "?module=issues&action=view&issueid=".$_GET['issueid']."&gid=$group&toggle_subscribe=true",
			'img' => !is_subscribed($_SESSION['userid'],$_GET['issueid']) ? $_ENV['imgs']['subscribe'] : $_ENV['imgs']['unsubscribe']
		);
		$links[] = array(
			'txt' => 'Issue Files',
			'url' => '?module=issues&action=files&issueid='.$_GET['issueid'],
			'img' => $_ENV['imgs']['file']
		);
		$links[] = array(
			'txt' => 'Issue Log',
			'url' => '?module=issues&action=view_log&issueid='.$_GET['issueid'],
			'img' => $_ENV['imgs']['issue_log']
		);
		if (issue_priv($_GET['issueid'],'move_issues')) {
			$links[] = array(
				'txt' => 'Move Issue',
				'url' => '?module=issues&action=move_issue&issueid='.$_GET['issueid'],
				'img' => $_ENV['imgs']['move']
			);
		}
		$links[] = array(
			'txt' => 'Copy Issue',
			'url' => '?module=issues&action=new&icopy='.$_GET['issueid'],
			'img' => $_ENV['imgs']['copy']
		);
		$assigned = issue_assigned($_GET['issueid']);
		$_ENV['tpl']->assign('assigned',$assigned);
 
		$sql = "SELECT eid,performed_on,action,userid,duration,fid,private 
				FROM events WHERE issueid='".$_GET['issueid']."' "; 
		$sql .= !issue_priv($_GET['issueid'],"view_private") ? "AND private != 't' " : '';
		$sql .= "ORDER By performed_on ASC";
		$events = $_ENV['dbi']->fetch_all($sql,'array');
		$num_events = count($events);
		for ($x = 0;$x < $num_events;$x++) {
			if (!empty($events[$x]['fid'])) {
				$events[$x]['links'][] = array(
					'img' => $_ENV['imgs']['file'],
					'alt' => 'Download File',
					'url' => '?module=download&fid='.$events[$x]['fid']
				);
			}
			if ($events[$x]['userid'] == $_SESSION['userid']
			or issue_priv($_GET['issueid'],"edit_events")) {
				if (!$disable_edit or ($disable_edit and is_admin($_SESSION['userid']))) {
					$events[$x]['links'][] = array(
						'img' => $_ENV['imgs']['edit'],
						'alt' => 'Edit Event',
						'url' => '?module=issues&action=edit&issueid='.$_GET['issueid'].'&gid='.$issue['gid'].'&eid='.$events[$x]['eid']
					);
				}
			}
			if (Permission::is_employee() and issue_priv($_GET['issueid'],'view_private')) {
				$url = "?module=issues&action=view&issueid=".$_GET['issueid']."&eid=".$events[$x]['eid'];
				$events[$x]['links'][] = array(
					'img' => $events[$x]['private'] == 't' ? $_ENV['imgs']['private'] : $_ENV['imgs']['public'],
					'alt' => $events[$x]['private'] == 't' ? 'Make Event Public' : 'Make Event Private',
					'url' => $events[$x]['private'] == 't' ? $url.'&type=public' : $url.'&type=private'
				);
			}
			$events[$x]['class'] = $events[$x]['private'] == 't' ? 'private' : 'data';
		}
		$_ENV['dbi']->assign('events',$events);
		Module::template('issues','view.tpl');
		Module::template('issues','show_events.tpl');
		if (!closed($_GET['issueid']) and issue_priv($_GET['issueid'],"create_issues")) {
			$statuses = issue_statuses($_GET['issueid']);
			$_ENV['tpl']->assign('group',$group);
			$_ENV['tpl']->assign('statuses',issue_statuses($_GET['issueid']));
			$_ENV['tpl']->assign('istatuses',issue_istatuses($_GET['issueid']));
			$_ENV['tpl']->assign('categories',issue_categories($_GET['issueid']));
			$_ENV['tpl']->assign('products',issue_products($_GET['issueid']));
			$imembers = issue_members($_GET['issueid']);
			$gmembers = group_members($group);
			$_ENV['tpl']->assign('members',$imembers);
			$_ENV['tpl']->assign('gmembers',$gmembers);
			$_ENV['tpl']->assign('notifylist',Permission::is_employee($_SESSION['userid']) ? $imembers : $gmembers);
			$_ENV['tpl']->assign('assigned',issue_assigned($_GET['issueid'],$group));
			$_ENV['tpl']->assign('egroups',escalation_groups($group));
			Module::template('issues','new_event.tpl');
		}
	}
} else {
	push_error('Could not find that issue in the database.');
	redirect('?module=issues');
}
?>
