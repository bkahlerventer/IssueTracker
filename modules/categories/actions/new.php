<?php
/**
* Create a new category and add it to the database
*/
module_check();
if (permission_check('category_manager')) {
	if ($_POST['commit'] == 'true') {
		if (empty($_POST['category'])) {
			push_error('Please enter a valid name for the new category.');
		} else {
			$sql = "SELECT cid FROM categories 
					WHERE LOWER(category) = LOWER('".trim($_POST['category'])."')";
			$cid = $_ENV['dbi']->fetch_one($sql);
			if (!empty($cid)) {
				push_error('A category by that name already exists.');
			} else {
				$insert['category'] = $_POST['category'];
				$_ENV['dbi']->insert('categories',$insert);
				redirect('?module=categories');
			}
		}
	}
	if (empty($_POST['commit']) or errors()) {
		module_template('categories','new.tpl');
	}
} else {
	redirect();
}
?>
