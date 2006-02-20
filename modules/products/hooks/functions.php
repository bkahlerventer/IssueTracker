<?php
/**
* Retrieve a list of all products
*
* @return array
*/
function product_list() {
	$sql = "SELECT pid,product FROM products ORDER BY product";
	$products = $_ENV['dbi']->fetch_all($sql,'array');
	return $products;
}

/**
* Determines if the given product already exists in the database or not
*
* @param string $product Product to look for in the database
* @return boolean
*/
function product_exists($product) {
	$sql = "SELECT pid FROM products 
			WHERE LOWER(product) = LOWER('".trim($product)."')";
	$pid = $_ENV['dbi']->fetch_one($sql);
	return is_numeric($pid);
}

/**
* Creates a new product with the given name and returns the id of the new product
*
* @param string $product Product to be created
* @param array $categories Array of category ids for the product
* @return boolean
*/
function product_create($product,$categories = array()) {
	$insert['product'] = trim($product);
	if (@count($categories) > 0) {
		$insert['categories'] = join(',',$categories);
	}
	$id = $_ENV['dbi']->insert('products',$insert,1);
	return $id;
}

/**
* Delete an existing product from the database 
*
* @param integer $product_id ID of the product that will be deleted
* @return boolean
*/
function product_delete($product_id) {
	if (is_numeric($product_id)) {
		$_ENV['dbi']->query("DELETE FROM products WHERE pid='".$product_id."'");
		return TRUE;
	}
	return FALSE;
}

/**
* Update a product with the given information
*
* @param integer $product_id ID of product to update
* @param array $fields Array of key=>value pairs to update the product with
* @return boolean
*/
function product_update($product_id,$fields) {
	$update = array();
	if (is_numeric($product_id)) {
		$valid_fields = array('product','categories');
		foreach ($fields as $key => $value) {
			if (in_array($key,$valid_fields)) {
				if ($key == 'categories' and is_array($value)) {
					$value = join(',',$value);
				}
				$update[$key] = $value;
			}
		}
		$_ENV['dbi']->update('products',$update,"WHERE pid='".$product_id."'");
		return TRUE;
	}
	return FALSE;
}

/**
* Pull the list of categories for a give product
*
* @param integer $product_id ID of product to pull categories for
* @return array|boolean
*/
function product_categories($product_id) {
	if (is_numeric($product_id)) {
		$sql = "SELECT categories FROM products 
				WHERE pid='".$product_id."'";
		$list = $_ENV['dbi']->fetch_one($sql);
		$categories = explode(',',$list);
		return $categories;
	}
	return FALSE;
}
?>
