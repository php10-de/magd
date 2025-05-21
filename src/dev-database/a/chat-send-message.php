<?php

$modul="chat";

require("../inc/req.php");

// For Admins only
GRGR([1001, 1003]);

$_ENV['OPENAI_API_KEY'] = OPENAI_API_KEY;
putenv('OPENAI_API_KEY='.OPENAI_API_KEY);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['chatid'];
    $msg = $_POST['msg'];
    $ai = $_POST['ai'];

    // Prepare the INSERT statement
    $stmt = "INSERT INTO chat_history (user_id, ai_id, session_id, human, cdate) VALUES ("
        . (int) $_SESSION['user_id'] . ","
        . (int) $ai . ","
        . "'" . mysqli_real_escape_string($con, $id) . "',"
        . "'" . mysqli_real_escape_string($con, $msg) . "',"
        . " NOW())";
    $res = mysqli_query($con, $stmt) or die(mysqli_error($con));

    //
    // Close the database connection
    // Set the HTTP response header to indicate that the response is JSON
    header('Content-Type: application/json');

    // data
    $data = [
        "id" => mysqli_insert_id($con)
    ];

    // Convert the chat history array to JSON and send it as the HTTP response body
    echo json_encode($data);
}
