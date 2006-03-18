<?php
Module::check();
/**
* Generate password string
*
* @return string
*/
function gen_passwd() {
	return substr(md5(rand(1,1000000)),0,rand(6,12));
}

/**
* Reset a user's password and email the new one to them
*
* @param integer $userid ID of user to reset password for
*/
function reset_passwd($userid) {
	if (!user_exists($userid)) {
		return;
	}
	$pass = gen_passwd();
	$update['password'] = md5($pass);
	$_ENV['dbi']->update('users',$update,"WHERE userid='$userid'");
	unset($update);

	$subject = _COMPANY_." Support Portal Alert";
	$message = "
Your password to the "._COMPANY_." Support Portal has been reset.
Your new password is: $pass

You may login to the interface at "._URL_." and change this password 
at any time.  If you do not know the username that you were given at 
the time of your account creation, you can use your email address as 
a login name with the password that has been sent to you.  If you 
have any questions please contact "._ADMINEMAIL_."

					- "._COMPANY_;
	include_once(_CLASSES_.'mail.class.php');
	if (!is_object($mailer)) {
		$mailer = new MAILER();
		$mailer->set('email_from',_EMAIL_);
	}
	$mailer->subject($subject);
	$mailer->to(email($userid));
	$mailer->message($message);
	$mailer->send();
}
	
/**
* Determine if a user actually exists
*
* @param integer $userid ID of user to look for
* @return boolean
*/
function user_exists($userid) {
	$sql = "SELECT userid FROM users WHERE userid='$userid'";
	$is = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Creates a new user account, returns the new user's userid if successful
* and false if not
*
* @param array $user_data Array containing user data
* @return integer|null
*/
function create_user($user_data) {
	extract($user_data);
	if (empty($username) or empty($email)) {
		return null;
	}
	$failed = FALSE;
	$sql = "SELECT userid FROM users WHERE LOWER(username) = LOWER('$username')";
	list($uid) = $_ENV['dbi']->fetch_one($sql);
	if (!is_null($uid)) {
		push_error('This username is already in use by another user.');
		return null;
	}
	$sql = "SELECT userid FROM users WHERE LOWER(email) = LOWER('$email')";
	list($uid) = $_ENV['dbi']->fetch_one($sql);
	if (!is_null($uid)) {
		push_error('This email address is already in use by another user.');
		return null;
	}
	$password = gen_passwd();
	$insert['username'] = $username;
	$insert['email'] = $email;
	if (!empty($first)) {
		$insert['first_name'] = $first;
	}
	if (!empty($last)) {
		$insert['last_name'] = $last;
	}
	if (!empty($admin)) {
		$insert['admin'] = $admin;
	}
	$insert['password']	= md5($password);
	$insert['active'] = 't';
	$userid = $_ENV['dbi']->insert('users',$insert,'users_userid_seq');
	if (!is_null($userid)) {
		$subject = _COMPANY_." Support Portal Account Created";
		$message  = "
A user was created with your email address for the "._COMPANY_." Issue Tracking System.
You may access your account by going to:

"._URL_."

Login with the following information.
Username: {$user_data['username']}
Password: $password
Once logged in you may change your password by clicking on the preferences link on the 
left hand side.  If you have any questions please contact "._ADMINEMAIL_.".

            - "._COMPANY_;

		// Make sure we have mail class and initialize it
		include_once(_CLASSES_.'mail.class.php');
		if (!is_object($mailer)) {
			$mailer = new MAILER();
			$mailer->set('email_from',_EMAIL_);
		}
		$mailer->subject($subject);
		$mailer->to($email);
		$mailer->message($message);
		$mailer->send();
		return $userid;
	}
	return null;
}
	
/**
* Determine if a user is active or not
*
* @param integer $userid
* @return boolean
*/
function user_active($userid) {
	$sql = "SELECT active FROM users WHERE userid='$userid'";
	$active = $_ENV['dbi']->fetch_one($sql);
	if ($active == 't') {
		return TRUE;
	}
	return FALSE;
}

/**
* Returns username, if $link is TRUE then it makes it a link
* to retrive the user's information
*
* @param integer $userid ID of user to lookup, will default to $_SESSION['userid']
* @param boolean $link Use link or not
* @return string $username
*/
function username($userid,$link = FALSE) {
	if (empty($userid)) {
		return 'Unknown';
	}
	$sql = "SELECT username FROM users WHERE userid='$userid'";
	$username = $_ENV['dbi']->fetch_one($sql);
	if ($link and $_SESSION['javascript'] == 't') {
		$url = '?module=users&action=whois&userid='.$userid;
		$name = 'User Info';
		$features  = "location=no,menubar=no,status=no,toolbar=no";
		$features .= ",width=400,height=185,screenx=200,screeny=200";
		$javascript = " onClick=\"window.open('$url','$name','$features')\"";
		$username = "<a$javascript>$username</a>";
	}
	return $username;
}

/**
* Takes userid and returns associated email
*
* @param integer $userid ID of user to retrieve email for
* @return string
*/
function email($userid) {
	$sql = "SELECT email FROM users WHERE userid='".$userid."'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Takes userid and returns associated sms
*
* @param integer $userid ID of user to get sms for
* @return string
*/
function sms($userid) {
	$sql = "SELECT sms FROM users WHERE userid='$userid'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Retrive array of groups the user belongs to
*
* @param integer $uid ID of user
* @param boolean $query Format return for sql query
* @return array
*/
function user_groups($uid,$query = FALSE) {
	$first = TRUE;
	if (!$query) {
		$groups = array();
	}
	$sql = "SELECT u.gid FROM group_users u, groups g WHERE u.userid='$uid' 
			AND g.gid = u.gid ORDER BY g.name";
	$ugroups = $_ENV['dbi']->fetch_all($sql);
	foreach ($ugroups as $ugroup) {
		if ($query == TRUE) {
			$groups .= empty($group) ? $ugroup : ','.$ugroup;
		} else {
			$groups[] = $ugroup;
		}
	}
	return $groups;
}

/**
* Takes userid and returns associated menu
*
* @param integer $userid ID of user
* @return array $menu
*/
function user_menu($userid) {
	$sql = "SELECT text,url FROM menus WHERE userid='$userid' ORDER BY text";
	$rows = $_ENV['dbi']->fetch_all($sql,'array');
	foreach ($rows as $row) {
		$menu[$row['text']] = $row['url'];
	}
	return $menu;
}

/**
* Retrieve array of user preferences
*
* @param integer $userid ID of user
* @param string $preference Preference to retrieve
* @return array
*/
function user_preferences($userid = null,$preference = null) {
	$curr_prefs = array();
	if (is_null($userid)) {
		$userid = $_SESSION['userid'];
	}
	if (!empty($preference)) {
		$sql = "SELECT value FROM user_prefs WHERE userid='$userid' 
				AND LOWER(preference)=LOWER('$preference') ";
		return $_ENV['dbi']->fetch_one($sql);
	}
	$sql = "SELECT preference,value FROM user_prefs 
			WHERE userid='$userid'";
	$prefs = $_ENV['dbi']->fetch_all($sql,'array');
	if (is_array($prefs)) {
		foreach ($prefs as $pref) {
			$curr_prefs[$pref['preference']] = $pref['value'];
		} 
	}
	if (empty($curr_prefs['sort_by'])) {
		$curr_prefs['sort_by'] = 'issueid';
	}
	if (!empty($curr_prefs['show_fields'])) {
		$curr_prefs['show_fields'] = explode(',',$curr_prefs['show_fields']);
	} else {
		$curr_prefs['show_fields'] = array();
	}
	if (empty($curr_prefs['date_format'])) {
		$curr_prefs['date_format'] = 'm/d/Y';
	}
	if (empty($curr_prefs['local_tz'])) {
		$curr_prefs['local_tz'] = 'f';
	}
	return $curr_prefs;
}
?>
