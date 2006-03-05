<?php
/**
* Class for working with modules
*
* @author Edwin Robertson <tm@tuxmonkey.com>
* @version 0.1
*/
class Module {
	/**
	* Look for "install" hook and run that hook for the given module.
	*
	* @param string $module Module to be installed
	* @return boolean
	*/
	function install($module) {
		# make sure to remove all leading and trailing white space
		$module = trim($module);

		logger::debug('Installing module '.$module);	
		# make sure the module directory exists
		if (@is_dir($_ENV['module_path'].'/'.$module)) {
			logger::debug('Module directory exists, looking for install hook');
			# check to see if the install hook exists
			if (@file_exists($_ENV['module_path'].'/'.$module.'/hooks/install.php')) {
				if (!require_once($_ENV['module_path'].'/'.$module.'/hooks/install.php')) {
					logger::error('Installation of '.$module.' module failed');
					return FALSE;
				}
			} else {
				logger::debug('Could not locate install module hook');
			}
			logger::system('Installed module '.$module.' successfully');
			return TRUE;
		} else {
			logger::error('Attempted to install module which does not exist ('.$module.')');
		}
		return FALSE;
	}

	/**
	* Look for "uninstall" hook and run that hook for the given module.
	*
	* @param string $module Module to be removed
	* @return boolean
	*/
	function uninstall($module) {
		# make sure to remove all leading and trailing white space
		$module = trim($module);
	
		# make sure the module director exists
		if (@is_dir($_ENV['module_path'].'/'.$module)) {
			# check to see if the uninstall hook exists
			if (@file_exists($_ENV['module_path'].'/'.$module.'/hooks/uninstall.php')) {
				if (!require_once($_ENV['module_path'].'/'.$module.'/hooks/install.php')) {
					return FALSE;
				}
			}
			return TRUE;
		} else {
			logmsg('Attempted to uninstall module which does not exist ('.$module.')');
		}
		return FALSE;
	}

	/**
	* Placeholder function here to make sure there is no direct access to modules
	*
	* @return TRUE
	*/
	function check() {
		return TRUE;
	}

	/**
	* Pull array of "loadable" modules
	*
	* @return array
	*/
	function loadable_modules() {
		if (empty($_ENV['lmodules'])) {
			$_ENV['lmodules'] = array();
			if ($dir = opendir($_ENV['module_path'])) {
				while (($item = readdir($dir)) !== FALSE) {
					if (is_dir($_ENV['module_path'].'/'.$item)) {
						array_push($_ENV['lmodules'],$item);
					}
				}
				closedir($dir);
			} 
		}
	}

	/**
	* Create an array of files to be include by given hook call
	*
	* @param string $hook Name of hook to match on
	* @param boolean $short Determines if we should perform short match or not
	* @return array
	*/
	function includes($hook,$short = TRUE) {
		$includes = array();
		$filename = $hook.'.php';

		// Make sure to update loadable modules
		Module::loadable_modules();

		if ($short !== TRUE) {
			if (!empty($_GET['action'])) {
				$filename .= $_GET['action'].'/'.$filename;
			}
			if (!empty($_GET['module'])) {
				$filename .= $_GET['module'].'/'.$filename;
			}
		}
		foreach ($_ENV['lmodules'] as $module) {
			if (file_exists($_ENV['module_path'].'/'.$module.'/'.$filename)) {
				array_push($includes,$_ENV['module_path'].'/'.$module.'/'.$filename);
			}
		}
		return $includes;
	}
}		
?>
