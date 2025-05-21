<?php

global $_VALID;

// Get the entity
validate('entity', 'string');
if (!isset($_VALID['entity'])) {
    throw new Exception('Which entity to work on?');
}
$entityName = $_VALID['entity'];

//Get col to move
if (isset($param[0])) {

    $col_to_move = $param[0];
    $col_move_after = $param[1];
}

$commandOptions = [];
validate('confirmed', 'enum', ['true','false']);
$commandOptions['confirmed'] = $_VALID['confirmed'] ? $_VALID['confirmed'] : null;

$showMessage = ss('Are you sure that you want to move') . ' ' . $col_to_move . ' ' . ss('after') . ' ' . $col_move_after . ss('?');

if($commandOptions['confirmed'] !== 'true'){
    $nextPage = "__CONFIRMATION__";
}else{

    $col_type_sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . mysqli_real_escape_string($con, $entityName) . "' AND COLUMN_NAME = '" . mysqli_real_escape_string($con, $col_to_move) . "';";

    $col_typeRes = mysqli_query($con, $col_type_sql);
    $col_type = mysqli_fetch_row($col_typeRes);

    if (isset($col_type[0])) {

        $sql = 'ALTER TABLE ' . mysqli_real_escape_string($con, $entityName) . ' CHANGE COLUMN ' . mysqli_real_escape_string($con, $col_to_move) . ' ' . mysqli_real_escape_string($con, $col_to_move) . ' ' . $col_type[0] .' AFTER ' . mysqli_real_escape_string($con, $col_move_after) . ';';
    }
    else {

        throw new Exception('Could not get ' . $col_to_move . ' attribute. Failed with ' . mysqli_error($con));
    }

    if (!mysqli_query($con, $sql)) {
        throw new Exception('Moving columns failed with ' . mysqli_error($con));
    }

    $loc       = 'bigredbutton.php';
    $nextParam = brb_param($entityName, 'form');
    nextHeader($loc, $nextParam, true);

    $loc       = 'bigredbutton.php';
    $nextParam = brb_param($entityName, 'list');
    nextHeader($loc, $nextParam, true);

    $nextPage = HTTP_HOST . $entityName . '.php';
    $showMessage = ss('Column') . ' ' . $col_to_move . ' ' . ss('moved');

    $nextPage = HTTP_HOST . $entityName . ( $commandOptions['screenType'] === 'detail' ? '_d' : '' ) . '.php';
    $showMessage = ss('Column') . ' ' . $col_to_move . ' ' . ss('moved');
}