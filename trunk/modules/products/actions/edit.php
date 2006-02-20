<?php
module_check();
if (permission_check('product_manager') and is_numeric($_GET['id'])) {
	if ($_POST['commit'] == 'true') {
		if (empty($_POST['product'])) {
			push_error('Please enter a valid name for the product.');
		} else {
			if (product_exists($_POST['product'])) {
				push_error('A product by that name alreasy exists.');
			} else {
				product_update($_GET['id'],array('product'=>$_POST['product']));
				redirect('?module=products');
			}
		}
	}
	module_template('products','edit.tpl');
}
?>
