<?php
Module::check();
$alerts = alerts($_GET['gid']);
$_ENV['tpl']->assign('alerts',$alerts);

if (empty($_GET['gid'])) {
	$_ENV['tpl']->assign('title','System Alerts');
} else {
	$_ENV['tpl']->assign('title',group_name($_GET['gid']).' Alerts');
}
Module::template('alerts','group.tpl');
?>
