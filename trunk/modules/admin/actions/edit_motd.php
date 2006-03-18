<?php
Module::check();
if (!empty($_POST['motd'])) {
	if ($fp = @fopen(_INCLUDES_.'motd','w')) {
		fwrite($fp,$_POST['motd']);
		fclose($fp);
		redirect();
	} else {
		push_error('Could not open motd file for writing.');
	}
}

$links[] = array(
	'txt' => 'Back to Administration',
	'url' => '?module=admin',
	'img' => $_ENV['imgs']['back']
);

if (file_exists(_INCLUDES_.'motd')) {
	if ($fp = fopen(_INCLUDES_.'motd','r')) {
		$motd = stripslashes(fread($fp,filesize(_INCLUDES_.'motd')));
		fclose($fp);
		$smarty->assign('motd',$motd);
	} else {
		push_error('Could not open motd file for reading.');
	}
}
Module::template('admin','edit_motd.tpl');
?>
