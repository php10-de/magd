<?php

$checkDbSql = 'SELECT 1 FROM red_button LIMIT 1';
try {
    $checkDbRes = mysqli_query($con, $checkDbSql);
} catch (Exception $e) {
    $sqlFile = DATA_ROOT . 'seed/magd_structure.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        mysqli_multi_query($con, $sql);
        do {
            if ($res = mysqli_store_result($con)) {
                mysqli_free_result($res);
            }
        } while (mysqli_next_result($con));
    } else {
        die('Database structure file not found.');
    }

    $sqlFile = DATA_ROOT . 'seed/magd_data.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        mysqli_multi_query($con, $sql);
        do {
            if ($res = mysqli_store_result($con)) {
                mysqli_free_result($res);
            }
        } while (mysqli_next_result($con));
    } else {
        die('Database data file not found.');
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}