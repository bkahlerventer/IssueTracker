<?php
Module::check();
if (empty($_GET['issueid'])) {
	redirect();
}
if (can_view_issue($_GET['issueid'])) {
	$links[] = array(
		'txt' => 'Back to Issue',
		'url' => '?module=issues&action=view&issueid='.$_GET['issueid'],
		'img' => $_ENV['imgs']['back']
	);
	$sql = "SELECT userid,logged,message,private FROM issue_log 
			WHERE issueid='".$_GET['issueid']."' ";
	$sql .= !is_employee($_SESSION['userid']) ? "AND private != 't' " : "";
	$sql .= "ORDER BY logged";
	$messages = $_ENV['dbi']->fetch_all($sql,'array');
	$_ENV['tpl']->assign('messages',$messages);

	$sql = "SELECT * FROM issues WHERE issueid='".$_GET['issueid']."'";
	$issue = $_ENV['dbi']->fetch_row($sql,'array');
	$_ENV['tpl']->assign('issue',$issue);
	$_ENV['tpl']->assign('assigned',issue_assigned($_GET['issueid']));
	Module::template('issues','view.tpl');
	Module::template('issues','view_log.tpl');
} else {
	redirect();
}
?>
