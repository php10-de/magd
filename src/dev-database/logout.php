<?php

require("inc/req.php");

$_SESSION["logedin"]=false;
unset($_COOKIE['logedin']);
setcookie('logedin', '', 1, '/');
session_destroy();

header('Location: index.php');

?>