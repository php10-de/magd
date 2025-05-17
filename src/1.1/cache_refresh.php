<?php
$modul="cache";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to change cache
RR(2);

if (!$headless) require("inc/header.inc.php");

$sql = "SELECT cache.cache_id, cache.url, cache.updated, cache.active FROM cache WHERE active = 1";
$listResult = mysqli_query($con, $sql);
require_once 'inc/fsc.class.php';

$cache = new Fsc(false);

$cache->resetAllAction();
$cache->permanentAction();

while($row = mysqli_fetch_array($listResult)) {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $row['url'];

    $ch = curl_init(); /// initialize a cURL session
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $cUrlResponse = curl_exec($ch);
    $httpResponseArr = curl_getinfo($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (404 == $httpCode) {
        echo $url . ' nicht gefunden.<br>';
    }
    curl_close($ch);
}
$cache->stopAction();

echo '<br><a href="/cache.php">Zurück zur Liste</a>';

require("inc/footer.inc.php");
?>