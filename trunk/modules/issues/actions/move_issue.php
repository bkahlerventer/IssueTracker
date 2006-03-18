<?php
Module::check();
if (empty($_GET['issueid'])) {
	redirect();
}
if (!issue_priv($_GET['issueid'],'move_issues')) {
	redirect();
}
if ($_POST['confirm'] == 'true' and !empty($_POST['gid'])) {
	$sql = "SELECT g.name FROM issues i, groups g 
			WHERE i.issueid='".$_GET['issueid']."' AND i.gid = g.gid";
	$name = $_ENV['dbi']->fetch_one($sql);
	$sql = "DELETE FROM issue_groups WHERE issueid='".$_GET['issueid']."'";
	$_ENV['dbi']->query($sql);
	$insert['issueid'] = $_GET['issueid'];
	$insert['gid'] = $_POST['gid'];
	$insert['opened'] = time();
	$insert['show_issue'] = 't';
	$_ENV['dbi']->insert('issue_groups',$insert);
	unset($insert);
	$update['gid'] = $_POST['gid'];
	$_ENV['dbi']->update('issues',$update,"WHERE issueid='".$_GET['issueid']."'");
	unset($update);
	issue_log($_GET['issueid'],"Issue moved to ".group_name($_POST['gid'])." from $name.");
	redirect("?module=issues&action=view&issueid=".$_GET['issueid']);
}
Module::template('issues','move.tpl');
?> 
