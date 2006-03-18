<?php
Module::check();
if (!empty($_GET['aid']) and can_view_alert($_GET['aid'])) {
	$sql = "SELECT title,message,posted,userid FROM alerts 
			WHERE aid='".$_GET['aid']."'";
	$alert = $_ENV['dbi']->fetch_row($sql,'array');
	$_ENV['tpl']->assign('alert',$alert);
	Module::template('alerts','view.tpl');
} else {
	redirect('?module=alerts');
}
?>
