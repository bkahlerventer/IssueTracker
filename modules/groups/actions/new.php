<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}
if (!empty($_POST['gname'])) {
	if (empty($_POST['gname'])) {
		push_error("Please enter a name for this group");
	}
	if (!empty($_POST['amount']) and !ereg('[0-9\.]{1,}',$_POST['amount'])) {
		push_error("Invalid value given for amount, please exclude commas. (#.##)");
	}
	$sql = "SELECT gid FROM groups 
			WHERE LOWER(name)=LOWER('".addslashes($_POST['gname'])."')";
	$id = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$id)) {
		push_error("This group name is already taken.");
	}
	if (!errors()) {
		$insert['name'] = $_POST['gname'];
		$insert['address'] = $_POST['address'];
		$insert['address2'] = $_POST['address2'];
		$insert['contact'] = $_POST['pcontact'];
		$insert['tech'] = $_POST['tcontact'];
		$insert['tao'] = $_POST['tao'];
		$insert['brm'] = $_POST['brm'];
		$insert['sales'] = $_POST['sales'];
		$insert['amount'] = !empty($_POST['amount']) ? $_POST['amount'] : 0;
		$insert['bought'] = !empty($_POST['bought']) ? $_POST['bought'] : 0;
		if (!empty($_POST['type'])) {
			$insert['group_type'] = $_POST['grouptype'];
		}
		$insert['email'] = $_POST['emailaddy'];
		$insert['notes'] = $_POST['notes'];
		$gid = $_ENV['dbi']->insert('groups',$insert,'groups_gid_seq');
		if (!empty($gid)) {
			group_useradd($_SESSION['userid'],$gid);
			$update['perm_set'] = PSET_GADMIN;
			$_ENV['dbi']->update('group_users',$update,"WHERE gid='$gid' AND userid='".$_SESSION['userid']."'");
			unset($update);
			session_register('GROUP_WIZARD');
			$_SESSION['GROUP_WIZARD'] = TRUE;
			redirect('?module=groups&action=edit&type=categories&gid='.$gid);
		} else {
			push_error('This group could not be created please contact '._ADMINEMAIL_.' about this issue.');
		}
	}
}
$links[] = array(
	'txt' => 'Back to Groups',
	'url' => '?module=groups',
	'img' => $_ENV['imgs']['back']
);
Module::template('groups','new.tpl');
?>
