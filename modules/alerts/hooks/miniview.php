<?php
/* $Id: miniview.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Alerts
*/
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}

$sql = "SELECT a.aid,a.title,a.message,a.posted,u.username FROM alerts a,users u 
		WHERE a.is_global='t' AND u.userid=a.userid ORDER BY a.aid DESC LIMIT 1";
$result = $dbi->query($sql);
if($dbi->num_rows($result) > 0){
	$system = $dbi->fetch($result,'array');
	$smarty->assign('system',$system);
}
$groups = implode(',',$_SESSION['groups']);
$sql = "SELECT aid FROM alert_permissions WHERE gid IN ($groups)";
$a = $dbi->fetch_all($sql);
if (!is_null($a)) {
	$a = implode(',',$a);
	$sql = "SELECT aid,title FROM alerts
			WHERE aid IN ($a) AND posted > '".(time() - _WEEK_)."' ORDER BY aid DESC";
	$a = $dbi->fetch_all($sql,'array');
	$smarty->assign('alerts',$a);
}
$smarty->display('alerts/miniview.tpl');
?>
