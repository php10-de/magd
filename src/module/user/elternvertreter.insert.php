<?php

$checkGEV = "SELECT parent_id FROM parent WHERE user=" .  (int) $_VALID['user_id'];
$checkRes = mysqli_query($con, $checkGEV) or die('check elternvertreter insert failed.');
$checkRow = mysqli_fetch_row($checkRes);
if (! $_VALID['user_id']) {
    die('user id missing in elternvertreter insert');
}
if (count($checkRow)) {
    $sqlGEV = "UPDATE parent SET elternvertreter = " . $_VALIDDB['klasse_id'] . " WHERE parent_id=" . $checkRow[0];
} else {
    $sqlGEV = "INSERT INTO parent(name, user, elternvertreter) 
VALUES('" . $_VALID['firstname'] . " " . $_VALID['lastname'] . "'," . (int) $_VALID['user_id'] . ", " . $_VALIDDB['klasse_id'] . ")";
}

mysqli_query($con, $sqlGEV) or die('elternvertreter insert failed.' . mysqli_error($con));
