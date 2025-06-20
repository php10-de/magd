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

function gitExec($cmd) {
    if (!SILENT) {
        echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($cmd) . "</pre>";
    }
    $output = shell_exec($cmd . " 2>&1");
    if ($output === null) {
        return "Fehler beim Ausführen des Befehls.";
    }
    return $output;
}


$homeDir = '/var/www/.php-git-home';

// 1. Ensure the directory exists
if (!is_dir($homeDir)) {
    if (!mkdir($homeDir, 0700, true)) {
        die("Failed to create HOME directory: $homeDir");
    }
}

putenv('HOME=' . $homeDir);

function puShitBaby($gitMessage = '--no message --') {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git add .";
    $addResult = gitExec($cmd);
    if (!SILENT) {
        echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($addResult) . "</pre>";
    }

    //sleep(2);
    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git commit -m " . escapeshellarg($gitMessage);
    $commitResult = gitExec($cmd);
    if (!SILENT) {
        echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($commitResult) . "</pre>";
    }

    $cmd = "cd " . escapeshellarg(DOC_ROOT) ." && git push";
    $pushResult = gitExec($cmd);
    if (!SILENT) {
        echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($pushResult) . "</pre>";
    }
}

function showGitStatus() {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) . " && git status";
    return gitExec($cmd);
}

function pullChanges() {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) . " && git pull";
    return gitExec($cmd);
}

function switchBranch($branchName) {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) . " && git checkout " . escapeshellarg($branchName);
    return gitExec($cmd);
}

function resetBranch() {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) . " && git reset --hard";
    return gitExec($cmd);
}

function cleanBranch() {
    $cmd = "cd " . escapeshellarg(DOC_ROOT) . " && git clean -fd";
    return gitExec($cmd);
}

if (!SILENT) {
    header('Content-Type: text/html; charset=UTF-8');
    require("inc/header.inc.php");
    GRGR(21);

    ?>

    <header class="page-header">
        <div class="right-wrapper pull-right">
            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>

    <div class="container-fluid">
    <div class="row">
        <form action="push.php" method="post">
            <input type="hidden" name="manual" value="1">
            <input type="submit" name="status" value="Status" style="..."><br><br>
            <input type="text" name="msg" placeholder="message" style="...">&nbsp;&nbsp;<input type="submit" name="submit" value="Push" style="..."><br><br>
            <input type="submit" name="pull" value="Pull" style="..."><br><br>
            <input type="text" name="branch" placeholder="Branch-Name" style="...">&nbsp;&nbsp;<input type="submit" name="switch_branch" value="Switch Branch" style="..."><br><br>
            <input type="submit" name="reset" value="Reset/Restore" style="...">
        </form>
        <br>
        <?php

        // check if user is already set in .git/config
        $gitConfigFile = DOC_ROOT . '../.git/config';
        if (file_exists($gitConfigFile)) {
            $configContent = file_get_contents($gitConfigFile);
            if (strpos($configContent, 'user') === false) {
                echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>Git user not configured. Configuring now...</pre>";
                $cmd = 'git config --add safe.directory /var/www/html';
                gitExec($cmd);

                $cmd = 'git config user.email "magd@sal-a.de"';
                gitExec($cmd);

                $cmd = 'git config user.name "MAGD"';
                gitExec($cmd);
            } else {
                //echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>Git user already configured.</pre>";
            }
        } else {
            echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>Git config file not found.</pre>";
        }

        $gitStatus = showGitStatus();

        if (isset($_REQUEST['pull'])) {
            $gitPullOutput = pullChanges();
            echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($gitPullOutput) . "</pre>";
        }

        if (isset($_REQUEST['submit'])) {
            $gitMessage = isset($_REQUEST['msg'])?$_REQUEST['msg']:'';
            puShitBaby($gitMessage);
        } else if ($GIT_MESSAGE) {
            puShitBaby($GIT_MESSAGE);
        }

        if (isset($_REQUEST['reset'])) {
            $resetOutput = resetBranch();
            echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($resetOutput) . "</pre>";
            $resetOutput = cleanBranch();
            echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($cleanOutput) . "</pre>";
        }

        if (isset($_REQUEST['switch_branch'])) {
            $branchName = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : '';
            if (!empty($branchName)) {
                $switchOutput = switchBranch($branchName);
                echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>" . htmlspecialchars($switchOutput) . "</pre>";
            } else {
                echo "<pre style='text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;'>Branch-Name darf nicht leer sein.</pre>";
            }
        }
        ?>
        <pre style="text-align:left;padding:20px;background:#f4f4f4;border:1px solid #ddd;overflow:auto;"><?php echo htmlspecialchars($gitStatus); ?></pre>
    </div>
    <?php
    require 'inc/footer.inc.php';
}
?>