<?php

function basicConvert($table, $id, $dropdown = false, $nameColumn = 'name', $filter = null, $userGroup = 1, $where=null) {
    global $con;

    if (file_exists(MODULE_ROOT . $table . '.php')) {
        include_once(MODULE_ROOT . $table . '.php');
        $convertFunction = $table.'Convert';
        if (function_exists($convertFunction) )
        return $convertFunction($id, $dropdown, $userGroup, $where);
    }

    // do nothing for people without rights
    if (!GR($userGroup)) {
        return false;
    }
    
    $idName = $table.'_id';
    $sql = "SELECT " . $idName.", ".$nameColumn." FROM ".$table." WHERE ";

    if (!$filter) {
        $sql .= "1=1";
    } else {
        $fr = array();
        foreach ($filter as $key => $value) {
            $fr[] .= "$key = $value";
        }
        $sql .= implode (' AND ', $fr);
    }
    if($where)
        $sql.=" AND $where ";
    $sql .= " ORDER BY " . $nameColumn;

    if (IS_MEMCACHE) $listResult = getMemCache($sql);
    if (!$listResult) {
        $r = mysqli_query($con, $sql) or die(mysqli_error($con));
        while($row=mysqli_fetch_array($r))
            $listResult[$row[$idName]]=$row;
        if (IS_MEMCACHE) {
            setMemCache($sql, $listResult);
        }
    }
    if($dropdown && 'autocomplete' !== $dropdown) {
        $return = "<option value='NULL' ".((!$id)?"selected='selected'":"")."> - </option>";
        foreach ($listResult as $row) {
            $return .= "<option value='".$row[$idName]."'".(($id==$row[$idName])?"selected='selected'":"").">".ss($row[$nameColumn])."</option>";
        }
    } else if('autocomplete' == $dropdown) {
        $returnArr = [];
        if ($listResult) {
            foreach ($listResult as $row) {
                $returnArr[] = ['label' => $row[$nameColumn], 'value' => $row[$idName]];
            }
        }
        $return = json_encode( $returnArr );
    } else
        $return = ss($listResult[$id][$nameColumn]);
    return $return;
}

function languageConvert($id, $dropdown = false) {

    if($dropdown) {

        $return = "<option value='DE' ".(($id=='DE')?"selected='selected'":"").">DE</option>";
        $return.= "<option value='EN' ".(($id=='EN')?"selected='selected'":"").">EN</option>";
        $return.= "<option value='GR' ".(($id=='GR')?"selected='selected'":"").">GR</option>";


    }else {

        switch($id) {
            case 'EN':$return ="EN";
                break;
            case 'DE':$return ="DE";
                break;
            case 'GR':$return ="GR";
                break;
        }

    }

    return $return;
}

function groupConvert($id, $dropdown = false) {
    global $con;

    // do nothing for people without right to manage groups
    if (!R(2)) {
        return false;
    }
    $sql = "SELECT gr_id, shortname FROM gr WHERE ";
    // Administrators see all groups
    if (GR(1)) {
        $sql .= "1=1";
    } else {
        $sql .= "gr_id != 1";
    }
    $listResult = getMemCache($sql);
    if (!$listResult) {
        $r = mysqli_query($con, $sql) or die(mysqli_error($con));
        while($row=mysqli_fetch_array($r))
            $listResult[$row['gr_id']]=$row;
        if ($memcache) {
            setMemCache($sql, $listResult);
        }
    }
    if($dropdown) {
        $return = "<option value='NULL' ".((!$id)?"selected='selected'":"")."> - </option>";
        foreach ($listResult as $row) {
            $return .= "<option value='".$row['gr_id']."'".(($id==$row['gr_id'])?"selected='selected'":"").">".ss($row['shortname'])."</option>";
        }
    } else 
        $return = ss($listResult[$id]['shortname']);
    return $return;
}

function navConvert($id, $dropdown = false) {
    global $con;

    // do nothing for people without right to change navigation
    if (!R(8)) {
        return false;
    }
    $sql = "SELECT nav_id, name FROM nav WHERE ";
    // Administrators see all groups
    if (GR(1)) {
        $sql .= "1=1";
    } else {
        $sql .= "gr_id != 1";
    }
    $listResult = getMemCache($sql);
    if (!$listResult) {
        $r = mysqli_query($con, $sql) or die(mysqli_error($con));
        while($row=mysqli_fetch_array($r))
            $listResult[$row['nav_id']]=$row;
        if ($memcache) {
            setMemCache($sql, $listResult);
        }
    }
    if($dropdown) {
        $return = "<option value='NULL' ".((!$id)?"selected='selected'":"")."> - </option>";
        foreach ($listResult as $row) {
            $return .= "<option value='".$row['nav_id']."'".(($id==$row['nav_id'])?"selected='selected'":"").">".ss($row['name'])."</option>";
        }
    } else
        $return = ss($listResult[$id]['shortname']);
    return $return;
}

?>
