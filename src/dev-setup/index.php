<?php
//define('__ROOT__', dirname(dirname(__FILE__)));

session_name("hrose");
session_start();

if (isset($_REQUEST['m'])) {
    $_SESSION['m'] = true;
}

if(isset($_SESSION['logedin']) || isset($_COOKIE['logedin'])) {
    header('Location:start.php');
}else {
	$loc = (strpos($_SERVER['HTTP_HOST'],'snacks.hrose') !== false)?'snack_register.php':'magd.php';
    header('Location:'.$loc);
}
?>
