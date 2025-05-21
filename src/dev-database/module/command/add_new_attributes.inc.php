<?php
global $_VALID;

$commandOptions = [];

$commandOptions['commandType'] = 'default';
$commandOptions['attributeType'] = null;
$commandOptions['after'] = [];
$commandOptions['entity'] = $_SESSION['entity'];
$commandOptions['attribute'] = mb_strtolower($param[0]);
validate('type', 'enum', ['list','detail']);
$commandOptions['screenType'] = $_VALID['type'] ? $_VALID['type'] : null;

$ATTRIBUTES = array(
    'file'          => 'VARCHAR(255)',
    'shorttext'     => 'VARCHAR(255)',
    'bool'          => 'TINYINT(1)',
    'integer'       => 'INT',
    'double'        => 'DOUBLE',
    'number'        => 'DOUBLE',
    'date'          => 'DATETIME',
    'enum'          => 'ENUM',
    'multiselect'   => 'SET'
);

if (!isset($_SESSION['entity'])) {
    throw new Exception('Which entity to work on?');
}

$sql =  'ALTER TABLE ' . $commandOptions['entity'] . ' ';

// Check if we have to add the new columns after an existing column
if (isset($param['attributes-types']) && count($param['attributes-types']) > 0) {

    $commandOptions['after'] = array_filter($param['attributes-types'], function($element) {return mb_strtolower($element) == 'after'; });
}

if (isset($param['attributes']) && count($param['attributes']) > 0) {

    foreach ($param['attributes'] as $attr_key => $attribute) {

        $col_name = mysqli_real_escape_string($con, $attribute);
        $not_null = ' NOT NULL ';

        // Set attribute type
        $attribute_type = ((isset($param['attributes-types'][$attr_key]) && isset($ATTRIBUTES[mb_strtolower($param['attributes-types'][$attr_key])])) ? mysqli_real_escape_string($con, $ATTRIBUTES[mb_strtolower($param['attributes-types'][$attr_key])]): "VARCHAR(255)");

        // If attribute type is file, make necessary changes
        if (mb_strtolower($param['attributes-types'][$attr_key]) == 'file') {

            $col_name .= '_media';
            $not_null = 'NULL';
        }

        // add ENUM options
        $enum_values = '';
        if (isset($param['with']) && isset($param['with'][$attr_key])) {

            $enum_values = "('" . implode("', '", $param['with'][$attr_key]) . "')";
        }

        // Add after colummn option
        if (isset($commandOptions['after']) && isset($commandOptions['after'][$attr_key + 1])) {

            $sql .= 'ADD COLUMN `' . $col_name . '` ' . $attribute_type . ' ' . $enum_values . ' ' . $not_null . ' AFTER ' . mysqli_real_escape_string($con, $param['attributes'][$attr_key +1]) . ' ' . (($attr_key < (count($param['attributes']) -2)) ? ", ": "");

        }
        // Add normal column
        else if (mb_strtolower($param['attributes-types'][$attr_key]) !== 'after') {

            $sql .= 'ADD COLUMN `' . $col_name . '` ' . $attribute_type . ' ' . $enum_values . ' ' . $not_null . (($attr_key < (count($param['attributes']) -1)) ? ", ": "");
        }
    }

    if (!mysqli_query($con, $sql)) {
        throw new Exception('add attributes failed with ' . mysqli_error($con));
    }
}
else {
    throw new Exception('There are no attributes to be added.');
}

$loc       = 'bigredbutton.php';
$nextParam = brb_param($commandOptions['entity'] , 'form');
nextHeader($loc, $nextParam, true);

$loc       = 'bigredbutton.php';
$nextParam = brb_param($commandOptions['entity'] , 'list');
nextHeader($loc, $nextParam, true);

$nextPage = HTTP_HOST . $commandOptions['entity'] . ( $commandOptions['screenType'] === 'detail' ? '_d' : '' ) . '.php';
$showMessage = ss('Attribute') . ' ' . $commandOptions['attribute']  . ' ' . ss('added');