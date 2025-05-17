<?php
// Limit
if (isset($_REQUEST['limit'])) {
    $_SESSION[$modul]['limit'] = $_REQUEST['limit'];
} else if (!$_SESSION[$modul]['limit']) {
    $_SESSION[$modul]['limit'] = STANDARD_LIMIT;
}

?>