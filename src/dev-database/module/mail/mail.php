<?php
if (isset($_REQUEST['read_mail'])) {
    require __DIR__ . '/read_mailNewsletter.php';
} else if (isset($_REQUEST['prepare'])) {
    require __DIR__ . '/prepare.php';
} else if (isset($_REQUEST['send'])) {
    require __DIR__ . '/send.php';
}

