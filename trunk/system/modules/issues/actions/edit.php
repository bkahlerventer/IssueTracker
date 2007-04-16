<?php
Module::check();
if (empty($_GET['eid'])) {
	redirect('?module=issues');
}
if ($disable_edit and !Permission::is_admin($_SESSION['userid'])) {
	redirect('?module=issues');
}

if ($_POST['commit'] == 'true') {
	if ($_POST['event'] == '') {
		push_error('Must enter something for the event.');
	}
	if (!errors()) {
		$update["action"] = $_POST['event'];
		$_ENV['dbi']->update('events',$update,"WHERE eid='".$_GET['eid']."'");
		unset($update);
		unset($event);
		redirect('?module=issues&action=view&issueid='.$_GET['issueid'].'&gid='.$_GET['gid']);
	}
}
$sql = "SELECT action,userid FROM events WHERE eid='{$_GET['eid']}'";
$data = $_ENV['dbi']->fetch_row($sql,'array');
if (!is_null($data)) {
	if ($data['userid'] == $_SESSION['userid'] or is_admin()) {
		$_ENV['tpl']->assign('event',$data['action']);
		Module::template('issues','edit_event.tpl');
	}
}
?>
