<?php
/* $Id: index.php 4 2004-08-10 00:36:34Z eroberts $ */
ob_start();
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
define('BROWSER',TRUE);
require_once('initialize.php');

if ($_GET['expired'] == 'true') {
	session_destroy();
	$smarty->display('header.tpl');
	$smarty->display('expired.tpl');
	$smarty->display('footer.tpl');
	ob_flush();
	exit;
}

set_time_limit(_MINUTE_);

if (!empty($_SESSION['ip'])
and ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'])) {
	session_destroy();
	header('Location: '._URL_);
}

if ($_GET['logout'] == 'true') {
	session_destroy();
	if ($_GET['session_expired'] == 'true') {
		header('Location: '._URL_.'?session_expired=true');
	} else {
		header('Location: '._URL_);
	}
}

if (!empty($_POST['username']) and !empty($_POST['password'])) {
	authenticate($_POST['username'],$_POST['password']);
}

if (isset($_SESSION['userid'])) {
	$_SESSION['groups'] = user_groups($_SESSION['userid']);
	$_SESSION['group_count'] = count($_SESSION['groups']);
	$_SESSION['prefs'] = user_preferences($_SESSION['userid']);
}

if ($_GET['module'] == 'download' and !empty($_GET['fid'])) {
	download($_GET['fid']);
}

if (isset($_SESSION['userid'])) {
	if (file_exists(_PATH_.'/download/'.$_SESSION['userid'])
	and $_GET['module'] != 'download') {
		remove_dir(_PATH_.'/download/'.$_SESSION['userid']);
	}
}

if (empty($_GET['issueid'])) {
	if (!empty($_GET['tid'])) {
		$_GET['issueid'] = $_GET['tid'];
	}
	if (!empty($_POST['issueid'])) {
		$_GET['issueid'] = $_POST['issueid'];
	}
}

$smarty->display('header.tpl');

if ((isset($_GET['module']) and file_exists(_MODULES_.$_GET['module'].'/noauth'))
or isset($_SESSION['userid'])) {
	if (!isset($_COOKIE['tz'])) {
		$_COOKIE['tz'] = _DEFTZ_;
	}

	if (file_exists(_MODULES_.$_GET['module'].'/noauth')) {
		$_GET['nonav'] = TRUE;
	}

	if (empty($_GET['nonav'])) {
		generate_navigation_menus();
		$smarty->display("leftnav.tpl");
	}

	if ($_GET['module'] != 'help' and !empty($_GET['module'])) {
		if (empty($_GET['action'])) {
			if (file_exists(_HELP_.$_GET['module'].'.hlp')) {
				$smarty->assign('help_file',_HELP_.$_GET['module'].'.hlp');
			}
		} else {
			if (file_exists(_HELP_.$_GET['module']."/".$_GET['action'].'.hlp')) {
				$smarty->assign('help_file',_HELP_.$_GET['module'].'/'.$_GET['action'].'.hlp');
			}
		}
	}

	if ($_GET['module'] != 'help') {
		$smarty->display('iconbar.tpl');
	}

	if (empty($_GET['module'])) {
		if ($_SESSION['group_count'] > 0) {
			$includes = module_includes('miniview');
			foreach ($includes as $inc) {
				include($inc);
			}
		} else {
			push_error('Your user does not belong to any groups within this system.  Please contact '._ADMINEMAIL_.' to correct this.');
			$smarty->display('errors.tpl');
		}
	} else {
		if ($_GET['module'] == 'help') {
			if (empty($_GET['act'])) {
				$file = _HELP_.$_GET['mod'].'.hlp';
			} else {
				$file = _HELP_.$_GET['mod'].'/'.$_GET['act'].'.hlp';
			}
			if ($fp = fopen($file,'r')) {
				$buffer = fread($fp,filesize($file));
				fclose($fp);
			} else {
				$buffer = 'Could not find a help file for requested module/action.';
			}
			$smarty->assign('help',$buffer);
			$smarty->display('help.tpl');
		} else {
			if (empty($_GET['action'])) {
				if (file_exists(_MODULES_.$_GET['module'].'/'.$_GET['module'].'.php')) {
					include_once(_MODULES_.$_GET['module'].'/'.$_GET['module'].'.php');
				}
			} else {
				if (file_exists(_MODULES_.$_GET['module'].'/actions/'.$_GET['action'].'.php')) {
					include_once(_MODULES_.$_GET['module'].'/actions/'.$_GET['action'].'.php');
				}
			}
		}
	}
} else {
	if ($fp = fopen(_INCLUDES_.'motd','r')) {
		$motd = fread($fp,filesize(_INCLUDES_.'motd'));
		$smarty->assign('motd',$motd);
		fclose($fp);
	}
	$smarty->assign('allow_register',$allow_register);
	$smarty->display('login.tpl');
}
$smarty->display('footer.tpl');
ob_end_flush();
?>
