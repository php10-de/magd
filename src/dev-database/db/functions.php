<?php

function memcacheArray($query, $linkIdentifier = false, $timeout = 86400) {
    if(!isset($query)){
        return null;
    }
    global $memcache, $con;
    if (!$memcache) {
        $r = ($linkIdentifier !== false) ? mysqli_query($con, $query,$linkIdentifier) : mysqli_query($con, $query);
        while($row=mysqli_fetch_array($r))
            $result[]=$row;
        return $result;
    }
    $hash = md5("mysql_query" . $query);
    $cache = getMemCache($hash);
    if ($cache) {
        return $cache;
    } else {
        $r = ($linkIdentifier !== false) ? mysqli_query($con, $query,$linkIdentifier) : mysqli_query($con, $query);
        while($row=mysqli_fetch_array($r))
            $result[]=$row;
        setMemCache($hash,$result,$timeout);
    }
    return $res;

}

# Gets key / value pair into memcache ... called by mysql_query_cache()
function getMemCache($query) {
    global $memcache;
    if (!$memcache) return false;
    $prefix = 'mysql_query';
    $hash = md5($prefix . $query);
    return ($memcache) ? $memcache->get($hash) : false;
}

# Puts key / value pair into memcache ...
function setMemCache($query,$object,$timeout = 86400) {
    global $memcache;
    $prefix = 'mysql_query';
    $hash = md5($prefix . $query);
    return ($memcache) ? $memcache->set($hash,$object,MEMCACHE_COMPRESSED,$timeout) : false;
}

function translate($field , $id)
{
	$query = "select " . $field . " as 'name' from dict where ID = '" . $id . "'";
	$result = mysqli_query($con, $query);
	$result = mysqli_fetch_array($result);
	return $result['name'];
}

?>
