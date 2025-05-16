<?php
$modul = 'healthcheck';
if(isset($include)){
    foreach($include as $key => $value){
        require($value);
    }
}else{
    require("../inc/req.php");
}

define('SEND_FAILURE_MAIL', true);
if (!isset($_REQUEST['TOKEN']) || $_REQUEST['TOKEN'] !== HEALTHCHECK_TOKEN) {
    die('HEALTHCHECK_TOKEN missing');
}

$cmd = 'export HTTP_HOST=' . HTTP_HOST. ' && ' . VENDOR_ROOT . 'phpunit/phpunit/phpunit ' . DOC_ROOT . 'tests';
//echo $cmd;die();
$output = shell_exec($cmd);
if (!$output) {
    die('Please run <br><br>' . $cmd);
}

if (strpos($output, 'OK (') === false) {
    echo '<h1>Failure</h1>';
    if (SEND_FAILURE_MAIL) {
        $to      = ADMIN_MAIL;
        $subject = 'Test failure ' . HTTP_HOST;
        $message = $output;
        $headers[] = 'From: Hrose HealthCheck <healthcheck@hrose.eu>';
        mail($to, $subject, $message, implode("\r\n", $headers));
    }
} else {
    echo '<h1>OK</h1>';
}
echo nl2br($output);
//echo 'Console cmd:<br>' . $cmd;
