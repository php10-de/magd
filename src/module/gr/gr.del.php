<?php
// For Administrators only

// don't delete the admin group
if ($VALID['id'] == 1) die('');

$sql = "DELETE FROM right2gr WHERE gr_id != 1 AND gr_id = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM dict WHERE gr_id != 1 AND gr_id = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM user2gr WHERE AND gr_id1 = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM gr2gr WHERE AND gr_id1 = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM gr WHERE gr_id != 1 AND gr_id = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$_SESSION['gr']['rl'] = true;