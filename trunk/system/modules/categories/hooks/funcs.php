<?php
/**
* Retrieve the category name of the given category id
*
* @param integer $id Id of category to retrieve
* @return string
*/
function category($id) {
	return $_ENV['dbi']->getfield('categoriess','category','cid',$id);
}

/**
* Retrieve a list of all categories
*
* @return array
*/
function category_list() {
	$sql = "SELECT cid,category FROM categories ORDER BY category";
	return $_ENV['dbi']->fetch_all($sql,'array');
}

/**
* Determines if the given category already exists in the database or not
*
* @param string $category Category to look for in the database
* @return boolean
*/
function category_exists($category) {
	$sql = "SELECT cid FROM categories WHERE LOWER(category) = LOWER('".trim($category)."')";
	$cid = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$cid)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Creates a new category with the given name and returns the id of the new category
*
* @param string $category Category to be created
* @return boolean
*/
function category_create($category) {
	if (!category_exists($category)) {
		$_ENV['dbi']->insert('categories',array('category'=>trim($category)),1);
	}
}

/**
* Delete an existing category from the database 
*
* @param integer $id ID of the category that will be deleted
* @return boolean
*/
function category_delete($id) {
	if (preg_match('/^[0-9]+$/',$id)) {
		$_ENV['dbi']->delete('categories',array('cid'=>$id));
		return TRUE;
	}
	return FALSE;
}

/**
* Update a category with the given information
*
* @param integer $id ID of category to update
* @param array $category New value to give to the given category id
* @return boolean
*/
function category_update($id,$category) {
	if (preg_match('/^[0-9]+$/',$id)) {
		if (!category_exists($category)) {
			$_ENV['dbi']->update('categories',array('category'=>$category),"WHERE cid='".$id."'");
			return TRUE;
		}
	}
	return FALSE;
}
?>
