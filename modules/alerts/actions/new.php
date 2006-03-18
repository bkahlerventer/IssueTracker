<?php
Module::check();
if (!Permission::check('create_alerts')) {
	redirect('?module=alerts');
}
if (!empty($_POST['new_title'])) {
	if (empty($_POST['new_title'])) {
		push_error('Please enter a title');
	} else if (empty($_POST['new_text'])) {
		push_error('Please enter an alert message.');
	} else if (count($_POST['groups']) < 1) {
		push_error('Please choose at least one group.');
	}
	if (!errors()) {
		$insert['title']      = $_POST['new_title'];
		$insert['message']    = $_POST['new_text'];
		$insert['is_global']  = in_array('GLOBAL',$_POST['groups']) ? 't' : 'f';
		$insert['posted']     = time();
		$insert['userid']     = $_SESSION['userid'];
		if ($aid = $_ENV['dbi']->insert('alerts',$insert,'alerts_aid_seq')) {
			unset($insert);
			if (!in_array('GLOBAL',$_POST['groups']) and count($_POST['groups']) > 0) {
				for ($x = 0;$x < count($_POST['groups']);$x++) {
					$insert['aid'] = $aid;
					$insert['gid'] = $_POST['groups'][$x];
					$_ENV['dbi']->insert('alert_permissions',$insert);
					unset($insert);
				}
			}
		}
		redirect('?module=alerts');
	}
}
Module::template('alerts','new.tpl');
?>
