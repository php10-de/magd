<?php

$date = '2021-01-01';
$sql[] = "DELETE FROM log WHERE dbupdate < '".$date."'";
$sql[] = "DELETE FROM dict_changelog WHERE dbupdate < '".$date."'";

?>
