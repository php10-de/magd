<?php

// For people with the right to change rights only
RR(4);

$sql = "DELETE FROM right2gr WHERE gr_id != 1 AND right_id = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());

$sql = "DELETE FROM r WHERE right_id = " . $_VALID['id'];
mysqli_query($con, $sql) or print(mysqli_error());
$_SESSION['r']['rl'] = true;