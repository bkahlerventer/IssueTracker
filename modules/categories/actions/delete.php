<?php
/**
* Delete an existing category from the database
*/
module_check();
if (permission_check('category_manager')) {
	if (is_numeric($_GET['id'])) {
		if ($_POST['confirm'] == 'true') {
			$_ENV['dbi']->query("DELETE FROM categories WHERE cid='".$_GET['id']."'");
			redirect('?module=categories');
		} else {
			module_template('categories','delete.tpl');
		}
	}
}
?>
