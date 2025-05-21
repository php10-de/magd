<?php

// For Managers only
GRGR(6);
// Only for people with the right to delete users
RR(3);

$sql = "DELETE FROM right2user WHERE user_id != 1 AND user_id = " . (int) $_VALID['user_id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM user2gr WHERE gr_id != 1 AND user_id != 1 AND user_id = " . (int) $_VALID['user_id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM gr WHERE gr_id != 1 AND gr_id = " . (int) $_VALID['user_id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM user WHERE user_id != 1 AND user_id = " . (int) $_VALID['user_id'];
mysqli_query($con, $sql) or print(mysqli_error());

$_SESSION['user']['rl'] = true;