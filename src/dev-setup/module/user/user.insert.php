<?php


// Default
$sql1001 = "INSERT INTO user2gr(user_id, gr_id) VALUES(" . (int) $_VALID['user_id'] . ", 1001)";
mysqli_query($con, $sql1001);
// Registriert
$sql21 = "INSERT INTO user2gr(user_id, gr_id) VALUES(" . (int) $_VALID['user_id'] . ", 21)";
mysqli_query($con, $sql21);

if (GR(1025) && !GR(1)) {
    // Musik-Eltern
    $sql21 = "INSERT INTO user2gr(user_id, gr_id) VALUES(" . (int) $_VALID['user_id'] . ", 1026)";
    mysqli_query($con, $sql21);
}