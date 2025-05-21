<?php

if (isset($_REQUEST['to_nav_id'])) {
    $sql = "SELECT level FROM nav WHERE nav_id=" . (int) $_REQUEST['to_nav_id'];
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($res);
    $_VALID['parent_level'] = $row[0];
    $_VALIDDB['parent_level'] = (int) $row[0];
}
