<?php
Module::check();
if ($_GET['update'] == 'true') {
	if (!preg_match('/^[_a-z0-9-\.\+]+@[a-z0-9-\.]+\.[a-z\.]{2,}$/si',$_POST['email'])) {
		push_error('Please enter an email address.');
	}
	$sql = "SELECT userid FROM users WHERE LOWER(email)=LOWER('".$_POST['email']."') 
			AND userid != '".$_SESSION['userid']."'";
	$userid = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$userid)) {
		push_error('This email address is already in use by another user.');
	}
	if (!empty($_POST['oldpass'])) {
		$sql = "SELECT userid FROM users WHERE userid='".$_SESSION['userid']."' 
				AND password='".md5($_POST['oldpass'])."'";
		if ($result = $dbi->query($sql)) {
			if ($_POST['newpass'] != $_POST['confirm']) {
				push_error('New Password and Confirmation do not match.');
			}
		} else {
			push_error('Old password is invalid.<br/>');
		}
	}
	if (!errors()) {
		$update['first_name'] = $_POST['first'];
		$update['last_name'] = $_POST['last'];
		$update['address'] = $_POST['address'];
		$update['address2'] = $_POST['address2'];
		$update['telephone'] = $_POST['phone'];
		$update['sms'] = $_POST['sms'];
		$update['email'] = $_POST['email'];
		if (!empty($_POST['newpass'])) {
			$update['password'] = md5($_POST['newpass']);
		}
		$_ENV['dbi']->update('users',$update,"WHERE userid='".$_SESSION['userid']."'");
		unset($update);
		if (!empty($_POST['new_text']) and !empty($_POST['new_link'])) {
			$insert['userid'] = $_SESSION['userid'];
			$insert['text'] = $_POST['new_text'];
			$insert['url'] = $_POST['new_link'];
			$_ENV['dbi']->insert('menus',$insert);
			unset($insert);
		}
		update_preference($_SESSION['userid'],'show_fields',join(',',$_POST['fields']));
		update_preference($_SESSION['userid'],'sort_by',$_POST['sort_by']);
		if (empty($_POST['wrap']) or $_POST['wrap'] == 0) {
			$_POST['wrap'] = 80;
		}
		if (!empty($_POST['wrap'])) {
			update_preference($_SESSION['userid'],'word_wrap',$_POST['wrap']);
		}
		update_preference($_SESSION['userid'],'disable_wrap',$_POST['disablewrap'] == 'on' ? 't' : 'f');
		update_preference($_SESSION['userid'],'date_format',$_POST['dformat']);
		update_preference($_SESSION['userid'],'local_tz',$_POST['localtz'] == 'on' ? 't' : 'f');
		update_preference($_SESSION['userid'],'session_timeout',$_POST['sesstimeout'] == 'on' ? 't' : 'f');
		redirect('?module=prefs');
	}
}

if (!empty($_GET['mid'])) {
	$sql = "DELETE FROM menus WHERE userid='".$_SESSION['userid']."' AND mid='".$_GET['mid']."'";
	$_ENV['dbi']->query($sql);
}

$links[] = array(
	'img' => $_ENV['imgs']['group'],
	'txt' => 'Group Preferences',
	'url' => '?module=prefs&action=group'
);

$sql = "SELECT first_name,last_name,email,sms,address,address2,telephone FROM users 
		WHERE userid='".$_SESSION['userid']."'";
$user = $_ENV['dbi']->fetch_row($sql,'array');
$_ENV['tpl']->assign('user',$user);

$sql = "SELECT mid,text,url FROM menus WHERE userid='".$_SESSION['userid']."'";
$menu_items = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('menu_items',$menu_items);

$issue_fields = array(
	array( 'field' => 'issueid',		'name' => 'Issue Number'	),
	array( 'field' => 'opened_by',		'name' => 'Opened By'		),
	array( 'field' => 'assigned_to',	'name' => 'Assigned To'		),
	array( 'field' => 'modified',		'name' => 'Last Modified'	),
	array( 'field' => 'status',			'name' => 'Status'			),
	array( 'field' => 'category',		'name' => 'Category'		),
	array( 'field' => 'severity',		'name' => 'Severity'		),
	array( 'field' => 'product',		'name' => 'Product'       	),
	array( 'field' => 'flags', 			'name' => 'Flags'			)
);

$_ENV['tpl']->assign('issue_fields',$issue_fields);
Module::template('prefs','prefs.tpl');
?>
