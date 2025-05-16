<?php
require_once(__DIR__ . "/hrose_ini.php");
if (file_exists(INC_ROOT."cache_start.php")) {
    require_once(INC_ROOT."cache_start.php");
}
ini_set('error_reporting',E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
global $con, $_VALID, $_VALIDDB, $db_name;
require_once(realpath(dirname(__FILE__)) . "/../db/db_connect.dist.php");
date_default_timezone_set("Europe/Berlin");
//session_name("hrose");
session_start();
//session_regenerate_id(true);
if (isset($_REQUEST['extern'])) {
    $_SESSION['extern'] = 1;
}
define('EXTERN',isset($_SESSION['extern']));
require_once(INC_ROOT."functions.php");

//Frei zugängliche Seite ohne eingeloggt zu sein        ---------//

$open_site = array("login","register","team","forgot_pw","api","tech","cron","healthcheck");

// Seiten die eingeloggten Zustand erforden, Berechtigungen prüfen in verify.php        ---------//

if(isset($modul) && !in_array($modul,$open_site)) {
    require_once(INC_ROOT."verify.php");
}

if (isset($_REQUEST['ace']) && GR(3)) {
    $ACE_FILE= basename($_SERVER['PHP_SELF']);
    require INC_ROOT . 'ace.php';
    exit;
}

require_once(INC_ROOT."version.php");
require_once(DOC_ROOT."db/SQLHistory.php");