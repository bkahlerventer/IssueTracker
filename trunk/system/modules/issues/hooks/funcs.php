<?php
Module::check();
/**
* Toggle a user's subscription to an issue
*
* @param integer $issueid ID of issue
*/
function toggle_subscribe($issueid) {
	if (!is_subscribed($_SESSION['userid'],$issueid)) {
		$insert['issueid']= $issueid;
		$insert['userid']	= $_SESSION['userid'];
		$_ENV['dbi']->insert('subscriptions',$insert);
		unset($insert);
	} else {
		$sql = "DELETE FROM subscriptions WHERE issueid='$issueid' 
				AND userid='".$_SESSION['userid']."'";
		$_ENV['dbi']->query($sql);
	}
}

/**
* Check to see if a user is already subscribed to an issue
*
* @param integer $userid ID of user
* @param integer $issueid ID of issue
* @return boolean
*/
function is_subscribed($userid,$issueid) {
	$sql = "SELECT issueid FROM subscriptions WHERE userid='$userid' 
			AND issueid='$issueid'";
	$id = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Retrieve the groups that are allowed to see an issue
*
* @param integer $issueid ID of issue
* @return array
*/
function issue_groups($issueid) {
	$sql = "SELECT i.gid,g.name FROM issue_groups i, groups g 
			WHERE i.issueid='$issueid' AND i.gid=g.gid 
			AND i.show_issue='t' ORDER BY g.name";
	return $_ENV['dbi']->fetch_all($sql,'array');
}

/**
* Determine if a user can see an issue
*
* @param integer $issueid ID of issue
* @param integer $userid ID of user
* @return boolean
*/
function can_view_issue($issueid,$userid = null) {
	if (Permission::is_manager()) {
		return TRUE;
	}
	$user_groups = is_null($userid) ? $_SESSION['groups'] : user_groups($userid);
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		if (in_array($group['gid'],$user_groups)) {
			return TRUE;
		}
	}
}

/**
* Determine if a user has a privilege on a issue
*
* @param integer $issueid ID of issue
* @param string $perm Privilege to check against
* @param integer $userid ID of user to check *BASTARDS*
* @return boolean
*/
function issue_priv($issueid,$perm,$userid = null) {
	$userid = empty($userid) ? $_SESSION['userid'] : $userid;
	if (Permission::is_manager($userid)) {
		return TRUE;
	}
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		if (Permission::check($perm,$group['gid'],$userid)) {
			return TRUE;	
		}
	}
	return FALSE;
}

/**
* Retrive user assigned to a issue for a group
*
* @param integer $issueid
* @param integer $gid
* @return integer
*/
function issue_assigned($issueid,$gid = null) {
	if (!is_null($gid)) {
		$sql = "SELECT assigned_to FROM issue_groups 
				WHERE issueid='$issueid' AND gid='$gid' ";
		$userid = $_ENV['dbi']->fetch_one($sql);
		return $userid;
	} else {
		$sql = "SELECT gid,assigned_to FROM issue_groups 
				WHERE issueid='$issueid' ";
		$assigned = $_ENV['dbi']->fetch_all($sql,'array');
		if (!is_null($assigned)) {
			foreach ($assigned as $group) {
				$users[$group['gid']] = $group['assigned_to'];
			}
		}
		return $users;
	}
}

/**
* Pull number of new issues for a group
*
* @param integer $gid ID of group to check
* @return integer
*/
function num_new_issues($gid) {
	list($registered) = fetch_status(TYPE_REGISTERED);
	$sql = "SELECT COUNT(i.issueid) FROM issues i, issue_groups g 
			WHERE i.issueid=g.issueid AND g.gid='$gid' ";
	$sql .= !Permission::check('view_private',$gid) ? " AND i.private !='t'" : '';
	$sql .= "AND i.status='$registered'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Pull number of open issues for this group
*
* @param integer $gid ID of group to check
* @return integer
*/
function num_open_issues($gid) {
	$closed = implode(",",fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));
	$sql = "SELECT COUNT(i.issueid) FROM issues i, issue_groups g 
			WHERE i.issueid=g.issueid AND g.gid='$gid' ";
	$sql .= !Permission::check('view_private',$gid) ? " AND i.private !='t'" : '';
	$sql .= "AND i.status NOT IN ($closed) AND g.show_issue='t'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Find out when last action was for this group
*
* @param integer $gid ID of group
* @return array
*/
function last_activity($gid) {
	$sql = "SELECT e.performed_on,e.userid FROM issues i, issue_groups g, events e 
			WHERE i.issueid=g.issueid AND i.issueid=e.issueid AND g.gid='$gid' 
			ORDER BY e.performed_on DESC LIMIT 1";
	$data = $_ENV['dbi']->fetch_row($sql,'array');
	if (!is_null($data)) {
		$last = array(
			'date' => date_format($data['performed_on']),
			'user' => username($data['userid'])
		);
		return $last;
	}
	return null;
}

/**
* Determines if a issue it closed or not
*
* @param integer $issueid ID of issue to check
* @return boolean
*/
function closed($issueid) {
	$closed = fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED));
	$sql = "SELECT status FROM issues WHERE issueid='$issueid'";
	$status = $_ENV['dbi']->fetch_one($sql);
	if (in_array($status,$closed)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Get the uid of the user that opened issue
*
* @param integer $issueid ID of issue
* @return integer
*/
function owner($issueid) {
	$sql = "SELECT opened_by FROM issues WHERE issueid='$issueid'";
	return $_ENV['dbi']->fetch_one($sql);
}
  
/**
* Retrieve array of all users in issue groups
*
* @param integer $issueid ID of issue
* @return array
*/ 
function issue_members($issueid) {
	$groups = issue_groups($issueid);
	$issue_members = array();
	foreach ($groups as $group) {
		$members = group_members($group['gid']);
		foreach ($members as $key => $val) {
			if (array_key_exists($key,$issue_members)) {
				continue;
			}
			$issue_members[$key] = $val;
		}
	}
	return $issue_members;
}

/**
* Get the modification time of an event
*
* @param integer $eid ID of event
* @return array
*/
function event_modify_time($eid) {
	$sql = "SELECT modified,userid FROM event_modifications 
			WHERE eid='$eid' ORDER BY modified DESC LIMIT 1";
	$data = $_ENV['dbi']->fetch_row($sql,'array');
	if (!is_null($data)) {
		$modify = array(
			'time' => date_format($data['modified']),
			'user' => username($data['userid'])
		);
		return $modify;
	}
	return null;
}

/**
* Retrive the summary of an issue
*
* @param integer $issueid ID of issue to get summary for
* @return string
*/
function issue_summary($issueid) {
	$sql = "SELECT summary FROM issues WHERE issueid='$issueid'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Retrieve all escalation groups for a issue
*
* @param integer $issueid ID of issue
* @return array
*/
function issue_escalation_groups($issueid) {
	$egroups = array();
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		$e = escalation_groups($group['gid']);
		foreach ($e as $gid => $name) {
			if (!in_array($gid,$egroups)) {
				$egroups[$gid] = $name;
			}
		}
	}
	return $egroups;
}

/**
* Retrieve array of all statuses for a issue
* 
* @param integer $issueid ID of issue
* @return array
*/
function issue_statuses($issueid) {
	$statuses = array();
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		$gstatuses = group_statuses($group['gid']);
		foreach ($gstatuses as $key => $val) {
			if (!array_key_exists($key,$statuses)) {
				$statuses[$key] = $val;
			}
		}
	}
	return $statuses;
}

/**
* Retrieve array of all internal statuses for a issue
* 
* @param integer $issueid ID of issue
* @return array
*/
function issue_istatuses($issueid) {
	$statuses = array();
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		$gstatuses = group_istatuses($group['gid']);
		foreach ($gstatuses as $key => $val) {
			if (!array_key_exists($key,$statuses)) {
				$statuses[$key] = $val;
			}
		}
	}
	return $statuses;
}

/**
* Retrieve array of all categories for a issue
* 
* @param integer $issueid ID of issue
* @return array
*/
function issue_categories($issueid) {
	$categories = array();
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		$gcategories = group_categories($group['gid']);
		foreach ($gcategories as $key => $val) {
			if (!array_key_exists($key,$categories)) {
				$categories[$key] = $val;
			}
		}
	}
	return $categories;
}

/**
* Retrieve array of all products for a issue
* 
* @param integer $issueid ID of issue
* @return array
*/
function issue_products($issueid) {
	$products = array();
	$groups = issue_groups($issueid);
	foreach ($groups as $group) {
		$gproducts = group_products($group['gid']);
		foreach ($gproducts as $key => $val) {
			if (!array_key_exists($key,$products)) {
				$products[$key] = $val;
			}
		}
	}
	return $products;
}

/**
* Send out notifcations for a issue
*
* @param integer $issueid ID of issue
* @param array $users Array of users to notify
* @param boolean $new_event Whether or not a new event was posted
*/
function issue_notify($issueid,$users = array(),$new_event = TRUE) {
	if (!issue_exists($issueid)) {
		return;
	}
	include_once(_CLASSES_."mail.class.php");
	$mailer = new MAILER();

	# Retrieve the groups in this issue
	$groups = issue_groups($issueid);
	$gcount = count($groups);

	# Statuses
	list($registered) = fetch_status(TYPE_REGISTERED);
	$closed = fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED));

	if ($new_event) {
		$sql = "SELECT action,private,userid FROM events 
				WHERE issueid='$issueid' ORDER BY eid DESC LIMIT 1";
		$event = $_ENV['dbi']->fetch_row($sql,'array');
		if (is_array($event)) {
			$event['action'] = stripslashes($event['action']);
		}
	}

	$sql = "SELECT private,status,severity,summary,problem,opened_by,opened,modified 
			FROM issues WHERE issueid='$issueid'";
	$issue = $_ENV['dbi']->fetch_row($sql,'array');
	$issue['summary'] = trim(stripslashes($issue['summary']));
	$issue['problem'] = trim(stripslashes($issue['problem']));
	if (in_array($issue['status'],fetch_status(TYPE_CLOSED))) {
		$subject = "(CLOSED) Issue #$issueid {$issue['summary']}";
	} else if ($issue['status'] == $registered 
	and $issue['opened'] == $issue['modified']) {
		$subject = "(NEW) Issue #$issueid {$issue['summary']}";
	} else {
		$subject = "(UPDATED) Issue #$issueid {$issue['summary']}";
	}

	if (!is_array($event)) {
		$message  = "The details of this issue have been modified.  ";
		$message .= "Please login and view the issue log for more information.\n";
	} else {
		if (in_array($issue['status'],fetch_status(TYPE_CLOSED))) {
			$message = "Closed issue $issueid by ".username($event['userid'])."\n";
		} else if ($issue['status'] != $registered
		or $issue['opened'] != $issue['modified']) {
			$message  = "Update to issue $issueid by ".username($event['userid'])."\n";
			$message .= "Action:\n{$event['action']}\n";
		} else {
			$message = "Issue Created by ".username($issue['opened_by'])."\n";
		}
	}
	if ($issue['status'] != $registered) {
		$sql = "SELECT requester,list FROM issue_requesters WHERE issueid='$issueid'";
		list($requester,$list) = $_ENV['dbi']->fetch_row($sql);
		if (!empty($requester)) {
			issue_log($issueid,"Notifying requester ($requester)");
			$msg .= $message."\n";
			$msg .= "To respond to this issue just reply to this email leaving the subject intact.\n";
			$mailer->set("email_from",$list);
			$mailer->subject($subject);
			$mailer->to($requester);
			$mailer->message($msg);
			$mailer->send();
		}
	} else {
		$message .= "\n\nProblem:\n{$issue['problem']}\n";
	}

	$message .= "\n\n"._URL_."?module=issues&action=view&issueid=$issueid";
	$userlist = array();
	foreach ($groups as $group) {
		if (show_issue($issueid,$group['gid'])) {
			$sql = "SELECT userid,type FROM notifications WHERE gid='{$group['gid']}'";
			$notify = $_ENV['dbi']->fetch_all($sql,'array');
			if (!is_null($notify)) {
				foreach ($notify as $row) {
					$userlist[] = array(
						'userid' => $row['userid'],
						'type' => $row['type']
					);
				}
			}
			$assigned = issue_assigned($issueid,$group['gid']);
			if (!in_array($assigned,$userlist)) {
				$userlist[] = array(
					'userid' => $assigned,
					'type' => 'E'
				);
			}
			$sql = "SELECT severity FROM issues WHERE issueid='{$issueid}'";
			$sev = $_ENV['dbi']->fetch_one($sql);
			if (!empty($sev)) {
				$sql = "SELECT userid FROM group_users WHERE severity <= '{$sev}' 
						AND gid='{$group['gid']}'";
				$sev_notify = $_ENV['dbi']->fetch_all($sql);
				if (is_array($sev_notify)) {
					foreach ($sev_notify as $userid) {
						$userlist[] = array(
							'userid' => $userid,
							'type' => 'S'
						);
					}
				}
			}
		}
	}
		
	$sql = "SELECT userid FROM subscriptions WHERE issueid='$issueid'";
	$subscriptions = $_ENV['dbi']->fetch_all($sql);
	if (!is_null($subscriptions)) {
		foreach ($subscriptions as $userid) {
			$userlist[] = array(
				'userid' => $userid,
				'type' => 'E'
			);
		}
	}

	$owner = owner($issueid);
	if (!in_array($owner,$userlist)) {
		$userlist[] = array(
			'userid' => $owner,
			'type' => 'E'
		);
	}

	if (is_array($users)) {
		foreach ($users as $userid) {
			$userlist[] = array(
				'userid' => $userid,
				'type' => 'E'
			);
		}
	}

	$sent = array();
	$sql = "SELECT COUNT(eid) FROM events WHERE issueid='$issueid'";
	$count = $_ENV['dbi']->fetch_one($sql);
	if ($count > 2) {
		$sql = "SELECT userid,action,performed_on FROM events 
				WHERE issueid='$issueid' AND private != 't' 
				ORDER BY eid DESC LIMIT 2 OFFSET 1";
		$events = $_ENV['dbi']->fetch_all($sql,'array');
		if (!is_null($events)) {
			$message .= "\n\nPrevious Events:\n----------------\n";
			foreach ($events as $data) {
				$date = date_format($data['performed_on']);
				$message .= "Posted $date by ".username($data['userid'])."\n";
				$message .= stripslashes($data['action'])."\n\n";
			}
		}
	}

	$org_subject = $subject;
	foreach ($userlist as $user) {
		if (empty($user['userid'])) {
			continue;
		}
		$email = eregi('S',$user['type']) ? sms($user['userid']) : email($user['userid']);
		if (in_array($email,$sent) or empty($email)) {
			continue;
		}
		if (!user_active($user['userid'])) {
			continue;
		}
		if (($event['private'] == "t" or $issue['private'] == 't')
		and !issue_priv($issueid,'view_private',$user['userid'])) {
			continue;
		}
		$mailer->add_header('X-Issue-Tracker-Severity: '.$issue['severity']);
		if (is_array($users)) {
			if (@in_array($user['userid'],$users)) {
				$mailer->add_header('X-Issue-Tracker-Selected: true');
			}
		}
		if ($user['userid'] == $owner) {
			$mailer->add_header('X-Issue-Tracker-Owner: true');
		}
		$new_subject  = '';
		$user_groups = user_groups($user['userid']);
		reset($groups);
		foreach ($groups as $group) {
			if (in_array($group['gid'],$user_groups)) {
				$mailer->add_header('X-Issue-Tracker-Group: '.$group['name']);
				$new_subject .= empty($new_subject) ? '('.$group['name'] : '/'.$ggroup['name'];
			}
			if ($user['userid'] == issue_assigned($issueid,$group['gid'])) {
				$mailer->add_header('X-Issue-Tracker-Assigned: true');
			}
		}
  
		$new_subject .= !empty($new_subject) ? ')' : '';
		$subject = $new_subject.$org_subject;
		$message = stripslashes($message);
		$mailer->set('email_from',_EMAIL_);
		$mailer->subject($subject);
		$mailer->to($email);
		$mailer->message($message);
		$mailer->send();
		array_push($sent,$email);
	}
	$maillist = implode(",",$sent);
	issue_log($issueid,'Notifications sent to '.$maillist);
}

/**
* Determine if a issue should be shown in a group
*
* @param integer $issueid ID of issue
* @param integer $gid ID of group
* @return boolean
*/
function show_issue($issueid,$gid) {
	$sql = "SELECT show_issue FROM issue_groups 
			WHERE issueid='$issueid' AND gid='$gid'";
	$show = $_ENV['dbi']->fetch_one($sql);
	if ($show == 't') {
		return TRUE;
	}
	return FALSE;
}

/**
* Reopen an issue
*
* @param integer $issueid Id of issue to reopen
*/
function reopen_issue($issueid) {
	$issue_groups = issue_groups($issueid);
	$owner = owner($issueid);
	foreach ($issue_groups as $group) {
		if ($_SESSION['userid'] == $owner or (in_array($group['gid'],$_SESSION['groups'])
		and Permission::check('technician',$group['gid']))) {
			$tech = TRUE;
			break;
		}
	}
	if (closed($issueid) and $tech) {
		list($registered) = fetch_status(TYPE_REGISTERED);
		$update['status'] = $registered;
		$update['modified'] = time();
		$_ENV['dbi']->update('issues',$update,"WHERE issueid='$issueid'");
	}
	issue_log($issueid,'Issue Reopened');
	return;
}

/**
* Escalate an issue
*
* @param integer $issueid Ticket to escalate
* @param integer $gid Group to escalate to
*/
function escalate_issue($issueid,$gid) {
	$sql = "SELECT show_issue FROM issue_groups 
			WHERE issueid='$issueid' AND gid='$gid'";
	$show = $_ENV['dbi']->fetch_one($sql);
	if (!is_null($show)) {
		if ($show == 'f') {
			$update['show_issue'] = 't';
			$_ENV['dbi']->update('issue_groups',$update,"WHERE issueid='$issueid' AND gid='$gid'");
		}
	} else {
		$insert['issueid'] = $issueid;
		$insert['gid'] = $gid;
		$insert['opened'] = time();
		$_ENV['dbi']->insert('issue_groups',$insert);
		issue_log($issueid,'Issue escalated to '.group_name($gid),TRUE);
	}
}

/**
* Deescalate an issue from a group
* 
* @param integer $issueid Id of issue
* @param integer $gid Id of Group
*/
function deescalate_issue($issueid,$gid) {
	$update['show_issue'] = 'f';
	$_ENV['dbi']->update('issue_groups',$update,"WHERE issueid='$issueid' AND gid='$gid'");
 	unset($update);

	$insert['action'] = group_name($gid).' has de-escalated the issue.';
	$insert['private'] = 't';
	$insert['userid'] = $_SESSION['userid'];
	$insert['performed_on'] = time();
	$insert['issueid'] = $issueid;
	$_ENV['dbi']->insert('events',$insert);
	$update['modified']     = time();
	$_ENV['dbi']->update('issues',$update,"WHERE issueid='$issueid'");
	issue_log($issueid,'De-escalated from '.group_name($gid),TRUE);
}

/**
* Adds an entry to the logs for an issue
*
* @param integer $issueid ID of issue
* @param string $msg Message to log
* @param integer $userid ID of user
*/
function issue_log($issueid,$msg,$private = null,$userid = null) {
	if (empty($msg) or empty($issueid)) {
		return;
	}
	if (empty($userid) and empty($_SESSION['userid'])) {
		$userid = _PARSER_;
	}
	$insert['issueid'] = $issueid;
	$insert['logged'] = time();
	$insert['userid'] = !is_null($userid) ? $userid : $_SESSION['userid'];
	$insert['message'] = $msg;
	$insert['private'] = $private === TRUE ? 't' : 'f';
	$_ENV['dbi']->insert('issue_log',$insert);
}

/**
* Update the viewed time on an issue for the current user
*
* @param integer $issueid ID of issue
*/
function update_view_tracking($issueid) {
	$sql = "SELECT viewed FROM view_tracking WHERE issueid='".$_GET['issueid']."' 
			AND userid='".$_SESSION['userid']."'";
	$viewed = $_ENV['dbi']->fetch_one($sql);
	if (!is_null($viewed)) {
		$where = "WHERE issueid='$issueid' AND userid='".$_SESSION['userid']."'";
		$update['viewed'] = time();
		$_ENV['dbi']->update('view_tracking',$update,$where);
	} else {
		$insert['issueid'] = $issueid;
		$insert['userid'] = $_SESSION['userid'];
		$insert['viewed'] = time();
		$_ENV['dbi']->insert('view_tracking',$insert);
	}
}

/**
* Retrieve the number of events in an issue that given users has not seen
*
* @param integer $issueid ID of issue
* @param integer $userid ID of user
* @param boolean $private Count private or not
* @return integer
*/
function unread_events($issueid,$userid,$private = FALSE) {
	$sql = "SELECT viewed FROM view_tracking WHERE issueid='$issueid' 
			AND userid='$userid'";
	$viewed = $_ENV['dbi']->fetch_one($sql);
	if (is_null($viewed)) {
		$viewed = 0;
	}
	$sql = "SELECT COUNT(eid) FROM events WHERE issueid='$issueid' 
			AND performed_on > $viewed ";
	$sql .= !$private ? "AND private != 't' " : "";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Checks to see if an issue exists
*
* @param integer $issueid Id of issue
* @return boolean
*/
function issue_exists($issueid) {
	$sql = "SELECT issueid FROM issues WHERE issueid='$issueid'";
	$id = $_ENV['dbi']->fetch_one($sql);
	if (!preg_match('/^[0-9]+$/',$id)) {
		return TRUE;
	}
	return FALSE;
}

/** 
* Determines if given issue was escalated to this group
*
* @param integer $issueid ID of issue
* @param integer $gid ID of group
* @return boolean
*/
function issue_escalated_to($issueid,$gid) {
	$sql = "SELECT gid FROM issues WHERE issueid='$issueid'";
	$igid = $_ENV['dbi']->fetch_one($sql);
	if (!empty($igid)) {
		if ($gid != $igid) {
			return TRUE;
		}
	}
	return FALSE;
}

/** 
* Determines if given issue was escalated from this group
*
* @param integer $issueid ID of issue
* @param integer $gid ID of group
* @return boolean
*/
function issue_escalated_from($issueid,$gid) {
	$sql = "SELECT gid FROM issues WHERE issueid='$issueid' ";
	$igid = $_ENV['dbi']->fetch_one($sql);
	if (!empty($igid)) {
		if ($gid == $igid) {
			$sql = "SELECT COUNT(gid) FROM issue_groups WHERE issueid='$issueid'";
			$count = $_ENV['dbi']->fetch_one($sql);
			if ($count > 1) {
				return TRUE;
			}
		}
	}
	return FALSE;
}
?>
