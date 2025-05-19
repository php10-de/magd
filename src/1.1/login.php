<?php
$modul = "login";
$area = "all";

require(__DIR__ . "/inc/req.php");

validate("email", "string");
validate("pw", "string");
validate("ref", "string");
validate("var", "string");
validate("pk", "string");
validate("id", "int");
validate("uuid", "string");
validate("stay", "boolean");
if (isset($_VALID['id'])) {
    // Allow to change the identity only for users with the right for doing so
    RR(1);
    $trueUserId = $_SESSION['user_id'];
    unset($_SESSION);
    session_destroy();
    session_regenerate_id();
    session_start();
    $_SESSION['true_user_id'] = $trueUserId;
}

if (isset($_VALID['uuid'])) {
    // Check right to login with UUID (mobile phone device ID)
    RR(12);
}

if ($_VALID['id'] OR $_VALID['uuid'] OR ( $_VALID['email'] && $_VALID['pw'])) {

    $pwsql = "SELECT user_id, firstname, lastname, lang FROM user WHERE is_active=1 AND ";
    if ($_VALID['id']) {
        $pwsql .= "user_id=" . $_VALIDDB['id'];
    } else if ($_VALID['uuid']) {
        $pwsql .= "uuid=" . $_VALIDDB['uuid'];
    } else {
        $pwsql .= "email =" . str_replace("\'", "", $_VALIDDB['email']) . " AND password = '" . my_sql(sha1($_VALID['pw'] . SALT)) . "'";
    }
    /*echo $_VALID['pw'].'<br>';
    echo SALT.'<br>';
    echo my_sql(sha1($_VALID['pw'] . SALT)).'<br>';
    echo $pwsql;
    exit;*/
    $pwresult = mysqli_query($con, $pwsql);
    $uRow = mysqli_fetch_array($pwresult);
    if ($uRow) {
        $user_id = $uRow['user_id'];
        $lang = $uRow['lang'];
        require INC_ROOT . 'login_actions.inc.php';
        setcookie('login_oid', $oid, time() + (86400 * 30 * 30), "/");
        if (!isset($_COOKIE['login_oid']) OR ! $_COOKIE['login_oid']) {
            header("Refresh:0");
        }
        if ($_VALID["ref"] != "") {
            $_VALID["ref"] .= ($_VALID["var"]) ? '&' . str_replace('&amp;', '&', $_VALID["var"]) : '';
            header('Location:' . $_VALID["ref"]);
            exit;
        } else {
            header('Location:start.php');
            exit;
        }
    } else {
        $headerError = ss('Wrong password.');
    }
} else if (isset($_REQUEST['submitted'])) {
    $headerError = ss('Please enter username and password.');
}/* else {
  $homeMsg = ss('Driver assistance systems and self-driving car');
  } */

$n4a['user_d.php'] = ss('Register');
$n4a['forgotpw.php'] = ss('Forgot Password?');
//require("inc/header.inc.php");
?>

<html>
<head>
    <!-- Basic -->
    <meta charset="UTF-8">

    <meta name="keywords" content="Hrose" />
    <meta name="description" content="Hrose">
    <meta name="author" content="Hrose">

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- replace:css -->
    <link rel="stylesheet" href="<?php echo HTTP_SUB ?>assets/css/header.login.css?version=<?php echo $front_version; ?>" />
    <!-- /replace:css -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css"/>

    <!-- replace:js -->
    <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/header.login.js?version=<?php echo $front_version; ?>"></script>
    <!-- /replace:js -->


</head>
<body>
<?php
if ($_VALID['email'] == "") {
    $email = ss('E-Mail');
} else {
    $email = $_VALID['email'];
}
if ($_VALID['pw'] == "") {
    $pass = ss('Password');
} else {
    $pass = $_VALID['pw'];
}
?>
<?php
if ($err) {
    echo '<span class="red">' . $err . '</span>';
}
?>
<?php
if ($_VALID['id']) {
    echo '<input type="hidden" name="id" value="' . $_VALID['id'] . '">';
}
?>
<?php
$_COOKIE['email'] = str_replace("\'", "", $_COOKIE['email']);
$_COOKIE['password'] = str_replace("\'", "", $_COOKIE['password']);
?>
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            <form class="login100-form validate-form"  name="formlogin" action="login.php" method="post">
                <?php if($_VALID['id']) {
                    echo '<input type="hidden" name="id" value="'.$_VALID['id'].'">';
                }?>
                <?php if ($_VALID["ref"] != "") { ?>
                    <input type="hidden" name="ref" value="<?php echo $_VALID['ref']; ?>">
                    <input type="hidden" name="var" value="<?php echo $_VALID['var']; ?>">
                <?php } ?>
                <span class="login100-form-logo">
                            <div class="text-center video-logo" style="height: 230px;overflow: hidden;position: relative;">
                                <a href="/" class="logo">
                                    <!--     -webkit-box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%);
                box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%); -->
                                    <video id="logo-video" playsinline autoplay muted loop style="position: absolute;left: 0;top: -130px;">
                                        <source src="/assets/videos/MAGD-logo.mp4" type="video/mp4">
                                    </video>
                                </a>
                            </div>
                            <div class="image-logo text-center">
                                <img src="/assets/images/magd-logo.jpg" alt="logo" style="width:100%"/>
                            </div>
                        </span>

                <div class="wrap-input100 validate-input" data-validate = "Enter Email">
                    <input class="input100"  id="email" name="email" type="text" placeholder="<?php echo ss('E-Mail'); ?>" value="<?php if (isset($_COOKIE["email"])) echo $_COOKIE['email']; ?>">
                    <span class="focus-input100" data-placeholder="&#xf0e0;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <input class="input100" name="pw" id="login_pw" type="password"  placeholder="<?php echo ss('Password'); ?>" value="<?php if (isset($_COOKIE["password"])) echo $_COOKIE['password']; ?>">
                    <span class="focus-input100" data-placeholder="&#xf023;"></span>
                </div>
                <div class="text-center p-b-20">
                    <?php
                    if ($headerError) {
                        echo '<span style="color:#d2322d!important" class="text-danger">' . $headerError . '</span>';
                    }
                    ?>
                </div>

                <!--                        <div class="contact100-form-checkbox hide">
                                            <input class="input-checkbox100" id="RememberMe" name="rememberme" type="checkbox">
                                            <label class="label-checkbox100" for="RememberMe">
                                                Remember me
                                            </label>
                                        </div>-->

                <div class="container-login100-form-btn">
                    <button name="submitted" type="submit" class="login100-form-btn">
                        <?php sss('Log in') ?>
                    </button>
                </div>

                <div class="text-center p-t-90">
                    <a class="txt1" href="/forgotpw.php">
                        Forgot Password?
                    </a>
                </div>
            </form>
            <p class="text-center m-t-30">&copy; Copyright 2021. All rights reserved.</p>
        </div>
    </div>
</div>
<!-- replace:bottomjs -->
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/footer.login.js?version=<?php echo $front_version; ?>"></script>
<!-- /replace:bottomjs -->

<script type="text/javascript">
    function clear_email()
    {
        if ($('#email').val() == '<?php sss('E-Mail') ?>')
        {

            $("#email").get(0).value = "";
        }

    }
    function clear_password()
    {
        if ($('#login_pw').val() == '<?php sss('Password') ?>')
        {
            $("#login_pw").get(0).value = "";
        }
    }
</script>

</body>
</html>

