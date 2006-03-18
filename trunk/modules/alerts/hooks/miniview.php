<?php
Module::check();
$sql = "SELECT a.aid,a.title,a.message,a.posted,u.username FROM alerts a,users u 
		WHERE a.is_global='t' AND u.userid=a.userid ORDER BY a.aid DESC LIMIT 1";
$result = $_ENV['dbi']->query($sql);
if($_ENV['dbi']->num_rows($result) > 0){
	$system = $_ENV['dbi']->fetch($result,'array');
	$_ENV['tpl']->assign('system',$system);
}
$groups = implode(',',$_SESSION['groups']);
$sql = "SELECT aid FROM alert_permissions WHERE gid IN ($groups)";
$a = $_ENV['dbi']->fetch_all($sql);
if (!is_null($a)) {
	$a = implode(',',$a);
	$sql = "SELECT aid,title FROM alerts
			WHERE aid IN ($a) AND posted > '".(time() - _WEEK_)."' ORDER BY aid DESC";
	$a = $_ENV['dbi']->fetch_all($sql,'array');
	$_ENV['tpl']->assign('alerts',$a);
}
Module::template('alerts','miniview.tpl');
?>
