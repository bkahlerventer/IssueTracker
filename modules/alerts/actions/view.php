<?php
/* $Id: view.announce.php 2 2004-08-05 21:42:03Z eroberts $ */
/**
* @package Issue-Tracker
* @subpackage Alerts
*/
if (preg_match('/'.basename(__FILE__).'/',$_SERVER['PHP_SELF'])) {
	print('Direct module access forbidden.');
	exit;
}
if (!empty($_GET['aid']) and can_view_alert($_GET['aid'])) {
	$sql = "SELECT title,message,posted,userid FROM alerts 
			WHERE aid='".$_GET['aid']."'";
	$alert = $dbi->fetch_row($sql,'array');
	$smarty->assign('alert',$alert);
	$smarty->display('alerts/view.tpl');
} else {
	redirect('?module=alerts');
}
?>
