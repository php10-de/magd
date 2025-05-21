<?php
namespace api\v1;
use app\hrose\APIv1;
use app\hrose\Autoloader;

$modul = 'api';
require("../../inc/req.php");
require("../../app/hrose/autoloader.php");
Autoloader::register();
try {
ini_set('display_errors', 'on');
    $api = new APIv1($_REQUEST, $_SERVER["HTTP_REFERER"]);
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
//header("Content-Type: application/json; charset=UTF-8");