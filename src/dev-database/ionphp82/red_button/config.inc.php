<?php
define('DEBUG',false);
define('ADMIN_MAIL','your-debug@mail');
define('WEBSITE','HROSE');
define('DEFAULT_GROUP_PERMISSION', 1001);

//define('APPLICATION_PATH', realpath(basename(__FILE)));
//define('APPLICATION_PATH', '/homepages/17/d31998980/htdocs/php10.de/job/');

/*
define('DB_HOST','');
define('DB_USER','');
define('DB_PASS','');
define('DB_NAME','');
$link=mysql_connect(DB_HOST,DB_USER,DB_PASS) or die("db-connect problem!");
mysql_select_db(DB_NAME) or die("db-select problem!");
*/

/*
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../../PEAR', APPLICATION_PATH . '/../../ZF', 
    get_include_path()
)));
 */

session_start();


define('DROPDOWN_MAX_NUM', 50); // maximum number of entries that are allowed to show in a dropdown

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return ucfirst(preg_replace_callback('/_([a-z])/', $func, $str));
  }
?>