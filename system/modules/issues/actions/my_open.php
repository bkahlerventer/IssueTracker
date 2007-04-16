<?php
Module::check();
if($_GET['showall'] != 'true'){
	$links[] = array(
		'img' => $_ENV['imgs']['show_clsoed'],
		'txt' => 'Show Closed',
		'url' => '?module=issues&action=my_open&showall=true&gid='.$_GET['gid']
	);
} else {
	$links[] = array(
		'img' => $_ENV['imgs']['hide_closed'],
		'txt' => 'Hide Closed',
		'url' => '?module=issues&action=my_open&showall=false&gid='.$_GET['gid']
	);
}

list($registered) = fetch_status(TYPE_REGISTERED);
$closed = join(',',fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));
if (empty($_GET['sort'])) {
	$_GET['sort'] = 'status';
}
$url  = "?module=issues&action=my_open$show$reverse";
$url .= $_GET['showall'] != 'true' ? '&showall=true' : '';
$url .= $_GET['reverse'] != 'true' ? '&reverse=true' : '';
$_ENV['tpl']->assign('url',$url);

$sql = "SELECT issueid,summary,status,gid,severity FROM issues 
		WHERE opened_by='".$_SESSION['userid']."' AND status != '$registered' ";
$sql .= $_GET['showall'] != 'true' ? "AND status NOT IN ($closed) " : '';
$sql .= "ORDER BY ".$_GET['sort']." DESC";
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('issues',$issues);
Module::template('issues','my_open.tpl');
?>
