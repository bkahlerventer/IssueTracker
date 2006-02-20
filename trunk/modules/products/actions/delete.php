<?php
/**
* Delete a product from the database
*/
module_check()
if (permission_check('product_manager') and is_numeric($_GET['id'])) {
	if ($_POST['confirm'] == 'true') {
		product_delete($_GET['id']);
		redirect('?module=products');
	} else {
		module_template('products','delete.tpl');
	}
} else {
	redirect();
}
?>
