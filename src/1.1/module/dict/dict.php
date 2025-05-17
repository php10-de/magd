<?php
if (isset($_REQUEST['refresh'])) {
    // Cache
    $csql = "SELECT * FROM dict WHERE 1=1";
    $cresult = mysqli_query($con, $csql);
    while ($row = mysqli_fetch_array($cresult)) {
        $de[] = "'".$row['ID']."' => '".addslashes($row['de'])."'";
        $en[$row['ID']] = $row['en'];
        $gr[$row['ID']] = $row['gr'];
    }

    $s = '<?php ';
    $s .= '$DE = array(';
    $s .= implode(",",$de);
    $s .= ");";
    $s .= ' ?>';
    $fp = fopen('inc/de.inc.php', 'w');
    fwrite($fp, $s);
    fclose($fp);
    //file_put_contents('inc/de.inc.php', $s);
}