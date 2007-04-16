<?php
Module::check();
if (Permission::check('category_manager')) {
	if (preg_match('/^[0-9]+$/',$_GET['id'])) {
		if ($_POST['confirm'] == 'true') {
			$_ENV['dbi']->query("DELETE FROM categories WHERE cid='".$_GET['id']."'");
			redirect('?module=categories');
		} else {
			Module::template('categories','delete.tpl');
		}
	}
}
?>
