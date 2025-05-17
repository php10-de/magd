<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$modul="forgot_pw";

require("inc/req.php");

validate("email","string");
$success = false;

require VENDOR_ROOT . 'phpmailer/phpmailer/src/Exception.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/PHPMailer.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/SMTP.php';

//Load Composer's autoloader
require VENDOR_ROOT . 'autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if($_VALID['email'] && strpos($_VALID['email'],"demo")===false){
    $newpassword = ae_gen_password(2,false);

    $membersql="UPDATE user
				SET password = '" . my_sql(sha1($newpassword.SALT)) . "'
				WHERE email = " . ($_VALIDDB['email']);
    $memberresult = mysqli_query($con, $membersql);
    /*echo $newpassword.'<br>';
    echo SALT.'<br>';
    echo my_sql(sha1($newpassword.SALT)).'<br>';
    die();*/
    $resultB = mysqli_affected_rows($con);
    if (!$resultB) {
        $err.=" ".ss('E-Mail nicht gefunden.')." ";
    } else {
        $success = true;
        $msg= ss("Guten Tag,")."\r\n\r\n ".ss("Ihr Passwort wurde zurückgesetzt auf:")."\r\n\r\n " . $newpassword;
        $subject= "Passwort zurückgesetzt";
		//echo $msg;die();
        try {
            //Server settings
            $mail->SMTPDebug = false;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = SMTP_SERVER;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = SMTP_USER;                     //SMTP username
            $mail->Password   = SMTP_PASS;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->CharSet = 'utf-8';
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(DEFAULT_MAIL_FROM);
            $mail->addAddress($_VALID['email']);     //Add a recipient
            $mail->addReplyTo(DEFAULT_MAIL_FROM);
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $msg;
            $mail->AltBody = $msg;

            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        //send_mail($msg, $subject, $_VALID['email'], "Hrose user");
    }

} else if ($_GET['submitted']) {
    $headerError = ss('Bitte geben Sie eine E-Mail-Adresse ein.');
}?>
<html>
    <head>
        <!-- Basic -->
        <meta charset="UTF-8">

        <meta name="keywords" content="<?php echo TITLE?>>" />
        <meta name="description" content="<?php echo TITLE?>">
        <meta name="author" content="<?php echo TITLE?>">

        <link rel="shortcut icon" type="image/jpg" href="http://sal-a.de/images/logo_favicon/favicon.ico"/>

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
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <form class="login100-form validate-form"  name="forgotpw" method="get" action="forgotpw.php">
                        <span class="login100-form-logo">
                            <div class="text-center" style="height: 230px;overflow: hidden;position: relative;">
                                <a href="/" class="logo">
                                    <!--     -webkit-box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%);
                box-shadow: 0px 0px 27px 7px rgb(0 0 0 / 35%); -->
                                    <video id="logo-video" playsinline autoplay muted loop style="position: absolute;left: 0;top: -130px;">
                                        <source src="/assets/videos/MAGD-logo.mp4" type="video/mp4">
                                    </video>
                                </a>
                            </div>
                            <div class="contentheadline" style="display:none;"><?php sss('Forgot Password')?></div>
                        </span>
                        
                        <?php if ($success) { ?>
                            <div class="container-login100-form-btn text-center">
                                Ihr Passwort wurde zurückgesetzt. <br/> Bitte prüfen Sie Ihr E-Mail-Postfach.
                            </div>
                            <div class="container-login100-form-btn">
                                <a href='login.php'><?php echo ss('Login'); ?></a>
                            </div>
                        <?php } else { ?>
                        <div class="wrap-input100 validate-input" data-validate = "Enter Email">
                            <input class="input100"  id="email" name="email" type="text" placeholder="<?php echo ss('E-Mail'); ?>" value="<?php if (isset($_COOKIE["email"])) echo $_COOKIE['email']; ?>">
                            <span class="focus-input100" data-placeholder="&#xf0e0;"></span>
                        </div>
                        
                        <p>
                            <?php if($err!="") {
                                echo '<br><span class="red">'.$err.'</span>';
                            } ?>
                        </p>

                        <div class="container-login100-form-btn">
                            <button name="submitted" type="submit" class="login100-form-btn">
                                <?php echo ss('Passwort zurücksetzen')?>
                            </button>
                        </div>
                        
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- replace:bottomjs -->
        <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/javascripts/login/main.js?version=<?php echo $front_version; ?>"></script>
        <!-- /replace:bottomjs -->
    </body>
</html>
