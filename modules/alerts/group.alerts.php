<?php
/* $Id: group.announce.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Alerts
*/
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}

$alerts = alerts($_GET['gid']);
$smarty->assign('alerts',$alerts);

if (empty($_GET['gid'])) {
	$smarty->assign('title','System Alerts');
} else {
	$smarty->assign('title',group_name($_GET['gid'])." Alerts");
}
$smarty->display("alerts/group.tpl");
?>
