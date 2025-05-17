<?php
$modul="register";

require("inc/req.php");

validate('email', 'string');
validate('pw', 'string');
validate('firstname', 'string');
validate('lastname', 'string');
validate('klasse_id', 'int');

if (isset($_REQUEST['submitted'])) {
    if (!$_VALID['firstname'] || !$_VALID['lastname'] || !$_VALID['email'] || !$_VALID['pw']) {
        $headerError = ss('Bitte füllen Sie alle Felder aus.');
    } else if (isset($_REQUEST['gev']) && !$_VALID['klasse_id'] ) {
        $headerError = ss('Bitte füllen Sie alle Felder aus.');
    } else {

        $checkSql = "SELECT user_id FROM user WHERE email='" . $_VALID['email'] . "'";
        $checkRes = mysqli_query($con, $checkSql);
        $checkRow = mysqli_fetch_row($checkRes);
        if (count($checkRow)) {
            $_VALID['user_id'] = $checkRow[0];
            $res = true;
        } else {
            $res               = mysqli_query($con, $sql);
            $sql               = "INSERT INTO user(firstname, lastname, email, lang" . (($_VALID['pw']) ? ', password' : '') . ")
                VALUES (" . $_VALIDDB['firstname'] . "," . $_VALIDDB['lastname'] . "," . $_VALIDDB['email'] . ",'DE'" . (($_VALID['pw']) ? ",'" . my_sql(sha1($_VALID['pw'] . SALT)) . "'" : "") . ")";
            $res               = mysqli_query($con, $sql);
            $_VALID['user_id'] = mysqli_insert_id($con);
            require MODULE_ROOT . 'user/user.insert.php';
        }
        if (isset($_REQUEST['gev'])) {
            require MODULE_ROOT . 'user/elternvertreter.insert.php';
        }
        if (!$res) {
            $headerError = ss('Something went wrong.');
        } else {
            setcookie("email", $_VALIDDB['email'], time()+360000);
            setcookie("password", $_VALIDDB['pw'], time()+360000);
            $_SESSION[$modul]['rl'] = true;
            header('Location: register.php?ok=Registration succesful');
            exit;
        }
    }
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $data[$key] = $value;
    }
}
?>
<html>
    <head>
        <!-- Basic -->
        <meta charset="UTF-8">

        <meta name="keywords" content="<?php echo TITLE?>" />
        <meta name="description" content="<?php echo TITLE?>">
        <meta name="author" content="<?php echo TITLE?>">

        <link rel="shortcut icon" type="image/jpg" href="/assets/images/fv-kls/favicon.ico"/>

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
if ($error) {
    echo '<p class="error">' . implode('<br>', $error) . '</p>';
}
?>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            <form class="login100-form validate-form"  name="formlogin" action="register.php" method="post">
                <span class="login100-form-logo">
                    <div class="text-center video-logo" style="height: 230px;overflow: hidden;position: relative;">
                        <a href="/" class="logo">
                            <!--     -webkit-box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%);
        box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%); -->
                                                                <img src="https://www.koenigin-luise-stiftung.de/sites/all/themes/klsv2/images/placeholder.jpg" width="200">
                            <!--<video id="logo-video" playsinline autoplay muted loop style="position: absolute;left: 0;top: -130px;">
                                <source src="/assets/videos/MAGD-logo.mp4" type="video/mp4">
                            </video>-->
                        </a>
                    </div>
                    <div class="image-logo text-center">
                        <  <img src="https://www.koenigin-luise-stiftung.de/sites/all/themes/klsv2/images/placeholder.jpg" alt="logo" style="width:200px"/>
                    </div>
                </span>


                <?php if (isset($_REQUEST['ok'])) {
                    echo '<div class="text-center p-b-20">
                    <span class="success">Vielen Dank! Die Registrierung war erfolgreich.</span>'
                    . '</div>';
                    exit;
                } ?>

                <div class="wrap-input100 validate-input" data-validate = "Enter Email">
                    <input class="input100" id="email" name="email" type="text" placeholder="<?php echo ss('E-Mail'); ?>" value="<?php if (isset($_REQUEST["email"])) echo $_REQUEST['email']; ?>">
                    <span class="focus-input100" data-placeholder="&#xf0e0;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Enter Firstname">
                    <input class="input100"  id="firstname" name="firstname" type="text" placeholder="<?php echo ss('Firstname'); ?>" value="<?php if (isset($_REQUEST["firstname"])) echo $_REQUEST['firstname']; ?>">
                    <span class="focus-input100" data-placeholder="&#xf2b9;"></span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Enter Lastname">
                    <input class="input100"  id="lastname" name="lastname" type="text" placeholder="<?php echo ss('Lastname'); ?>" value="<?php if (isset($_REQUEST["lastname"])) echo $_REQUEST['lastname']; ?>">
                    <span class="focus-input100" data-placeholder="&#xf2b9;"></span>
                </div>

<?php if(isset($_REQUEST['gev'])) {?>
<input type="hidden" name="gev" value="1">

    <div class="wrap-input100 validate-input" data-validate = "Klasse">
        <select name="klasse_id" class="input100" required="required" class="form-select">
            <option selected>Klasse...</option>
            <?php
            $klasseSql = "SELECT klasse_id, name FROM klasse WHERE 1";
            $r = mysqli_query($con, $klasseSql) or die(mysqli_error($con));
            while($row=mysqli_fetch_array($r)) {
                echo '<option value="' . (int) $row['klasse_id'] . '" ' . (($row['klasse_id'] == $_VALID['klasse_id'])?' selected="selected"':'') . '>' . $row['name'] . '</option>';
            }?>
        </select>
        <span class="focus-input100" data-placeholder="&#xf19d;"></span>
    </div>
<?php } ?>

                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <input class="input100" name="pw" id="login_pw" type="password"  placeholder="<?php echo ss('Password'); ?>" value="">
                    <span class="focus-input100" data-placeholder="&#xf023;"></span>
                </div>
                <div class="text-center p-b-20">
                    <?php
                    if ($headerError) {
                        echo '<span style="color:#d2322d!important" class="text-danger">' . $headerError . '</span>';
                    }
                    ?>
                </div>

                <div class="container-login100-form-btn">
                    <button name="submitted" type="submit" class="login100-form-btn">
                        Registrieren
                    </button>
                </div>

</form>
        </div>
    </div>
</div>
<?php if($err!="") {
    echo '<br><span class="red">'.$err.'</span>';
}

?>
<!-- replace:bottomjs -->
<script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/footer.register.js?version=<?php echo $front_version; ?>"></script>
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
