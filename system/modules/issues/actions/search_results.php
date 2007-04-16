<?php
Module::check();
if (empty($_POST['criteria'])) {
	$_POST['criteria'] = '%';
}
if (@count($_POST['groups']) < 1 or empty($_POST['groups'])) {
	$_POST['groups'] = $_SESSION['groups'];
}
$issues = array();
$links[] = array(
	'img' => $_ENV['imgs']['search'],
	'txt' => 'Search Again',
	'url' => '?module=issues&action=search'
);

$sql = "SELECT DISTINCT i.issueid,i.gid,i.summary FROM issues i 
		LEFT JOIN issue_groups g USING (issueid) LEFT JOIN events e USING (issueid) 
		WHERE (LOWER(i.problem) LIKE LOWER('%".$_POST['criteria']."%') 
		OR LOWER(i.summary) LIKE LOWER('%".$_POST['criteria']."%') 
		OR LOWER(e.action) LIKE LOWER('%".$_POST['criteria']."%')) ";
$sql .= is_array($_POST['groups']) ? "AND g.gid IN (".join(',',$_POST['groups']).") " : "";
$sql .= is_array($_POST['opened']) ? "AND i.opened_by IN (".join(',',$_POST['opened']).") " : "";
$sql .= is_array($_POST['assigned']) ? "AND g.assigned_to IN (".join(',',$_POST['assigned']).") " : "";
$sql .= is_array($_POST['status']) ? "AND i.status IN (".join(',',$_POST['status']).") " : "";
$sql .= is_array($_POST['category']) ? "AND i.category IN (".join(',',$_POST['category']).") " : "";
$sql .= is_array($_POST['product']) ? "AND i.product IN (".join(',',$_POST['product']).") " : "";
$sql .= "ORDER BY i.issueid ASC";
$issues = $_ENV['dbi']->fetch_all($sql,'array');
$_ENV['tpl']->assign('issues',$issues);
Module::template('issues','search_results.tpl');
?>
