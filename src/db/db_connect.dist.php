<?php
$db_host = "127.0.0.1";
$db_user = "appuser";
$db_pw = "apppass";
$db_name = "appdb";

$con = mysqli_connect($db_host, $db_user, $db_pw) or  die('Verbindung nicht m&ouml;glich : ' . mysqli_error($con));
mysqli_select_db($con, $db_name) or die ('Kann '.$db_name.' nicht benutzen : ' . mysqli_error($con));
mysqli_query($con, 'set character set utf8;');

if (defined('MEMCACHE') AND MEMCACHE == 1) {
    $memcache = new Memcache;
    $memcache->connect('localhost', 11211);
}
include 'functions.php';
?>
