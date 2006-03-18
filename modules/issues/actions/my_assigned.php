<?php
Module::check();
if($_GET['showall'] != 'true'){
	$links[] = array(
		'img' => $_ENV['imgs']['show_closed'],
		'txt' => 'Show Closed',
		'url' => '?module=issues&action=my_assigned&showall=true&gid='.$_GET['gid']
	);
} else {
	$links[] = array(
		'img' => $_ENV['imgs']['hide_closed'],
		'txt' => 'Hide Closed',
		'url' => '?module=issues&action=my_assigned&showall=false&gid='.$_GET['gid']
	);
}

list($registered) = fetch_status(TYPE_REGISTERED);
$closed = join(',',fetch_status(array(TYPE_CLOSED,TYPE_AUTO_CLOSED)));

if (empty($_GET['sort'])) {
	$_GET['sort'] = 'status';
}

$url  = "?module=issues&action=my_assigned$show$reverse";
$url .= $_GET['showall'] != 'true' ? '&showall=true' : '';
$url .= $_GET['reverse'] != 'true' ? '&reverse=true' : '';
$smarty->assign('url',$url);

$sql = "SELECT i.issueid,i.summary,i.opened_by,i.status,g.gid,i.severity FROM issues i,issue_groups g";
$sql .= $_GET['sort'] == "opened_by" ? ",users u " : ' ';
$sql .= "WHERE g.assigned_to='".$_SESSION['userid']."' ";
$sql .= "AND i.status != '$registered' ";
$sql .= $_GET['showall'] != "true" ? "AND i.status NOT IN ($closed) " : '';
$sql .= "AND i.issueid=g.issueid ";

switch ($_GET['sort']) {
	case 'opened_by':
		$sql .= "AND u.userid=t.".$_GET['sort']." DESC ";
		$sql .= "ORDER BY u.username ";
		break;

	default:
		$sql .= "ORDER BY i.".$_GET['sort']." DESC ";
		break;
}
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('issues',$issues);
Module::template('issues','my_assigned.tpl');
?>
