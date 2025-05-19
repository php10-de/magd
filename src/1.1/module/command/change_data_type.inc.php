<?php

global $_VALID;

// Get the entity
validate('entity', 'string');
if (!isset($_VALID['entity'])) {
    throw new Exception('Which entity to work on?');
}
$entityName = $_VALID['entity'];

//Get type to change
if (isset($param[0])) {

    $col_to_change = $param[0];

    $into_type = mb_strtolower($param[1]);
}

$commandOptions = [];
validate('confirmed', 'enum', ['true','false']);
$commandOptions['confirmed'] = $_VALID['confirmed'] ? $_VALID['confirmed'] : null;

// Get current type of column
$col_type_sql = "SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . mysqli_real_escape_string($con, $entityName) . "' AND COLUMN_NAME = '" . mysqli_real_escape_string($con, $col_to_change) . "';";

if (COMMAND_DEBUG_MODE == true) {
    error_log($col_type_sql);
}

$col_typeRes = mysqli_query($con, $col_type_sql);
$col_type = mysqli_fetch_row($col_typeRes);

if (isset($col_type[0])) {

    $type_to_change = isset($DATA_TYPES[$col_type[0]]) ? $DATA_TYPES[$col_type[0]] : null;

    $type_to_change_into = isset($DATA_TYPES[$into_type]) ? $DATA_TYPES[$into_type] : null;;

    // Confirmation message
    if ($type_to_change !== $type_to_change_into) {

        $showMessage = '<p>' . ss('You are going to change the attribute') . ' ' . $col_type[0] . ' into ' . $into_type . '!</p>';
        $showMessage .= '<p>' . ss('All of your data from ') . ' ' . $col_to_change . ' ' . ss('will be lost!') . '</p>';
    }
    else {
        $showMessage = '<p>' . ss('Are you sure you want to change the attribute of ') . ' ' . $col_to_change . ' into ' . $into_type . '?</p>';
        $showMessage .= '<p>' . ss('Your data from ') . ' ' . $col_to_change . ' ' . ss('migh be lost!') . '</p>';
    }
}
else {

    throw new Exception('Could not get ' . $col_to_change . ' attribute. Failed with ' . mysqli_error($con));
}

if($commandOptions['confirmed'] !== 'true'){
    $nextPage = "__CONFIRMATION__";
}
else{

    mysqli_query($con, "SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

    // Create backup column with new type
    $backupSql = "ALTER TABLE " . mysqli_real_escape_string($con, $entityName) . " ADD COLUMN tmp_" . mysqli_real_escape_string($con, $col_to_change) . " " . $col_type[1] . " AFTER " . mysqli_real_escape_string($con, $col_to_change);

    if (COMMAND_DEBUG_MODE == true) {
        error_log($backupSql);
    }

    if (!mysqli_query($con, $backupSql)) {
        throw new Exception('Could not create backup. Failed with ' . mysqli_error($con));
    }

    // Save data to backup column
    $saveBackupDataSql = "UPDATE " . mysqli_real_escape_string($con, $entityName) . " SET tmp_" . mysqli_real_escape_string($con, $col_to_change) . " = " . mysqli_real_escape_string($con, $col_to_change) . " WHERE " . mysqli_real_escape_string($con, $col_to_change) . " IS NOT NULL;";

    if (COMMAND_DEBUG_MODE == true) {
        error_log($saveBackupDataSql);
    }

    if (!mysqli_query($con, $saveBackupDataSql)) {
        throw new Exception('Could not save backup data. Failed with ' . mysqli_error($con));
    }

    // Delete the original data
    $updateSql = 'UPDATE ' . mysqli_real_escape_string($con, $entityName) . ' SET ' . mysqli_real_escape_string($con, $col_to_change) . ' = NULL WHERE ' . mysqli_real_escape_string($con, $col_to_change) . ' IS NOT NULL;';

    if (COMMAND_DEBUG_MODE == true) {
        error_log($updateSql);
    }

    if (!mysqli_query($con, $updateSql)) {
        throw new Exception('Could not delete column data. Failed with ' . mysqli_error($con));
    }

    // Change column type to the new desired type
    $changeSql = 'ALTER TABLE ' . mysqli_real_escape_string($con, $entityName) . ' MODIFY ' . mysqli_real_escape_string($con, $col_to_change) . ' ' . $attributeType . ';';

    if (COMMAND_DEBUG_MODE == true) {
        error_log($changeSql);
    }

    if (!mysqli_query($con, $changeSql)) {
        throw new Exception('Changing column type failed with ' . mysqli_error($con));
    }

    // Save data to backup column
    $saveOldBackupDataSql = "UPDATE " . mysqli_real_escape_string($con, $entityName) . " SET " . mysqli_real_escape_string($con, $col_to_change) . " = tmp_" . mysqli_real_escape_string($con, $col_to_change) . " WHERE tmp_" . mysqli_real_escape_string($con, $col_to_change) . " IS NOT NULL;";

    if (COMMAND_DEBUG_MODE == true) {
        error_log($saveOldBackupDataSql);
    }

    if (!mysqli_query($con, $saveOldBackupDataSql)) {
        throw new Exception('Could not save original data. Failed with ' . mysqli_error($con));
    }

    // Delete the old column
    $deleteBackupSql = "ALTER TABLE " . mysqli_real_escape_string($con, $entityName) . " DROP COLUMN tmp_" . mysqli_real_escape_string($con, $col_to_change);

    if (COMMAND_DEBUG_MODE == true) {
        error_log($deleteBackupSql);
    }

    if (!mysqli_query($con, $deleteBackupSql)) {
        throw new Exception('Could not delete backup column. Failed with ' . mysqli_error($con));
    }

    // Change column name for input file type
    if ($is_media === true) {

        $changeColNameSql = 'ALTER TABLE ' . mysqli_real_escape_string($con, $entityName) . ' CHANGE ' . mysqli_real_escape_string($con, $col_to_change) . ' ' . mysqli_real_escape_string($con, $col_to_change) . '_media VARCHAR(255);';

        if (COMMAND_DEBUG_MODE == true) {
            error_log($changeColNameSql);
        }

        if (!mysqli_query($con, $changeColNameSql)) {
            throw new Exception('Changing column type file failed with ' . mysqli_error($con));
        }
    }
    // If it was input type file, change it to normal
    else if ($is_media === false && strpos($col_to_change, '_media') !== false) {

        $col_to_change = str_replace('_media', '', $col_to_change);

        $changeColNameSql = 'ALTER TABLE ' . mysqli_real_escape_string($con, $entityName) . ' CHANGE ' . mysqli_real_escape_string($con, $col_to_change) . '_media ' . mysqli_real_escape_string($con, $col_to_change) . ' ' . mysqli_real_escape_string($con, $attributeType) . ';';

        if (COMMAND_DEBUG_MODE == true) {
            error_log($changeColNameSql);
        }

        if (!mysqli_query($con, $changeColNameSql)) {
            throw new Exception('Changing column from type file to ' . $into_type . ' failed with ' . mysqli_error($con));
        }
    }

    $loc       = 'bigredbutton.php';
    $nextParam = brb_param($entityName, 'form');
    nextHeader($loc, $nextParam, true);

    $loc       = 'bigredbutton.php';
    $nextParam = brb_param($entityName, 'list');
    nextHeader($loc, $nextParam, true);

    $nextPage = HTTP_HOST . $entityName . '.php';
    $showMessage = ss('Column') . ' ' . $col_to_change . ' ' . ss('attribute changed');

    $nextPage = HTTP_HOST . $entityName . ( $commandOptions['screenType'] === 'detail' ? '_d' : '' ) . '.php';
    $showMessage = ss('Column') . ' ' . $col_to_change . ' ' . ss('attribute changed');
}