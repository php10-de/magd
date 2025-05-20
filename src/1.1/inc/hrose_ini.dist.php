<?php

if (file_exists(__DIR__ . '/settings.inc.php')) include __DIR__ . '/settings.inc.php';

if (file_exists(__DIR__ . '/pre.config.inc.php')) include __DIR__ . '/pre.config.inc.php';

if (isset($argv[1])) {
    $param = explode('/', $argv[1]);
    if (defined('CRONTOKEN') && isset($param[0]) AND (CRONTOKEN == $param[0])) {
        $_SERVER['HTTP_HOST'] = $HTTP_HOST;
        if (!defined('__DIR__')) {
            define('__DIR__', $DIR);
        }
        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';
        define('CRONRUN', true);
    } else {
        define('CRONRUN', false);
    }
} else {
    define('CRONRUN', false);
}

defined('HOVERCOLOR') || define('HOVERCOLOR','#d6e8ff'); //Tabellenfarbe bei Hover
defined('col1') || define('col1','#FFFFFF'); //Tabellenfarbe 1
defined('col2') || define('col2','#EEEEEE'); //Tabellenfarbe 2
defined('LOGOUT_TIME') || define('LOGOUT_TIME','10'); // Nach x Stunden ohne Aktivität -> Logout // bsp. 0.1 = 6min
defined('STANDARD_LIMIT') || define('STANDARD_LIMIT','50');  //Datensätze pro Seite - darf nie 0 sein
defined('SHORTVIEW_NUM') || define('SHORTVIEW_NUM','1000000000'); //Datensätze pro Seite - darf nie 0 sein
ini_set('display_errors',1);
ini_set('session.gc_maxlifetime', 1440000);
defined('IS_MEMCACHE') || define('IS_MEMCACHE', false);
defined('DEFAULT_LANG') || define('DEFAULT_LANG','en');

$with_slashes = true; // Validates variables for database with apostrophs
if (isset($_SERVER['SERVER_NAME']) AND $_SERVER['SERVER_NAME'] === 'localhost') {
    defined('SSL') || define('SSL', false);
    defined('SUBDIR') || define('SUBDIR', '');
    defined('DOC_ROOT') || define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/' . SUBDIR);
    defined('HTTP_SUB') || define('HTTP_SUB', '/' . SUBDIR);
} elseif (!$_SERVER['DOCUMENT_ROOT']) {
    // for phpunit
    defined('HROSE_PHPUNIT_TEST_MODE') || define('HROSE_PHPUNIT_TEST_MODE', true);
    defined('HTTP_HOST') || define('HTTP_HOST', isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'http://localhost:8888');
    defined('SSL') || define('SSL', false);
    defined('SUBDIR') || define('SUBDIR', '');
    defined('DOC_ROOT') || define('DOC_ROOT', dirname(__DIR__) . '/' . SUBDIR);
    defined('HTTP_SUB') || define('HTTP_SUB', '/' . SUBDIR);
} else {
    defined('SSL') || define('SSL', true);
    defined('SUBDIR') || define('SUBDIR', '');
    defined('DOC_ROOT') || define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
    defined('HTTP_SUB') || define('HTTP_SUB', '/');
}

defined('TITLE') || define('TITLE','MAGD'); //Title
defined('LOGO') || define('LOGO', HTTP_SUB.'assets/images/magd-logo.png');
defined('SSL') || define('SSL', true);

defined('HTTP_HOST') || define('HTTP_HOST', isset($_SERVER['HTTP_HOST'])?'http' . ((!defined('SSL') || !SSL)?'':'s').'://'.$_SERVER['HTTP_HOST'].HTTP_SUB:null);
defined('API_ROOT') || define('API_ROOT',HTTP_HOST.'/api/v1/index.php');
defined('INC_ROOT') || define('INC_ROOT',DOC_ROOT.'inc/');
defined('SQL_ROOT') || define('SQL_ROOT',INC_ROOT.'sql/');
defined('RB_ROOT') || define('RB_ROOT',DOC_ROOT.'red_button/');
defined('APP_ROOT') || define('APP_ROOT',DOC_ROOT.'app/');
defined('SSH_ROOT') || define('SSH_ROOT',DOC_ROOT . '../');
defined('MODULE_ROOT') || define('MODULE_ROOT',DOC_ROOT.'module/');
defined('MEDIA_ROOT') || define('MEDIA_ROOT',DOC_ROOT.'media/');
defined('MEDIA_PRIV_ROOT') || define('MEDIA_PRIV_ROOT',DOC_ROOT.'var/media/');
defined('VAR_ROOT') || define('VAR_ROOT',DOC_ROOT.'var/');
defined('TEST_ROOT') || define('TEST_ROOT',DOC_ROOT.'test/');
defined('VENDOR_ROOT') || define('VENDOR_ROOT',DOC_ROOT.'../vendor/');
defined('DATA_ROOT') || define('DATA_ROOT',DOC_ROOT.'../data/');

$NO = array('No','Nein','n','N');

if (file_exists(__DIR__ . '/config.inc.php')) include __DIR__ . '/config.inc.php';

defined('TEST_MAIL_RECIPIENT') || define('TEST_MAIL_RECIPIENT', ADMIN_MAIL);

defined('FEATURE_DESCR') || define('FEATURE_DESCR','2000');
defined('LIST_TEXT_LENGTH') || define('LIST_TEXT_LENGTH', 60);
defined('DEFAULT_MAIL_FROM') || define('DEFAULT_MAIL_FROM', ADMIN_MAIL);

defined('SQL_AUTOEXEC_DAYS') || define('SQL_AUTOEXEC_DAYS', 100); // days until sql statements are autoexecuted
error_reporting(E_ALL ^ E_NOTICE);
ini_set('log_errors', 'On');

// Set the error log file path
ini_set('error_log', VAR_ROOT . '/logs/php_errors.log');
?>