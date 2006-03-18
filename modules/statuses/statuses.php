<?php
Module::check();

// Used to make sure only 1 "Registered" status is defined
list($registered) = fetch_status(TYPE_REGISTERED);
$smarty->assign('status_types',$status_types);

if (Permission::is_admin() or Permission::check('status_manager')) { 
	if ($_GET['subaction'] == 'delete' and !empty($_GET['id'])) {
		if ($_POST['confirm'] == 'true') {
			$_ENV['dbi']->delete('statuses',array('sid'=>$_GET['id']));
			redirect('?module=admin&action=statuses');
		} else {
			Module::template('statuses','delete.tpl');
		}
	} else if ($_GET['subaction'] == 'new') {
		if ($_POST['commit'] == 'true') {
			if (empty($_POST['status'])) {
				push_error('Status can not be empty.');
			} else if ($_POST['status_type'] == TYPE_REGISTERED and !empty($registered)) {
				push_error('Only one "Registered" status is allowed.');
			} else {
				$sql = "SELECT sid FROM statuses 
						WHERE LOWER(status) = LOWER('".trim($_POST['status'])."')";
				$sid = $_ENV['dbi']->fetch_one($sql);
				if (!empty($sid)) {
					push_error('This status already exists.');
				} else {
					$insert['status'] = $_POST['status'];
					$insert['status_type'] = $_POST['status_type'];
					$_ENV['dbi']->insert('statuses',$insert);
					redirect('?module=admin&action=statuses');
				}
			}
		}
		if (empty($_POST['commit']) or errors()) {
			Module::template('statuses','new.tpl');
		}
	} else if ($_GET['subaction'] == 'edit' and !empty($_GET['id'])) {
		if ($_POST['commit'] == 'true') {
			if (empty($_POST['status'])) {
				push_error('Status can not be empty.');
			} else if ($_POST['status_type'] == TYPE_REGISTERED and !empty($registered)
			and $_GET['id'] != $registered) {
				push_error('Only one "Registered" status is allowed.');
			} else {
				$sql = "SELECT sid FROM statuses 
						WHERE LOWER(status) = LOWER('".trim($_POST['status'])."')";
				$sid = $_ENV['dbi']->fetch_one($sql);
				if (empty($sid) or $sid == $_GET['id']) {
					$update['status'] = $_POST['status'];
					$update['status_type'] = $_POST['status_type'];
					$_ENV['dbi']->update('statuses',$update,"WHERE sid='".$_GET['id']."'");
					redirect('?module=admin&action=statuses');
				} else {
					push_error('This status already exists.');
				}
			}
		} 
		if (empty($_POST['commit']) or errors()) {
			$sql = "SELECT status,status_type FROM statuses WHERE sid='".$_GET['id']."'";
			$status = $_ENV['dbi']->fetch_row($sql,'array');
			$_ENV['tpl']->assign('status',$status);
			Module::template('statuses','edit.tpl');
		}
	} else {
		$links[] = array(
			'txt' => 'Back to Administration',
			'url' => '?module=admin',
			'img' => $_ENV['imgs']['back']
		);
		$links[] = array(
			'txt' => 'New Status',
			'url' => '?module=admin&action=statuses&subaction=new',
			'img' => $_ENV['imgs']['status']
		);
		$sql = "SELECT sid,status,status_type FROM statuses ORDER BY status";
		$statuses = $_ENV['dbi']->fetch_all($sql,'array');
		$num_statuses = count($statuses);
		for ($x = 0;$x < $num_statuses;$x++) {
			$statuses[$x]['status_type'] = $status_types[$statuses[$x]['status_type']];
		}
		$_ENV['tpl']->assign('statuses',$statuses);
		Module::template('statuses','list.tpl');
	}
} else {
	redirect();
}
?>
