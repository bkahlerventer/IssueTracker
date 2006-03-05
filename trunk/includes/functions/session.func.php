<?php
/** Start a session - Do not access this function directly */
function it_session_open($save_path,$session_name) {
	it_session_gc(ini_get("session.gc_maxlifetime"));
	return TRUE;
}

/** Close a session - Do not access this function directly */
function it_session_close() {
	return TRUE;
}

/** Read session data - Do not access this function directly */
function it_session_read($id) {
	$id = addslashes($id);
	$expire = time() + ini_get("session.gc_maxlifetime");
	$sql = "SELECT session_data FROM sessions WHERE session_id='$id'";
	$result = $_ENV['dbi']->query($sql);
	if ($_ENV['dbi']->num_rows($result) > 0) {
		list($data) = $_ENV['dbi']->fetch($result);
		return $data;
	} else {
		$sql = "INSERT INTO sessions VALUES('$id','','$expire')";
		$_ENV['dbi']->query($sql);
	}
	return '';
}

/** Write session data - Do not access this function directly */
function it_session_write($id,$data) {
	$id = addslashes($id);
	$data = addslashes($data);
	$expire = time() + ini_get("session.gc_maxlifetime");
	$sql = "UPDATE sessions SET session_data='$data',session_expires='$expire' 
			WHERE session_id='$id'";
	$_ENV['dbi']->query($sql);
	return TRUE;
}

/** Destroy session - Do not access this function directly */
function it_session_destroy($id) {
	$sql = "DELETE FROM sessions WHERE session_id='$id'";
	$_ENV['dbi']->query($sql);
	return TRUE;
}

/** Session garbage cleanup - Do not access this function directly */
function it_session_gc($maxlifetime) {
	$sql = "DELETE FROM sessions WHERE session_expires < ".time();
	$_ENV['dbi']->query($sql);
	return TRUE;
}

/**
* This is just a simple redirect function
* but it makes sure that the session is written
* and closed before the redirect happens
*
* @param string $url URL to redirect to
*/
function redirect($url = null) {
	if (!preg_match('/^http|https/i',$url)) {
		$url = _URL_.$url;
	}
	session_write_close();
	header('Location: '.$url);
	exit;
}

if ($session_handler === TRUE) {
	session_set_save_handler(
		'it_session_open',
		'it_session_close',
		'it_session_read',
		'it_session_write',
		'it_session_destroy',
		'it_session_gc'
	);
}
?>
