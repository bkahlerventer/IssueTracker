<?php
/**
* Database Abstraction Layer
*
* This is the database abstraction layer used for Issue-Tracker.
* Currently it supports MySQL & PostgreSQL
*
* To initialize the dbi you must first form an array with your
* database parameters, and then pass the array to the init method.
*
* <code>
* <?php
* $db = array(
*	"type"	=>	"<mysql|pgsql>",
*	"host"	=>	"<host>",
*	"port"	=>	"<port>",
*	"name"	=>	"<database>",
*	"user"	=>	"<username>",
*	"pass"	=>	"<password>"
* );
*
* include_once("dbi.class.php");
* $dbi = new DBI;
* $dbi->init($db);
* ?>
* </code>
* 
* @author Edwin Robertson {TuxMonkey}
* @version 4.0
*/
class DBI {
	/** Database Type (mysql|pgsql|sqlite) */
	var $type;
	/** Database host to connect to */
	var $host;
	/** Port to use */
	var $port;
	/** Username to connect */
	var $user;
	/** Password to connect */
	var $pass;
	/** Database name */
	var $name;
	/** Database connection resource */
	var $link;
	/** Database administrator's email */
	var $admin_email;
	/** Email address to show emails are from */
	var $email_from;
	/** Should DBI run in debug mode */
	var $debugging = FALSE;
	/** Directory to store logs in */
	var $log_dir = './';
	/** Whether or not to cache query results of fetch_* methods */
	var $cache_fetch_results = FALSE;
	/** Default time to keep results in cache (seconds) */
	var $cache_length = 30;
	/** Cache array */
	var $cache = array();

	/** Constructor */
	function DBI() {
	}

	/**
	* Initialize database variables and make database connection
	* 
	* @param array $params
	*/
	function init($params = array()) {
		foreach ($params as $key => $val) {
			$this->$key = $val;
		}
		$this->type = strtolower($this->type);  
		$this->debug('Attempting to initialize database connection');
		switch ($this->type) {
			case 'mysql':
				$this->link = mysql_connect($this->host,$this->user,$this->pass);
				if ($this->link) {
					mysql_select_db($this->name,$this->link);
				}
				break;

			case 'pgsql':
				# Build the connection string
				$conn_str  = 'user='.$this->user;
				$conn_str .= !empty($this->pass) ? ' password=\''.$this->pass.'\'' : '';
				$conn_str .= !empty($this->host) ? ' host='.$this->host : '';
				$conn_str .= !empty($this->port) ? ' port='.$this->port : '';
				$conn_str .= ' dbname='.$this->name;
				$this->link = pg_connect($conn_str);
				break;

			case 'sqlite':
				if (!function_exists('sqlite_open')) {
					trigger_error('SQLite support not present in currently installed version of PHP.',E_USER_ERROR);
				} else {
					$this->link = sqlite_open($this->name);
				}
				break;

			default:
				$this->debug('Unknown database type in init()');
				break;
		}
		if (is_resource($this->link)) {
			$this->debug('Connection successfully made.');
		} else {
			$this->logger('Failed to establish connection with database.');
		}
	}

	/**
	* Execute given SQL string and return result
	*
	* @param string $sql SQL string to execute
	* @return resource
	*/
	function query($sql) {
		if (empty($sql)) {
			$this->debug('query() called with empty SQL string');
			return;
		}
		$this->debug($sql);
		switch ($this->type) {
			case 'mysql':
				$result = @mysql_query($sql,$this->link) or mysql_error($this->link);
				break;

			case 'pgsql':
				$result = @pg_query($this->link,$sql) or pg_last_error($this->link);
				break;

			case 'sqlite':
				$result = @sqlite_query($this->link,$sql) or sqlite_error_string(sqlite_last_error($this->link));
				break;

			default:
				$this->debug('Unknown database type in query()');
				break;
		}
		return $result;
	}

	/**
	* Return number of fields present in a single row of given resultset
	*
	* @param resource $result Result to count fields in
	* @return integer
	*/
	function num_fields($result) {
		if (empty($result)) {
			$this->logger('Invalid database result passed to num_fields().');
			return;
		}
		switch ($this->type) {
			case 'mysql':	$fields = @mysql_num_fields($result);	break;
			case 'pgsql':
				if (function_exists('pg_num_fields')) {
					$fields = @pg_num_fields($result);
				} else {
					$this->debug('Currently installed version of PHP does not support the pg_num_fields function!');
				}
				break;
			case 'sqlite':	$fields = @sqlite_num_fields($result);	break;
			default:
				$this->debug('Unknown database type in num_fields()');
				break;
		}
		return $fields;
	}

	/**
	* Return name of field from resultset
	*
	* @param resource $result
	* @param integer $field
	* @return string
	*/
	function field_name($result,$field) {
		if (empty($result)) {
			$this->debug('Invalid database result passed to field_name()');
			return;
		}
		switch ($this->type) {
			case 'mysql':	$name = @mysql_field_name($result,$field);	break;
			case 'pgsql':
				if (function_exists('pg_field_name')) {
					$name = @pg_field_name($result,$field);
				} else {
					$this->debug('Currently installed version of PHP does not support the pg_field_name function!');
				}
				break;
			case 'sqlite':	$name = @sqlite_field_name($result,$field);	break;
			default:
				$this->debug('Unknown database type in field_name()');
				break;
		}
		return $name;
	}

	/**
	* Return number of rows in a resultset
	*
	* @param resource $result
	* @return integer
	*/
	function num_rows($result) {
		if (empty($result)) {
			$this->debug('Invalid database result passed to num_rows()');
			return;
		}
		switch ($this->type) {
			case 'mysql':	$rows = @mysql_num_rows($result);	break;
			case 'pgsql':
				$rows = function_exists('pg_num_rows')
					? @pg_num_rows($result)
					: @pg_numrows($result);
				break;
			case 'sqlite':	$rows = @sqlite_num_rows($result);	break;
			default:
				$this->debug('Unknown database type in num_rows()');
				break;
		}
		return $rows;
	}

	/**
	* Return number of rows affected by result
	*
	* @param resource $result
	* @return integer
	*/
	function affected_rows($result) {
		if (empty($result)) {
			$this->debug('Invalid database result passed to affected_rows()');
			return;
		}
		switch ($this->type) {
			case 'mysql':	$rows = @mysql_affected_rows($result);	break;
			case 'pgsql':
				$rows = function_exists('pg_affected_rows')
					? @pg_affected_rows($result)
					: @pg_cmdtuples($result);
				break;
			case 'sqlite':	$rows = @sqlite_changes($this->link);	break;
			default:
				$this->debug('Unknown database type in affected_rows()');
				break;
		}
		return $rows;
	}

	/**
	* Fetch a row from given resultset
	*
	* @param resource $result
	* @param string $rtype Type of fetch to perform (row,array,object)
	* @param integer $offset Only used if specify field for $rtype
	* @return mixed
	*/
	function fetch($result,$rtype = 'row',$offset = 0) {
		$rtype = strtolower($rtype);
		switch ($this->type) {
			case 'mysql':
				switch ($rtype) {
					case 'array':	$data = @mysql_fetch_array($result,MYSQL_ASSOC);	break;
					case 'object':	$data = @mysql_fetch_object($result);				break;
					default:		$data = @mysql_fetch_row($result);					break;
				}
				break;
			case 'pgsql':
				switch ($rtype) {
					case 'array':	$data = @pg_fetch_array($result,null,PGSQL_ASSOC);	break;
					case 'object':	$data = @pg_fetch_object($result);					break;
					default:		$data = @pg_fetch_row($result);						break;
				}
				break;
			case 'sqlite':
				switch ($rtype) {
					case 'array':	$data = @sqlite_fetch_array($result,SQLITE_ASSOC);	break;
					case 'object':	(object)$data = @sqlite_fetch_array($result,SQLITE_ASSOC);	break;
					default:		$data = @sqlite_fetch_array($result,SQLITE_NUM);	break;
				}
				break;
			default:
				$this->debug('Unknown database type in fetch()');
				break;
		}
		return $data;
	}

	/**
	* Free memory used by a result
	*
	* @param resource $result
	*/
	function free($result) {
		if (empty($result)) {
			$this->debug('Invalid database result passed to free().');
			return;
		}
		switch ($this->type) {
			case 'mysql':	@mysql_free_result($result);	break;
			case 'pgsql':	@pg_free_result($result);		break;
			case 'sqlite':									break;
			default:
				$this->debug('Unknown database type in free()');
				break;
		}
	}

	/** Close connection to database server */
	function close() {
		switch ($this->type) {
			case "mysql":	@mysql_close($this->link);	break;
			case "pgsql":	@pg_close($this->link);		break;
			case "sqlite":	@sqlite_close($this->link);	break;
			default:
				$this->debug("Unknown database type in close()");
				break;
		}
	}

	/**
	* Retrieve the last insert id
	*
	* @param string $sequence The sequence to pull last id from
	* @return integer
	*/
	function insert_id($sequence = null) {
		switch ($this->type) {
			case 'mysql':	$id = @mysql_insert_id();	break;
			case 'pgsql':	
				if (!is_null($sequence)) {
					$id = $this->fetch_one("SELECT last_value FROM ".$sequence);
				}
				break;
			case 'sqlite':	$id = sqlite_last_insert_rowid($this->link);	break;
			default:	
				$this->debug('Unknown database type in insert_id()');
				break;
		}
		return $id;
	}

	/**
	* Retrieve the first column of first row from
	* result of given sql string
	*
	* @param string $sql SQL string to execute
	* @param integer $cache_time Time in seconds to cache results for
	* @return mixed
	*/
	function fetch_one($sql,$cache_time = null) {
		# Check cached results
		if ($cached_result = $this->check_cache($sql)) {
			return $cached_result;
		}
  		# Execute the query
		$result = $this->query($sql);
		# Check to see if we got data back
		if ($this->num_rows($result) > 0) {
			list($data) = $this->fetch($result);
			$this->free($result);
			$this->cache_result($sql,$data,$cache_time); 
			return $data;
		}
		return null;
	}

	/**
	* Retrieve the first row from result of given sql string
	*
	* @param string $sql SQL string to execute
	* @param string $rtype Type of data returned (row,array,object,field)
	* @param integer $cache_time Time in seconds to keep cached queries
	* @return mixed
	*/
	function fetch_row($sql,$rtype = 'row',$cache_time = null) {
		# Check cached results
		if ($cached_result = $this->check_cache($sql)) {
			return $cached_result;
		}
		# Execute the query
		$result = $this->query($sql);
		if ($this->num_rows($result) > 0) {
			$row = $this->fetch($result,$rtype);
			$this->free($result);
			$this->cache_result($sql,$row,$cache_time);
			return $row;
		}
		return null;
	}

	/**
	* Runs a query and returns the result in an associative array
	*
	* @param string $sql
	* @param string $rtype Type of data returned (row,array)
	* @param integer $cache_time Time in seconds to keep cached queries
	* @return array
	*/
	function fetch_all($sql,$rtype = 'row',$cache_time = null) {
		# Check cached results
		if ($cached_result = $this->check_cache($sql)) {
			return $cached_result;
		}
		# Execute query
		$result = $this->query($sql);
		if ($this->num_rows($result) > 0) {
			$rows = array();
			while ($row = $this->fetch($result,$rtype)) {
				if (count($row) > 1) {
					array_push($rows,$row);
				} else {
					array_push($rows,$row[0]);
				}
			}
			$this->free($result);
			$this->cache_result($sql,$rows,$cache_time);    
			return $rows;
		}
		return null;
	}

	/**
	* Generic insert function, if sequence is specified then the last
	* id will be returned
	*
	* @param string $table Table to insert data into
	* @param array $data Array of data to be inserted
	* @return integer
	*/
	function insert($table,$data,$sequence = null) {
		$first = TRUE;
		if (!is_array($data)) {
			return;
		}
		foreach ($data as $key => $val) {
			$fields .= !empty($fields) ? ','.$key : $key;
			$values .= !empty($values) 
				? ','.(trim($val) != '' ? "'".addslashes($val)."'" : 'NULL') 
				: (trim($val) != '' ? "'".addslashes($val)."'" : 'NULL');
		}
		$sql = "INSERT INTO $table ($fields) VALUES($values);";
		$result = $this->query($sql);
		if ($result) {
			if (!is_null($sequence) or $this->type == 'mysql') {
				$id = $this->insert_id($sequence);
				return $id;
			}
			return TRUE;
		}
		return FALSE;
	}
  
	/**
	* Generic update function, will only update a single row
	*
	* @param string $table Table to update
	* @param array $data Data to update in the table
	* @param string $condition Any conditional statements (Where id=3)
	*/
	function update($table,$data,$condition = null) {
		foreach ($data as $key => $val) {
			$values .= !empty($values) ? ',' : '';
			$values .= $key.'='.(trim($val) != '' ? "'".addslashes($val)."'" : "NULL");
		}
		$sql = "UPDATE $table SET $values $condition";
		return $this->query($sql);
	}

	/**
	* Delete a single row from a table
	*
	* @param string $table Table to delete row from
	* @param array $matches Data to match on for row deletion
	*/
	function delete($table,$matches) {
		$conditions = null;
		if (empty($table) or @count($matches) < 1) {
			return;
		}
		foreach ($matches as $field => $value) {
			$conditions .= is_null($conditions) ? "WHERE " : "AND ";
			$conditions .= "{$field}='".addslashes($value)."' ";
		}
		$sql = "DELETE FROM {$table} $conditions";
		return $this->query($sql);
	}

	/**
	* Retrieve a single field from a table
	*
	* @param string $table Table to pull field from
	* @param string $field Field to pull from table
	* @param string $idfield Unique field to match ID against
	* @param integer $id ID to match
	* @param integer $cache_time Time in seconds to cache results
	* @return mixed
	*/
	function getfield($table,$field,$idfield,$id,$cache_time = 15) {
		$sql = "SELECT {$field} FROM {$table} WHERE {$idfield}='{$id}'";
		return $this->fetch_one($sql,$cache_time);
	}

	/**
	* Save query results to cache
	*
	* @param string $sql Query being cached
	* @param string $result Results of query
	* @param string $cache_time Time to cache result (seconds)
	*/
	function cache_result($sql,$result,$cache_time = null) {
		if ($this->cache_fetch_results === TRUE) {
			if (!empty($result)) {
				$cache_time = !is_null($cache_time) ? $cache_time : $this->cache_length;
				$time = time();
				$expires = $time + $cache_time;
				if ($expires > $time) {
					$this->cache[md5($sql)] = array(
						'expires' => $expires,
						'results' => $result
					);
				}
			}
		}
	}

	/**
	* Check cached results for matching query
	*
	* @param string $sql Query to check for
	* @return mixed
	*/
	function check_cache($sql) {
		if ($this->cache_fetch_results === TRUE) {
			$md5sum = md5($sql);
			if (is_array($this->cache[$md5sum])) {
				if ($this->cache[$md5sum]['expires'] > time()) {
					return $this->cache[$md5sum]['results'];
				} else {
					unset($this->cache[$md5sum]);
				}
			}
		}
		return FALSE;
	}

	/** Save Fetch Cache */
	function save_cache() {
		if ($this->cache_fetch_results === TRUE) {
			$sql = "SELECT COUNT(*) FROM dbcache";
			$result = $this->query($sql);
			if (!$result) {
				$sql = "CREATE TABLE dbcache (cachedata TEXT NOT NULL);";
				$this->query($sql);
			} else {
				list($count) = $this->fetch($result);
				if ($count == 1) {
					$update['cachedata'] = trim(serialize($this->cache));
					$this->update('dbcache',$update);
				} else {
					$insert['cachedata'] = trim(serialize($this->cache));
					$this->insert('dbcache',$insert);
				}
			}
		}
	}

	/** Load Fetch Cache */
	function load_cache() {
		if ($this->cache_fetch_results === TRUE) {
			$sql = "SELECT * FROM dbcache";
			$result = $this->query($sql);
			if (!$result) {
				$sql = "CREATE TABLE dbcache (cachedata TEXT NOT NULL);";
				$this->query($sql);
				$this->load_cache();
			} else {
				# Check to see if we got data back
				if ($this->num_rows($result) > 0) {
					list($data) = $this->fetch($result);
					$this->free($result);
					$this->cache = unserialize(trim($data));
				}
			}
			if (!is_array($this->cache)) {
				$this->cache = array();
			}
		}
	} 

	/**
	* Log debug message to the dbi log
	*
	* @param string $msg Message to log
	*/
	function debug($msg) {
		if ($this->debugging) {
			$this->logger('[DEBUG] '.$msg);
		}
	}

	/**
	* Logging function
	*
	* @param string $msg Message to be logged to file
	* @param string $type Type of msg (error,query)
	*/
	function logger($msg) {
		$date = date('[r]',time());
		if ($fp = fopen($this->log_dir.'/dbi.log','a+')) {
			fwrite($fp,"$date $msg\n");
			fclose($fp);
		}
	}
}
?>
