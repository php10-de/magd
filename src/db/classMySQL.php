<?php
/**
 * MySQL Class
 *
 * @copyright Sonnenertrag.eu
 * @author Matthias Hanus | matthias.hanus@gmail.com
 *
 * @version 0.1
 *
 *
 */
class MySQL {

	protected $conn_id;
	protected $query_count = 0;
	protected $query_time = 0;
	protected $magic_quotes;
        protected $memcache;
        protected $log = false;
        protected $db_log = false;
        protected $text_log = false;
        protected $slow_query = 0.00;

	public function __construct($host, $user, $pass, $db) {

		$this->mysqlConnect ( $host, $user, $pass );

		$this->selectDB ( $db );

		$this->query ( "SET NAMES 'utf8'" );
		$this->query ( "SET CHARACTER SET 'utf8'" );
		$config = Registry::getInstance ()->getConfig ();
		if ($config->get ( 'DB', 'memcache')) {
                    $this->memcache = new Memcache;
                    $this->memcache->connect('localhost', 11211);
                }

	}

	public function mysqlConnect($host, $user, $pass) {

		$this->conn_id = mysql_connect ( $host, $user, $pass );

		if (! $this->conn_id) {

			throw new DBConnectionException ( "Can't connect to MySQL-Server Error: " . mysql_error ( $this->conn_id ) );

		}

	}

	public function selectDB($db) {

		$db = mysql_select_db ( $db, $this->conn_id );

		if (! $db) {

			throw new DBSelectException ( "Database is not available! Error: " . mysql_error ( $db ) );

		}

	}

	public function update($table, Array $values, $where) {
                if ($this->text_log) {
                    $GLOBALS ['log']->debug ( $query );
                }
                if ($this->log) {
                    $time_start = microtime ( true );
                }

		$updatestring = '';

		foreach ( $values as $field => $value ) {

			$updatestring .= sprintf ( "`%s` = %s, ", $field, $this->ins_string ( $value ) );

		}

		$updatestring = substr ( $updatestring, 0, - 2 );

		$sql = sprintf ( "UPDATE `%s` SET %s WHERE %s", $table, $updatestring, $where );

		$res = $this->query ( $sql );

                if ($this->log) {

                    $this->query_count = ($this->query_count + 1);

                    $time_end = microtime ( true );

                    $time = ($time_end - $time_start) + 0;

                    $this->query_time = ($this->query_time + $time);

                    if ($this->text_log) {

                        $GLOBALS ['log']->debug ( $res . '(' . $time . ')');

                    }
                    //mail('fraunholz@mac.com','update', $res.'-'.$this->log.'---'."INSERT INTO db_log (query, query_time, query_duration, avg_duration, res) VALUES('".mysqli_real_escape_string($con, $sql)."', now(), " . $time . ", " . $time . "," . (int) $res .") ON DUPLICATE KEY UPDATE cnt = cnt + 1, query_duration=query_duration + " . $time . ", avg_duration = ((query_duration + " . $time . ") / (cnt + 1))");

                    if ($this->db_log  && ($time > $this->slow_query)) {
                        mysqli_query($con, "INSERT INTO db_log (query, query_time, query_duration, avg_duration, request_uri)
                            VALUES('".mysqli_real_escape_string($con, $sql)."', now(), " . $time . ", " . $time . ", '" . $_SERVER['REQUEST_URI']."')
                                ON DUPLICATE KEY UPDATE cnt = cnt + 1, query_duration=query_duration + " . $time . ", avg_duration = ((query_duration + " . $time . ") / (cnt + 1))");
                    }

                }
		return true;

	}

	public function insert($table, Array $values, $lastId = false) {
                if ($this->text_log) {
                    $GLOBALS ['log']->debug ( $query );
                }
                if ($this->log) {
                    $time_start = microtime ( true );
                }


		$insertstring = '';

		foreach ( $values as $field => $value ) {

			$insertstring .= sprintf ( "`%s` = %s, ", $field, $this->ins_string ( $value ) );

		}

		$insertstring = substr ( $insertstring, 0, - 2 );

		$sql = sprintf ( "INSERT INTO `%s` SET %s", $table, $insertstring );

		$res = $this->query ( $sql );

                if ($this->log) {

                    $this->query_count = ($this->query_count + 1);

                    $time_end = microtime ( true );

                    $time = ($time_end - $time_start) + 0;

                    $this->query_time = ($this->query_time + $time);

                    if ($this->text_log) {

                        $GLOBALS ['log']->debug ( $res . '(' . $time . ')');

                    }

                    if ($this->db_log  && ($time > $this->slow_query)) {
                        mysqli_query($con, "INSERT INTO db_log (query, query_time, query_duration, avg_duration, request_uri)
                            VALUES('".mysqli_real_escape_string($con, $sql)."', now(), " . $time . ", " . $time . ", '" . $_SERVER['REQUEST_URI']."')
                                ON DUPLICATE KEY UPDATE cnt = cnt + 1, query_duration=query_duration + " . $time . ", avg_duration = ((query_duration + " . $time . ") / (cnt + 1))");
                    }

                }

		if ($lastId === true) {

			return mysql_insert_id ( $this->conn_id );

		}

		return true;

	}

	public function numRows($res) {

		return mysqli_num_rows ( $res );

	}

	public function fetchRow($res) {

		return mysqli_fetch_row ( $res );

	}

	public function fetchArray($res) {
		$array = mysqli_fetch_array ( $res, MYSQL_ASSOC );
		foreach($array as $key => $value) {
			$array[$key] = stripslashes($value);
		}
		return $array;
	}

	public function getArray($query) {
		$return = array();
		if($res = $this->query($query)){
			while ($row = $this->fetchArray($res)) {
				$return[] = $row;
			}
			return $return;
		}
		return $false;
	}

	public function fetchObject($res, $object = '') {

		return mysql_fetch_object ( $res, $object );

	}

	public function query($query) {
                if ($this->text_log) {
                    $GLOBALS ['log']->debug ( $query );
                }
                if ($this->log) {
                    $time_start = microtime ( true );
                }


		if (isset ( $query )) {

			$res = mysql_query ( $query );
                        //echo 'Verwende memcache...';

		} else {

			throw new DBInputOutputException ( 'No query given' );

		}

		if (! $res) {

			throw new DBQueryException ( mysql_error ( $this->conn_id ) . ' | Query: ' . $query );

		}

                if ($this->log) {

                    $this->query_count = ($this->query_count + 1);

                    $time_end = microtime ( true );

                    $time = ($time_end - $time_start) + 0;

                    $this->query_time = ($this->query_time + $time);

                    if ($this->text_log) {

                        $GLOBALS ['log']->debug ( $res . '(' . $time . ')');

                    }

                    if ($this->db_log  && ($time > $this->slow_query)) {
                        mysqli_query($con, "INSERT INTO db_log (query, query_time, query_duration, avg_duration, res, request_uri)
                            VALUES('".mysqli_real_escape_string($con, $query)."', now(), " . $time . ", " . $time . "," . (int) $res .", '" . $_SERVER['REQUEST_URI']."')
                                ON DUPLICATE KEY UPDATE cnt = cnt + 1, query_duration=query_duration + " . $time . ", avg_duration = ((query_duration + " . $time . ") / (cnt + 1))");
                    }

                }

		return $res;

	}

	public function memcacheArray($query, $timeout = 1) {
		$config = Registry::getInstance ()->getConfig ();
		if (!$config->get ( 'DB', 'memcache')) {
                    return $this->query($query);
                }
                if ($this->text_log) {
                    $GLOBALS ['log']->debug ( $query );
                }
                if ($this->log) {
                    $time_start = microtime ( true );
                }


		if (isset ( $query )) {

			$res = $this->mysql_query_cache ( $query, false, $timeout * 3600 );

		} else {

			throw new DBInputOutputException ( 'No query given' );

		}

		if (! $res) {

			throw new DBQueryException ( mysql_error ( $this->conn_id ) . ' | Query: ' . $query );

		}

                if ($this->log) {

                    $this->query_count = ($this->query_count + 1);

                    $time_end = microtime ( true );

                    $time = ($time_end - $time_start) + 0;

                    $this->query_time = ($this->query_time + $time);

                    if ($this->text_log) {

                        $GLOBALS ['log']->debug ( $res . '(' . $time . ')');

                    }

                    if ($this->db_log  && ($time > $this->slow_query)) {
                        mysqli_query($con, "INSERT INTO db_log (query, query_time, query_duration, avg_duration, res, request_uri)
                            VALUES('".mysqli_real_escape_string($con, $query)."', now(), " . $time . ", " . $time . "," . (int) $res .", '" . $_SERVER['REQUEST_URI']."')
                                ON DUPLICATE KEY UPDATE cnt = cnt + 1, query_duration=query_duration + " . $time . ", avg_duration = ((query_duration + " . $time . " )/ (cnt + 1))");
                    }

                }

		return $res;

	}

	public function getCacheArray($sql, $validity = 12){

		$queryHash = md5($sql);

		$res = $this->query("SELECT * FROM `cache` WHERE `name` = '".$queryHash."'");


		if ($this->numRows($res) == 1){



			$row = $this->fetchArray($res);

			$timediff = (time()-$row['date'])/3600;

			if ($timediff < $validity){

				return unserialize($row['value']);

			}
			else {

				$res_q = $this->getArray($sql);
				$res_s = serialize ($res_q);
				$data = Array (

					'value' => $res_s,
					'date'	=> time(),

				);


				$this->update('cache',$data,"`name` = '".$queryHash."'");

				return unserialize($res_s);

			}

		}
		else {

			$res_q = $this->getArray($sql);

			$res_s = serialize ($res_q);

			$data = Array (

				'value' => $res_s,
				'date'	=> time(),
				'name'	=> $queryHash,

			);

			$this->insert('cache',$data);

			return unserialize($res_s);

		}

	}

	public function getCount() {

		return $this->query_count;

	}

	public function getTime() {

		return $this->query_time;

	}

	public function ins_string($string, $type = '') {

		$string = mysql_real_escape_string ( $string, $this->conn_id );

		switch ($type) {
			case "int" :
				$string = ($string != "") ? intval ( $string ) : NULL;
				break;
			case "float" :
				$string = ($string != "") ? str_replace ( ",", ".", $string ) : NULL;
				break;
			case "cb" :
				$string = ($string == "on" or $string == "1") ? 1 : 0;
				break;
			case "like" :
				$string = ($string != "") ? "'%" . $string . "%'" : "''";
				break;
			case "postcode" :
				$string = ($string != "") ? "'" . $string . "%'" : "''";
				break;
			default :
				$string = ($string != "") ? "'" . $string . "'" : "''";
		}
		return $string;

	}

        # Gets key / value pair into memcache ... called by mysql_query_cache()
        protected function getCache($key) {
            return ($this->memcache) ? $this->memcache->get($key) : false;
        }

        # Puts key / value pair into memcache ... called by mysql_query_cache()
        protected function setCache($key,$object,$timeout = 60) {
            return ($this->memcache) ? $this->memcache->set($key,$object,MEMCACHE_COMPRESSED,$timeout) : false;
        }

        # Caching version of mysqli_query($con)
        protected function mysql_query_cache($sql,$linkIdentifier = false,$timeout = 60) {
            $hash = md5("mysql_query" . $sql);
            $cache = $this->getCache($hash);
            if ($cache) {
                return $cache;
            } else {
                $r = ($linkIdentifier !== false) ? mysqli_query($con, $sql,$linkIdentifier) : mysqli_query($con, $sql);
                while($row=mysqli_fetch_array($r))
                $result[]=$row;
                $this->setCache($hash,$result,$timeout);
            }
            return $result;
        }

}

?>
