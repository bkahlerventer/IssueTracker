<?php
/**
* Class for management and verification of permissions
*
* @author Edwin Robertson <tm@tuxmonkey.com>
* @version 1.0
*/
class Permission {
	/**
	* Determine if a user is an admin
	*
	* @param integer $userid
	* @returns boolean
	*/
	function is_admin($userid = null) {
		$userid = empty($userid) ? $_SESSION['userid'] : $userid;
		# Form query to get admin
		$sql = "SELECT admin FROM users WHERE userid='$userid'";
		# Fetch the result and cache it for 30 seconds
		$admin = $_ENV['dbi']->fetch_one($sql,30);
		$retval = $admin == 't' ? TRUE : FALSE;
		return $retval;
	}

	/**
	* Determines whether or not the user is an employee
	*
	* @param integer $userid ID of user to check
	* @returns boolean
	*/
	function is_employee($userid = null) {
		$userid = empty($userid) ? $_SESSION['userid'] : $userid;
		if (Permission::is_admin($userid)) {
			return TRUE;
		}
		$sql = "SELECT userid FROM group_users 
				WHERE userid='$userid' AND gid='"._EMPLOYEES_."'";
		# Fetch the result and cache it for 30 seconds
		$emp = $_ENV['dbi']->fetch_one($sql,30);
		$retval = preg_match('/^[0-9]+$/',$emp) ? TRUE : FALSE;
		return $retval;
	}

	/**
	* Retrieve the permission set for a given user in a given group
	*
	* @param integer $gid ID of group to look in
	* @param integer $userid ID of user to get permission set for
	* @returns array
	*/
	function retrieve_set($gid,$userid = null) {
		$pset = array();
		$userid = empty($userid) ? $_SESSION['userid'] : $userid;
		$sql = "SELECT p.permissions,g.perm_set FROM group_users g, permission_sets p 
				WHERE g.userid='$userid' AND g.gid='$gid' AND g.perm_set = p.permsetid";
		# Fetch the result and cache it for 60 seconds
		$result = $_ENV['dbi']->query($sql,60);
		if ($_ENV['dbi']->num_rows($result) > 0) {
			list($pset,$id) = $_ENV['dbi']->fetch($result);
			$pset = explode(',',stripslashes($pset));
			if (!is_array($pset)) {
				logger('Permission set '.$id.' returns none arrary.','permissions');
				return;
			}
		}
		return $pset;
	}

	/**
	* Retrieve the permission set id for a given user in a given group
	*
	* @param integer $gid ID of group to look in
	* @param integer $userid ID of user to get permission set for
	* @return integer
	*/
	function permission_set_id($gid,$userid = null) {
		$userid = empty($userid) ? $_SESSION['userid'] : $userid;
		$sql = "SELECT perm_set FROM group_users 
				WHERE userid='$userid' AND gid='$gid'";
		return $_ENV['dbi']->fetch_one($sql);
	}

	/**
	* Retrieve the text of a permission
	*
	* @param integer $permid ID of permission
	*/
	function permission($permid) {
		$sql = "SELECT permission FROM permissions WHERE permid='$permid'";
		return $_ENV['dbi']->fetch_one($sql);
	}

	/**
	* Retrieve the permission set name for a permission set id
	*
	* @param integer $psetid ID of the permission set
	* @return string
	*/
	function permission_set_name($psetid) {
		$sql = "SELECT name FROM permission_sets WHERE permsetid='$psetid'";
		return $_ENV['dbi']->fetch_one($sql);
	}

	/**
	* Determine if a user has a certain permission
	*
	* @param string $priv Privilege to be checked
	* @param integer $userid ID of user to check
	* @param integer $gid ID of group to check
	* @returns boolean
	*/
	function permission_check($perm,$gid = null,$userid = null) {
		if (empty($userid)) {
			$userid = $_SESSION['userid'];
		}
		if (Permission::is_admin($userid)) {
			return TRUE;
		}
		$sql = "SELECT permid,group_perm,user_perm FROM permissions WHERE permission='$perm'";
		$data = $_ENV['dbi']->fetch_row($sql,'array');
		if ($data['user_perm'] == 't') {
			$sql = "SELECT userid FROM user_permissions 
					WHERE userid='$userid' AND permid='{$data['permid']}'";
			$id = $_ENV['dbi']->fetch_one($sql);
			if (preg_match('/^[0-9]+$/',$id)) {
				return TRUE;
			}
		} elseif ($data['group_perm'] == 't') {
			$user_groups = $userid == $_SESSION['userid'] ? $_SESSION['groups'] : user_groups($userid);
			foreach ($user_groups as $key => $val) {
				$sql = "SELECT permid FROM group_permissions 
						WHERE permid='{$data['permid']}' AND gid='$val'";
				$id = $_ENV['dbi']->fetch_one($sql);
				if (preg_match('/^[0-9]+$/',$id)) {
					return TRUE;
				}
			}
		}
		if (!is_null($gid)) {
			$pset = Permission::retrieve_set($gid,$userid);
			if (in_array($perm,$pset)) {
				return TRUE;
			}
		} else {
			foreach ($_SESSION['groups'] as $key => $val) {
				$pset = Permission::retrieve_set($val,$userid);
				if (in_array($perm,$pset)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	* Attempt to authenticate a user
	*
	* @param string $username Username of user
	* @param string $password Password of user
	* @return boolean|integer
	*/
	function authenticate($username,$password) {
		$sql = "SELECT username,userid FROM users 
				WHERE LOWER(username)=LOWER('".addslashes($username)."') 
				AND password='".md5($password)."' AND active='t'";
		$data = $_ENV['dbi']->fetch_row($sql,'array');
		if (preg_match('/^[0-9]+$/',$data['userid'])) {
			# If we're calling the authentication function from
			# the browser then make sure to define the needed
			# session variables, and then redirect to the user
			if (defined('BROWSER')) {
				$_SESSION['userid'] = $data['userid'];
				$_SESSION['javascript'] = $_POST['javascript'] == 'enabled' ? 't' : 'f';
				$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['prefs'] = user_preferences($_SESSION['userid']);
				$_SESSION['prefs']['show_fields'] = empty($_SESSION['prefs']['show_fields'])
					? array() : explode(',',$_SESSION['prefs']['show_fields']);
				$_SESSION['prefs']['sort_by'] = empty($_SESSION['prefs']['sort_by'])
					? 'issueid' : $_SESSION['prefs']['sort_by'];
				$_SESSION['prefs']['word_wrap'] = !preg_match('/^[0-9+$/',$_SESSION['prefs']['word_wrap'])
					? 80 : $_SESSION['prefs']['word_wrap'];
				logger("$username logged in.",'logins');
				if (!empty($_POST['request'])) {
					redirect('/?'.$_POST['request']);
				} else {
					redirect();	
				}
			} else {
				# If we're not calling authentication from the browser 
				# then just return the retrieved userid
				return $data['userid'];
			}
		} else {
			# If we didnt pull a userid then just return error message
			# for the browser and FALSE if not
			if (defined('BROWSER')) {
				push_error('Invalid login and/or password.');
			} else {
				return FALSE;
			}
		}
	}

	/**
	* Determine if a permission is a group permission
	*
	* @param integer $permid ID of permission
	* @return boolean
	*/
	function group_permission($permid) {
		$sql = "SELECT group_perm FROM permissions WHERE permid='$permid'";
		$gperm = $_ENV['dbi']->fetch_one($sql);
		if ($gperm == 't') {
			return TRUE;
		}
		return FALSE;
	}
}
?>
