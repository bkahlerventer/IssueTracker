<?php
Module::check();

/**
* Verifies that a group actually exists with given gid
*
* @param integer $gid ID of group to verify
* @return boolean
*/
function group_exists($gid) {
	$sql = "SELECT gid FROM groups WHERE gid='$gid'";
	$id = $_ENV['dbi']->query($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Get the name of a group from the database and return it
*
* @param integer $gid ID of group to get name of
* @return string
*/
function group_name($gid) {
	$sql = "SELECT name FROM groups WHERE gid='$gid'";
	return stripslashes($_ENV['dbi']->fetch_one($sql));
}

/**
* Add a user to a group
*
* @param integer $userid ID of user to add to group
* @param integer $gid ID of group to add user to
*/
function group_useradd($userid,$gid) {
	# check to make sure this user isn't already in the group
	$sql = "SELECT userid FROM group_users 
			WHERE gid='$gid' AND userid='$userid'";
	$id = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		return;
	}
	$insert['userid']       = $userid;
	$insert['gid']          = $gid;
	$insert['show_group']   = 't';
	$_ENV['dbi']->insert('group_users',$insert);
	$uname  = username($userid,FALSE);
	$gname  = group_name($gid);
	logger("$uname added to $gname.",'users');
}

/**
* Remove a user from a group
*
* @param integer $userid ID of user to remove from group
* @param integer $gid ID of group to remove user from
* @return boolean
*/
function group_userdel($userid,$gid) {
	$uname  = username($userid);
	$gname  = group_name($gid);
	$sql = "DELETE FROM group_users WHERE gid='$gid' AND userid='$userid'";
	$result = $_ENV['dbi']->query($sql);
	if ($_ENV['dbi']->affected_rows($result) == 1) {
		logger("$uname removed from $gname",'users');	
		notify_del($userid,$gid,'E');
		notify_del($userid,$gid,'S');
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
* Retrieve array of uids for group
*
* @param integer $gid ID of group to get members list for
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_members($gid,$ignore = null) {
	# initialize empty array
	$group_users = array();

	# retrieve users that are directly part of group
	$sql = "SELECT userid FROM group_users WHERE gid='$gid'";
	$members = $_ENV['dbi']->fetch_all($sql);

	# get usernames for group members
	$members = join(',',$members);
	$sql = "SELECT userid,username FROM users 
			WHERE userid IN ($mbrs) ORDER BY username";
	$users = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($users as $user) {
		$group_users[$user['userid']] = $user['username'];
	}
	return $group_users;
}

/**
* Add user to default notify list of a group
*
* @param integer $userid ID of user to add to notify list
* @param integer $gid ID of group to add user to
*/
function notify_add($userid,$gid,$type) {
	$type = $type != 'S' ? 'E' : $type;
	$sql = "SELECT userid FROM notifications WHERE userid='$userid' 
			AND gid='$gid' AND type='$type'";
	$id = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		return;
	}
	$insert['userid'] = $userid;
  	$insert['gid']    = $gid;
    $insert['type']   = $type;
	$_ENV['dbi']->insert('notifications',$insert);
  	unset($insert);
	$uname = username($userid,FALSE);
	$gname = group_name($gid);
	logger("Adding $uname to $type notification list of $gname.",'users');
}

/**
* Remove user from default notify list of a group
*
* @param integer $userid ID of user to remove from notify list
* @param integer $gid ID of group to remove from
* @return boolean
*/
function notify_del($userid,$gid,$type) {
	$sql = "DELETE FROM notifications WHERE userid='$userid' 
			AND gid='$gid' AND type='$type'";
	$result = $_ENV['dbi']->query($sql);
	if ($_ENV['dbi']->affected_rows($result) == 1) {
		$uname = username($userid,FALSE);
		$gname = group_name($gid);
		logger("Removing $uname from default notify list of $gname.","users");
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
* Retrive list of users receiving notifications for a group
*
* @param integer $gid ID of group to check
* @param string $type Type of notifcations 'E' = Email, 'S' = SMS
* @return array
*/
function notify_list($gid,$type = 'E') {
	$notify = array();
	$sql = "SELECT userid FROM notifications 
			WHERE gid='$gid' AND type='$type'";
	$notify = $_ENV['dbi']->fetch_all($sql);
	return $notify;
}

/**
* Determine if a group is active
*
* @param integer $gid ID of group to check
* @return boolean
*/
function group_active($gid) {
	$sql = "SELECT active FROM groups WHERE AND gid='$gid'";
	$active = $_ENV['dbi']->fetch_one($sql);
	if ($active == 't') {
		return TRUE;
	}
	return FALSE;
}

/**
* Determine if we should show the group to the user or not
*
* @param integer $gid ID of group
* @return boolean
*/
function show_group($gid) {
	$sql = "SELECT show_group FROM group_users 
			WHERE gid='$gid' AND userid='".$_SESSION['userid']."'";
	$show = $_ENV['dbi']->fetch_one($sql);
	if ($show == 'f') {
		return FALSE;
	}
	return TRUE;
}

/**
* Retrieve array of escalation groups for a group
*
* @param integer $gid ID of group
* @return array
*/
function escalation_groups($gid) {
	$egroups = array();
	$sql = "SELECT g.name,e.egid FROM groups g, escalation_points e 
			WHERE e.gid='$gid' AND e.egid=g.gid";
	$groups = $_ENV['dbi']->fetch_all($sql,'array');
	if (!is_null($groups)) {
		foreach ($groups as $group) {
			$egroups[$group['egid']] = $group['name'];
		}
	}
	return $egroups;
}

/**
* Retrieve array of statuses for a group
*
* @param integer $gid ID of group
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_statuses($gid,$ignore = null) {
	$allowed = implode(",",fetch_status(array(
		TYPE_WAITING,TYPE_LONG_TERM,TYPE_CLOSED
	)));
	$sql = "SELECT g.sid,s.status FROM group_statuses g, statuses s 
			WHERE g.gid='$gid' AND g.sid = s.sid AND s.sid IN ($allowed) 
			ORDER BY s.status";
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$statuses[$row['sid']] = $row['status'];
	}
	return $statuses;
}

/**
* Retrieve array of internal statuses for a group
*
* @param integer $gid ID of group
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_istatuses($gid,$ignore = null) {
	$sql = "SELECT g.sid,s.status FROM group_istatuses g, statuses s 
			WHERE g.gid='$gid' AND g.sid = s.sid ORDER BY s.status";
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$statuses[$row['sid']] = $row['status'];
	}
	return $statuses;
}

/**
* Retrieve array of categories for a group
*
* @param integer $gid ID of group
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_categories($gid,$ignore = null) {
	$sql = "SELECT g.cid,c.category FROM group_categories g, categories c 
			WHERE g.gid='$gid' AND g.cid = c.cid ORDER BY c.category";
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$categories[$row['cid']] = $row['category'];
	}
	return $categories;
}

/**
* Retrieve array of products for a group
*
* @param integer $gid ID of group
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_products($gid,$ignore = null) {
	$sql = "SELECT p.pid,p.product FROM group_products g, products p 
			WHERE p.pid=g.pid AND g.gid='$gid'";
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$products[$row['pid']] = $row['product'];
	}
	return $products;
}

/**
* Determine if a user is a member of given group
*
* @param integer $gid ID of group
* @param integer $userid ID of user
* @return boolean
*/
function user_in_group($gid,$userid = null) {
	$userid = empty($userid) ? $_SESSION['userid'] : $userid;
	$members = group_members($gid);
	if (is_array($members)) {
		if (array_key_exists($userid,$members)) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
* Retrieve array of issues that should be viewable by given group
*
* @param integer $gid ID of group
* @param integer $limit Number of issues to return
* @param boolean $show_registered Whether or not to show registered issues
* @param string $ignore INTERNAL FUNCTION USE ONLY
* @return array
*/
function group_issues($gid,$limit = null,$show_registered = TRUE,$ignore = null) {
	$_GET['sort'] = empty($_GET['sort']) ? $_SESSION['prefs']['sort_by'] : $_GET['sort'];
	$issues = array();
	list($registered) = fetch_status(TYPE_REGISTERED);
	$closed = implode(",",fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));

	$sql = "SELECT i.issueid,i.summary,i.private";
	foreach ($_SESSION['prefs']['show_fields'] as $val) {
		switch ($val) {
			case 'flags':
				continue;
				break;
			case 'assigned_to':
				$sql .= ',g.assigned_to';
				break;
			case 'opened_by':
				$sql .= ',u.username';
				break;
			default:
				$sql .= ",i.$val";
				break;
		}
	}
	$sql .= " FROM issues i, issue_groups g";
	$sql .= in_array('opened_by',$_SESSION['prefs']['show_fields']) ? ',users u' : '';
	if (preg_match('/,/',$gid)) {
		$sql .= " WHERE g.gid IN ($gid) ";
	} else {
		$sql .= " WHERE g.gid = '$gid' ";
	}
	$sql .= "AND i.issueid = g.issueid ";
	$sql .= in_array("opened_by",$_SESSION['prefs']['show_fields']) ? "AND u.userid = i.opened_by " : "";
	$sql .= !permission_check("view_private",$gid) ? "and i.private != 't' " : "";
	$sql .= !$show_registered ? "AND i.status != '$registered' " : "";
	$sql .= $_GET['showall'] != "true" ? "AND i.status NOT IN ($closed) " : "";
	if ($_GET['sort'] == 'assigned_to') {
		$sql .= 'ORDER BY g.assigned_to ';
	} else if ($_GET['sort'] == 'opened_by') {
		$sql .= 'ORDER BY u.username ';
	} else {
		$sql .= "ORDER BY i.".$_GET['sort']." ";
	}
	if ($_GET['sort'] == 'severity') {
		$sql .= $_GET['reverse'] == 'true' ? 'DESC' : 'ASC';
	} else {
		$sql .= $_GET['reverse'] == 'true' ? 'ASC' : 'DESC';
	}
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$issues[$row['issueid']] = $row;
	}
	return $issues;
}

/**
* Determine if a group has reached their limit
*
* @param integer $gid ID of group
* @param string $type Only match given limit type
* @return boolean
*/
function group_over_limit($gid,$type = null) {
	if (empty($gid)) {
		return FALSE;
	}

	$sql = "SELECT group_type,bought,start_date,end_date FROM groups WHERE gid='$gid'";
	$data = $_ENV['dbi']->fetch_row($sql,'array');
	if (!is_null($data)) {
		extract($data);
		if (!is_null($type) and $group_type != $type) {
			return FALSE;
		}
		if ($group_type == 'hours') {
			$sql = "SELECT issueid FROM issue_groups WHERE gid='$gid'";
			$issues = $_ENV['dbi']->fetch_all($sql);
			if (!is_null($issues)) {
				$issues = implode(',',$issues);
				$sql = "SELECT SUM(duration) FROM events WHERE issueid IN ($issues) ";
				if (!empty($start_date) and !empty($end_date)) {
					$sql .= "AND performed_on >= $start_date ";
				}
				$hours = $_ENV['dbi']->fetch_one($sql);
				if (!is_null($hours)) {
					if ($hours >= $bought) {
						return TRUE;
					}
				}
			}
		} else if ($group_type == 'issues') {
			$sql = "SELECT COUNT(issueid) FROM issues WHERE gid='$gid'";
			if (!empty($start_date) and !empty($end_date)) {
				$sql .= "AND opened >= $start_date ";
			}
			$issues = $_ENV['dbi']->fetch_one($sql);
			if (!is_null($issues)) {
				if ($issues >= $bought) {
					return TRUE;
				}
			}
		}
	}
	return FALSE;
}

/**
* Retrieve array of the public email address for group(s)
*
* @param integer $groupId ID of group
* @return array
*/
function public_address($groupId = null) {
	$address = array();
  	$sql = "SELECT gid,LOWER(email) AS email FROM groups WHERE email != '' ";
	$sql .= !is_null($groupId) ? "AND gid='{$groupId}' " : "";
	$groups = $_ENV['dbi']->fetch_all($sql,'array');
	if (is_array($groups)) {
		if (!is_null($groupId) and $groups[0]['gid'] == $groupId) {
			return $groups[0]['email'];
		} else {
			foreach ($groups as $group) {
				$address[$group['gid']] = $group['email'];
			}
		}
	}
	return $address;
}
?>
