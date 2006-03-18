<?php
Module::check();
if (empty($_GET['gid']) or !user_in_group($_GET['gid'])) {
	redirect('?module=groups');
} else {
	if (Permission::check('create_issues',$_GET['gid'])) {
		$links[] = array(
			'txt' => 'Create Issue',
			'url' => '?module=issues&action=new&gid='.$_GET['gid'],
			'img' => $_ENV['imgs']['new_issue']
		);
	}
	if ($_GET['showall'] != 'true') {
		$links[] = array(
			'txt' => 'Show Closed',
			'url' => '?module=issues&action=group&showall=true&gid='.$_GET['gid'],
			'img' => $_ENV['imgs']['show_closed']
		);
	} else {
		$links[] = array(
			'txt' => 'Hide Closed',
			'url' => '?module=issues&action=group&showall=false&gid='.$_GET['gid'],
			'img' => $_ENV['imgs']['hide_closed']
		);
	}
	if (empty($_GET['sort'])) {
		$_GET['sort'] = $_SESSION['sort'];
	}
	$rows = group_issues($_GET['gid']);
	$show_private = Permission::check('view_private',$_GET['gid']);
	foreach ($rows as $key => $val) {
		$rows[$key]['unread'] = unread_events($key,$_SESSION['userid'],$show_private);
		$rows[$key]['escto'] = issue_escalated_to($key,$_GET['gid']);
		$rows[$key]['escfrom'] = issue_escalated_from($key,$_GET['gid']);
	}
	$num_rows = count($rows);
	$colspan = count($_SESSION['prefs']['show_fields']) + 2;
	$title = "Issues :: ".group_name($_GET['gid'])." (Displaying $num_rows Issues)";
	$_ENV['tpl']->assign('colspan',$colspan);
	$_ENV['tpl']->assign('title',$title);
	$_ENV['tpl']->assign('rows',$rows);
	$_ENV['tpl']->assign('num_rows',$num_rows);
	$url = '?module=issues&action=group&gid='.$_GET['gid'];
	$url .= $_GET['showall'] == 'true' ? '&showall=true' : '';
	$_ENV['tpl']->assign('url',$url);
	Module::template('issues','group.tpl');
}
?>
