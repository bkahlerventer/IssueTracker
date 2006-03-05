<?php
# File size constants
define("_SIZEGB_",1073741824);
define("_SIZEMB_",1048576);
define("_SIZEKB_",1024);

/**
* Determine the size of a file
*
* @param integer $fid ID of file
* @return string
*/
function fsize($fid) {
	$sql = "SELECT uploaded_on,name,typeid,file_type FROM files WHERE fid='$fid'";
	list($date,$name,$id,$type) = $_ENV['dbi']->fetch_row($sql);
	if (!empty($name)) {
		$filename = !empty($date) ? $filename = $date."-".$name : $name;
		if (file_exists(_FILES_."$type/$id/$filename")) {
			$size = filesize(_FILES_."$type/$id/$filename");
			if ($size >= _SIZEGB_) {
				$size = number_format(($size / _SIZEGB_),2).' GB';
			} elseif ($size >= _SIZEMB_) {
				$size = number_format(($size / _SIZEMB_),2).' MB';
			} elseif ($size >= _SIZEKB_) {
				$size = number_format(($size / _SIZEKB_),2).' KB';
			} elseif ($size >= 0) {
				$size = $size . ' bytes';
			} else {
				$size = '0 bytes';
			}
		}
	} else {
		$size = 'N/A';
	}
	return $size;
}

/**
* Determines whether or not a user can download a file
*
* @param integer $fid ID of file to check
* @return boolean
*/
function can_download($fid) {
	if (is_admin() or is_employee()) {
		return TRUE;
	}
	$sql = "SELECT typeid,file_type FROM files WHERE fid='$fid'";
	list($id,$type) = $_ENV['dbi']->fetch_row($sql);
	if (!empty($id)) {
		switch ($type) {
			case 'groups':
				if (user_in_group($gid)) {
					return TRUE;
				}
				break;
			case 'issues':
				if (can_view_issue($id,$_SESSION['userid'])) {
					return TRUE;
				}
				break;
			default:
				logger('Could not determine type for file '.$fid);
				return FALSE;
				break;
		}
	}
	return FALSE;
}

/**
* Determines if a file is valid for upload/download
*
* @param string $filename is the full name of the file to be reviewed
* @return boolean
*/
function mime_check($filename) {
	global $bad_mime;
	if (empty($filename)) {
		return FALSE;
	} else {
		foreach ($bad_mime as $key => $val) {
			if (eregi("$val$",trim($filename))) {
				return FALSE;
			}
		}
	}
	return TRUE;
}

/**
* Upload a file
*
* @param integer $id ID of the issue or group to associate with
* @param string $type Type of upload (issue|group)
* @return integer
*/
function upload($id,$type = 'issues') {
	// cycle through the $_FILES array
	foreach ($_FILES as $upload) {
		if (empty($upload['name']) or $upload['size'] < 0) {
			continue;
		}
		$insert['file_type'] = $type;
		$insert['typeid'] = $id;
		$insert['userid'] = $_SESSION['userid'];
		$insert['uploaded'] = time();
		$insert['private'] = $_POST['eprivate'] == 'on' ? 't' : 'f';
		$insert['filetype'] = $upload['type'];
		$insert['filesize'] = $upload['size'];
		$insert['filename'] = $upload['name'];
		$insert['content']	= file_get_contents($upload['tmp_name']);
		$fid = $_ENV['dbi']->insert('files',$insert,TRUE);
		unset($insert);
		if (!empty($fid)) {
			if ($type == 'groups') {
				logger(username($_SESSION['userid']).' uploaded '.$upload['name'].' to '.group_name($id),'uploads');
			} else {
				logger(username($_SESSION['userid'])." uploaded ".$upload['name']." to issue $id.","uploads");
				issue_log($_GET['issueid'],"File uploaded: ".$upload['name']);
			}
			$files[] = $fid;
		} else {
			push_fatal_error($upload['name']." could not be uploaded.");
		}
	}
	return $files;
}

/**
* Download a file by its fid
*
* @param integer $fid ID of file to be downloaded
*/
function download($fid) {
	// check to see if user can download this file
	if (can_download($fid)) {
		$sql = "SELECT filetype,filesize,filename,content FROM files WHERE fid='$fid'";
		$data = $_ENV['dbi']->fetch_row($sql,'array');
		if (is_array($data)) {
			header("Content-Type: {$data['filetype']}");
			header("Content-Disposition: attachment; filename={$data['filename']};");
			header("Content-Length: ".filesize($data['filename']));
			echo($data['content']);
		}
	}
}

/**
* Log messages to database/file
*
* @param string $message Message to be logged to file
* @param string $type Type of logging
*/
function logger($message,$type) {
	$date = date('[r]',time());
    if (!empty($_SESSION['userid'])) {
		$user = '[User: '.username($_SESSION['userid']).']';
    }
    $fp = fopen(_LOGS_.'/'.$type,'a+');
    fwrite($fp,"$date$user $message\n");
    fclose($fp);
}
?>
