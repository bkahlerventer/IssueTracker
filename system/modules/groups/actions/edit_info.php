<?php
Module::check();
if (!Permission::is_employee()) {
	redirect();
}

if ($_GET['submit'] == 'true') {
	if (empty($_POST['gname'])) {
		push_error('Group name can not be blank.');
	} else if (!empty($_POST['bought']) and eregi('[^0-9]',$_POST['bought'])) {
		push_error("Please enter a numerical value for amount purchased.");
	} else if (!empty($_POST['amount']) and eregi('[^0-9\.]',$_POST['amount'])) {
		push_error("Please enter a numerical value for contract amount. [#.##]");
	} else if (!ereg("^(0{0,1}[1-9]|1[012])/(0{0,1}[1-9]|[12][0-9]|3[01])/(19|20)[0-9][0-9]$",$_POST['startdate'])) {
		push_error("Invalid start date.");
	} else if (!ereg("^(0{0,1}[1-9]|1[012])/(0{0,1}[1-9]|[12][0-9]|3[01])/(19|20)[0-9][0-9]$",$_POST['enddate'])) {
		push_error("Invalid end date.");
	} else if (!empty($_POST['emailaddy']) and !ereg("[TID]",$_POST['notes'])) {
		push_error("When using an email address the notes field must contain the string [TID] for the acknowledgment message.");
	}
	if (!errors()) {
		if ($_POST['gname'] != group_name($_GET['gid'])) {
			$update['name'] = $_POST['gname'];
		}

		$parts = explode('/',$_POST['startdate']);
		$sdate = mktime(0,0,0,$parts[0],$parts[1],$parts[2]);
		$parts = explode('/',$_POST['enddate']);
		$edate = mktime(0,0,0,$parts[0],$parts[1],$parts[2]);
    
		$update['address'] = $_POST['address'];
		$update['address2'] = $_POST['address2'];
		$update['contact'] = $_POST['pcontact'];
		$update['tech'] = $_POST['tcontact'];
		$update['tao'] = $_POST['tao'];
		$update['brm'] = $_POST['brm'];
		$update['sales'] = $_POST['sales'];
		$update['amount'] = !empty($_POST['amount']) ? $_POST['amount'] : 0;
		$update['bought'] = !empty($_POST['bought']) ? $_POST['bought'] : 0;
		$update['start_date'] = $sdate;
		$update['end_date'] = $edate;
		$update['notes'] = $_POST['notes'];
		$update['status_reports'] = $_POST['statusreports'] == "on" ? "t" : "f";
		$update['active'] = $_POST['active'] == "on" ? "t" : "f";
		$update['group_type'] = $_POST['grouptype'];

		if (Permission::is_admin($_SESSION['userid'])) {
			$update['email'] = $_POST['emailaddy'];
		}

		$_ENV['dbi']->update("groups",$update,"WHERE gid='".$_GET['gid']."'");
		unset($update);
		redirect('?module=groups&action=view&gid='.$_GET['gid']);
	}
}

$links[] = array(
	'txt' => 'Back to Group Information',
	'url' => '?module=groups&action=view&gid='.$_GET['gid'],
	'img' => $_ENV['imgs']['back']
);

$sql = "SELECT * FROM groups WHERE gid='".$_GET['gid']."'";
$group = $_ENV['dbi']->fetch_row($sql,"array");
if (is_null($group)) {
	redirect();
}
$_ENV['tpl']->assign('group',$group);
Module::template('groups','edit_info.tpl');
?>
