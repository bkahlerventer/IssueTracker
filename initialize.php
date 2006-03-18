<?php
/*
* Make sure register globals is turned off.  As of v4.0, Issue-Tracker
* will no longer function correctly with it turned on.  If you have
* other applications that require it to work, then you can create a
* .htaccess file in the root Issue-Tracker directory that contains
* the following line
*
* php_flag register_globals off
*
* You must have AllowOverride set to Options or All in your Apache
* configuration for this to work though
*/
if (ini_get('register_globals') == 1) {
	print('Please turn off register_globals in your php.ini ');
	print('or make sure your webserver is setup to obey .htaccess files.'."\n");
	exit;
}

# Squash notices cause they annoy me
error_reporting(E_ALL ^ E_NOTICE);

// Make sure _PATH_ is defined, otherwise when we pull in the config
// file it will have no idea where to find our directories
if (!defined('_PATH_')) {
	define('_PATH_',dirname(__FILE__));
}

// Without these two files we're screwed
require_once(_PATH_.'/conf/config.php');
require_once(_CLASSES_.'dbi.class.php');
require_once(_CLASSES_.'module.class.php');
require_once(_CLASSES_.'permissions.class.php');
include_once(SMARTY_DIR.'Smarty.class.php');

// If the logs directory is not writable, quit now
if (!defined('PARSER')) {
	if (!is_writable(_LOGS_)) {
		print('Logs directory is not writable by the web server.  Please correct this.');
		exit;
	}
}

// If we're accessing issue-tracker through the browser and we're not using 
// the db session handler then make sure the sessions director is writable
if (defined('BROWSER')) {
	if (!$session_handler) {
		if (!is_writable(_SESSIONS_)) {
			print('Sessions directory is not writable by the web server.  Please correct this.');
			exit;
		}
		session_save_path(_SESSIONS_);
	} else {
		include_once(_FUNCTIONS_.'session.func.php');
	}
}

// Initialize the database abstraction layer
$_ENV['dbi'] = new DBI;
$_ENV['dbi']->init($db);
$_ENV['dbi']->admin_email = _ADMINEMAIL_;
$_ENV['dbi']->email_from = _EMAIL_;
$_ENV['dbi']->log_queries = FALSE;
// Log any queries that take more than 1 second to complete
// this should give us a good idea where any bottlenecks are
$_ENV['dbi']->long_query = 1;

// Only start smarty and sessions if viewing through browser
if (defined('BROWSER')) {
	session_name(_SESSIONNAME_);
	session_start();
	if (!is_array($_SESSION['errors']) or !is_array($_SESSION['errors'])) {
		$_SESSION['errors'] = array();
	}
	$_SESSION['theme'] = (!empty($_SESSION['theme']) and file_exists(_THEMES_.$_SESSION['theme'])) ? $_SESSION['theme'] : $default_theme;
	$_ENV['tpl'] = new Smarty;
	$_ENV['tpl']->template_dir = _TEMPLATES_.$_SESSION['theme']."/tpl/";
	$_ENV['tpl']->compile_dir  = _TPLCOMPILE_;
	$_ENV['tpl']->config_dir   = _TPLCONFIG_;
	$_ENV['tpl']->cache_dir    = _TPLCACHE_;
	$_ENV['tpl']->debugging    = FALSE;
	$_ENV['tpl']->caching      = FALSE;  // DO NOT CHANGE THIS!
}

# Load all functions file located in _FUNCTIONS_
$files = glob(_FUNCTIONS_.'/*.func.php');
foreach ($files as $file) {
	include_once($file);
}

# Run any module specific initialization routines
Module::includes('init');

# Pull in module functions
Module::includes('funcs');

if (defined('BROWSER')) {
	if (file_exists(_THEMES_.$_SESSION['theme']."/functions.php")) {
		include_once(_THEMES_.$_SESSION['theme']."/functions.php");
	}
	if (file_exists(_THEMES_.$_SESSION['theme']."/images.php")) {
		include_once(_THEMES_.$_SESSION['theme']."/images.php");
	}
	$title  = _TITLE_;
	$title .= !empty($_GET['module']) ? " :: ".ucwords($_GET['module']) : "";
	$title .= !empty($_GET['action']) ? " :: ".ucwords($_GET['action']) : "";
	$title .= !empty($_GET['gid']) ? " :: Group ".group_name($_GET['gid']) : "";
	$title .= !empty($_GET['issueid']) ? " :: ".$_GET['issueid'] : "";
	$_ENV['tpl']->assign('title',$title);
	$_ENV['tpl']->assign('crumbs',build_crumbs());
}

// Dont display any errors, just log them in the logs directory
ini_set("display_errors",0);
ini_set("log_errors",1);
ini_set("error_log",_LOGS_.'phperrors');
?>
