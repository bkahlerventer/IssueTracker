<?php
/**
* Retrieve the status name of the given status id
*
* @param integer $id Id of status to retrieve
* @return string
*/
function status($id) {
	if (preg_match('/^[0-9]+$',$id)) {
		return $_ENV['dbi']->getfield('statuses','status','sid',$id);
	}
}

/**
* Retrieve a list of all statuses
*
* @return array
*/
function status_list() {
	$sql = "SELECT sid,status FROM statuses ORDER BY status";
	return $_ENV['dbi']->fetch_all($sql,'array');
}

/**
* Determines if the given status already exists in the database or not
*
* @param string $status Status to look for in the database
* @return boolean
*/
function status_exists($status) {
	$sql = "SELECT sid FROM statuses WHERE LOWER(status) = LOWER('".trim($status)."')";
	$sid = $_ENV['dbi']->fetch_one($sql);
	if (preg_match('/^[0-9]+$/',$sid)) {
		return TRUE;
	}
	return FALSE;
}

/**
* Creates a new status with the given name and returns the id of the new status
*
* @param string $status Status to be created
* @return boolean
*/
function status_create($status) {
	if (!status_exists($status)) {
		return $_ENV['dbi']->insert('statuses',array('status'=>trim($status)),1);
	}
}

/**
* Delete an existing status from the database 
*
* @param integer $id ID of the status that will be deleted
* @return boolean
*/
function status_delete($id) {
	if (preg_match('/^[0-9]+$/',$id)) {
		$_ENV['dbi']->delete('statuses',array('sid'=>$id));
		return TRUE;
	}
	return FALSE;
}

/**
* Update a status with the given information
*
* @param integer $id ID of status to update
* @param array $status New value to give the given status id
* @return boolean
*/
function status_update($id,$status) {
	if (preg_match('/^[0-9]+$/',$id)) {
		if (!status_exists($status)) {
			$_ENV['dbi']->update('statuses',array('status'=>$status),"WHERE sid='".$id."'");
			return TRUE;
		}
	}
	return FALSE;
}

/**
* Retrieve a status' type
*
* @param integer $sid ID of status
* @return integer
*/
function status_type($sid) {
	$sql = "SELECT status_type FROM statuses WHERE sid='$sid'";
	return $_ENV['dbi']->fetch_one($sql);
}

/**
* Retrieve an array of statuses matching given type
*
* @param integer $type Status Type (see conf/const.php for status type constants)
* @return array
*/
function fetch_status($type) {
	$statuses = array();
	if (!is_array($_ENV['stype'])) {
		$_ENV['stype'] = array();
	}
	if (is_array($type)) {
		foreach ($type as $val) {
			$s = fetch_status($val);
			foreach ($s as $sid) {
				if (!in_array($sid,$statuses)) {
					array_push($statuses,$sid);
				}
			}
		}
	} else {
		if (array_key_exists($type,$_ENV['stype'])) {
			return $_ENV['stype'][$type];
		} else {
			$sql = "SELECT sid FROM statuses WHERE status_type='$type'";
			$statuses = $_ENV['dbi']->fetch_all($sql);
		}
	}
	$_ENV['stype'][$type] = $statuses;
	return $statuses;
}
?>
