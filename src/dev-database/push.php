<?php

/**
 * Hinweis bei Problem:
 * In .git/config
 * [user] Sektion mit name und email aufnehmen
 */

$modul = "red_button";

require_once("inc/req.php");

GRGR(3);

if (isset($_REQUEST['manual']) || isset($_REQUEST['submit'])) {
    define('SILENT', false);
} else {
    define('SILENT', true);
}

function puShitBaby($gitMessage = '--no message --') {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git add .";
    _exec($cmd);

    //sleep(2);
    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git commit -m " . escapeshellarg($gitMessage);
    _exec($cmd);

    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git push origin master";
    _exec($cmd);
}

//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//parse_str(file_get_contents('php://input'), $post);

if (isset($_REQUEST['submit'])) {
    $gitMessage = isset($_REQUEST['msg'])?$_REQUEST['msg']:'';
    puShitBaby($gitMessage);
} else if ($GIT_MESSAGE) {
    puShitBaby($GIT_MESSAGE);
}

/*
$git = new git();
$git->run('git commit -m \'test\'', array('php10', 'gottoosion'));
$git->run('git push origin master', array('php10', 'gottoosion'));

class git {

    public function run($command, array $arguments = array()) {
        $pipes = array();
        $directory = '/home/www/universal-kiosk.com';
        $descriptorspec = array(
            array('pipe', 'r'),  // STDIN
            array('pipe', 'w'),  // STDOUT
            array('file', $directory . '/error.txt', 'w'),  // STDERR
        );
        $process = proc_open($command, $descriptorspec, $pipes);
        foreach ($arguments as $arg) {
            // Write each of the supplied arguments to STDIN
            fwrite($pipes[0], (preg_match("/\n(:?\s+)?$/", $arg) ? $arg : "{$arg}\n"));
        }
        $response = stream_get_contents($pipes[1]);
        // Make sure that each pipe is closed to prevent a lockout
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        proc_close($process);
        return $response;
    }
}
*/

if (!SILENT) {
    ?>
    <html>
    <body>
    <div style="align:self-center;width:100%;text-align:center;padding-top:250px;-webkit-font-smoothing:subpixel-antialiased;line-height:1.4em;min-width:600px">
        <form action="push.php" method="post">
            <input type="submit" name="submit" value="Push" style="background:#5AABAE;border-radius:36px;font-size:20px;width:424px;height:164px;color:#ffffff;padding:15px 10px 2px 10px;text-transform:uppercase;font-family:'PT Sans Narrow', sans-serif;font-weight:700;letter-spacing:1.2px;touch-action:manipulation;border:1px solid transparent;transition:background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;cursor:pointer"><br><br>
            <input type="text" name="msg" placeholder="message" style="width: 270px;background: #fbfbfb;margin:2px 6px 16px 6px;font-size: 14px; border:1px solid #ddd;box-shadow: inset 0 1px 2px rgba(0,0,0,.07);color:#32373c;transition:50ms border-color ease-in-out">
        </form></div>
    </body>
    </html>
    <?php
}
?>