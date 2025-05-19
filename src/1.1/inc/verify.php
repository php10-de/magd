<?php

if($_SESSION["logedin"]) {
    // stuff after login

} else {

    $loginOK = false;
    if (isset($_COOKIE['logedin'])) {
        $loginOK = true;
        $user_id = $_COOKIE['logedin'];
        $lang = $_COOKIE['lang']?$_COOKIE['lang']:'DE';
    } else if (isset($_REQUEST['k'])) {
        $sqlK    = "SELECT user_id, firstname, lastname, lang FROM user WHERE is_active=1 AND login_link='?k=" . mysqli_real_escape_string($con, $_REQUEST['k']) . "'";
        $kResult = mysqli_query($con, $sqlK);
        $uRow    = mysqli_fetch_array($kResult);
        if ($uRow) {
            $loginOK = true;
            $user_id = $uRow['user_id'];
            $lang    = $uRow['lang'];
            require INC_ROOT . 'login_actions.inc.php';
        }
    }

    if ($loginOK) {
        require INC_ROOT . 'login_actions.inc.php';
    } else {

        // URL Parameter mitnehmen        -----------//
        if(count($_GET)!=0) {
            foreach($_GET as $key => $value) {
                $vars.=$key."=".$value."&";
            }
            $vars = substr($vars,0,-1);
        }

        header("Location: " . HTTP_HOST . "login.php?ref=".basename($_SERVER['PHP_SELF'])."?rn&var=".urlencode($vars));

    }
}

?>