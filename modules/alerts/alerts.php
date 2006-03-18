<?php
Module::check();
if (Permission::check('create_alerts')) {
	$links[] = array(
		'txt' => 'New Alert',
		'url' => '?module=alerts&action=new',
		'img' => $_ENV['imgs']['new_alert']
	);
}

$system = alerts();
$_ENV['tpl']->assign('system',$system);
foreach ($_SESSION['groups'] as $gid) {
	$sql = "SELECT aid FROM alert_permissions WHERE gid='$gid'";
	$a = $_ENV['dbi']->fetch_all($sql);
	if (count($a) > 0) {
		$a = implode(',',$a);
		$sql = "SELECT aid,title FROM alerts 
				WHERE aid IN ($a) ORDER BY posted DESC";
		$a = $_ENV['dbi']->fetch_all($sql,'array');
	}
	$alerts[$gid] = $a;
}
$_ENV['tpl']->assign('alerts',$alerts);
Module::template('alerts','alerts.tpl');
?>
