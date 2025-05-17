<?php

global $_VALID;

$commandOptions = [];

//Attribute
if(isset($param[0])){
    $commandOptions['attribute'] = $param[0];
}else{
    throw new Exception('Which attribute to make editable in list?');
}

//Entity 
if(isset($param[1])){
    $commandOptions['entity'] = $param[1];
}else{
    validate('entity', 'string');
    if (!isset($_VALID['entity'])) {
         throw new Exception('In which entity to make attribute editable in list?');
     }
     $commandOptions['entity'] = $_VALID['entity'];
}

//Check if attribute exists IN ENTITY AND IF IT IS THE TYPE THAT NEEDED
$inCheckAttrSQL = "SHOW COLUMNS FROM `" . mysqli_real_escape_string($con, $commandOptions['entity']) . "` LIKE '" . mysqli_real_escape_string($con, $commandOptions['attribute']) . "';";
$inCheckAttrRES = mysqli_query($con, $inCheckAttrSQL);
if ($inCheckAttrRES && mysqli_num_rows($inCheckAttrRES) === 1) {
    if(isset($commandType)){
        $entityAttributeType = mysqli_fetch_array($inCheckAttrRES)['Type'];
        if (strpos($entityAttributeType, "varchar") === false) {
            if (strpos($entityAttributeType, "int") === false) {
                throw new Exception(ss('attribute') . ' ' . $commandOptions['attribute'] . ' ' . ss('is not the proper type') . ' ' . ss('in') . ' ' . $commandOptions['entity']);
            }
        }
    }
}else{
    throw new Exception(ss('attribute') . ' ' . $commandOptions['attribute'] . ' ' . ss('does not exists') . ' ' . ss('in') . ' ' . $commandOptions['entity']);
}

$entityName =  urlencode(str_replace(' ', '_', mb_strtolower($commandOptions['entity']))) ;
$attributeName = urlencode(str_replace(' ', '_', mb_strtolower($commandOptions['attribute'])));

$sql = '';

if(isset($activateFeature)){
    $sql = "REPLACE INTO red_button_conf ( red_button_entity_name, red_button_data_name, param, value) VALUES ( '" . mysqli_real_escape_string($con, $entityName) . "','"
    . mysqli_real_escape_string($con, $attributeName) . "','listedit', 1 );";
}else{
    $sql = "DELETE FROM red_button_conf WHERE red_button_entity_name = '". mysqli_real_escape_string($con, $entityName) ."' AND red_button_data_name = '".mysqli_real_escape_string($con, $attributeName)."' AND param = 'listedit';";
}

if (!mysqli_query($con, $sql)) {
    throw new Exception('show / hide attribute sum failed with ' . $con->error);
}


$loc       = 'bigredbutton.php';
$nextParam = brb_param($entityName, 'form');
nextHeader($loc, $nextParam, true);

$loc       = 'bigredbutton.php';
$nextParam = brb_param($entityName, 'list');
nextHeader($loc, $nextParam, true);

$nextPage = HTTP_HOST . $entityName . '.php';
$showMessage = ss('Making ') . $commandOptions['attribute'] . (($activateFeature)?' ':' not ') . ss('editable in list view');