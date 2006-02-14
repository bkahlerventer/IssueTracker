<?php
/* $Id: announce.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Alerts
*/
if (preg_match('/'.basename(__FILE__).'/i',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}
if (permission_check("create_alerts")) {
	$links[] = array(
		'txt' => 'New Alert',
		'url' => '?module=alerts&action=new',
		'img' => $_ENV['imgs']['new_alert']
	);
}

$system = alerts();
$smarty->assign('system',$system);
foreach ($_SESSION['groups'] as $gid) {
	$sql = "SELECT aid FROM alert_permissions WHERE gid='$gid'";
	$a = $dbi->fetch_all($sql);
	if (count($a) > 0) {
		$a = implode(",",$a);
		$sql = "SELECT aid,title FROM alerts 
				WHERE aid IN ($a) ORDER BY posted DESC";
		$a = $dbi->fetch_all($sql,"array");
	}
	$alerts[$gid] = $a;
}
$smarty->assign('alerts',$alerts);
$smarty->display('alerts/alerts.tpl');
?>
