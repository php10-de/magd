<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$modul="test";

require("inc/req.php");
ini_set('display_errors', 0);

/*** Rights ***/
// Generally for people with the right
$groupID = 1;
GRGR($groupID);


require VENDOR_ROOT . 'phpmailer/phpmailer/src/Exception.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/PHPMailer.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/SMTP.php';

//Load Composer's autoloader
require VENDOR_ROOT . 'autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = true;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = SMTP_SERVER;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username       = SMTP_USER;                     //Set the SMTP server to send through
    $mail->Password       = SMTP_PASS;                     //Set the SMTP server to send through
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    //$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure =
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    //$mail->setFrom('foerderverein@paula-fuerst-gemeinschaftsschule.de', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
    $mail->setFrom('info@your-domain.de', 'Your Name');
    $mail->addAddress('test@your-domain.de');               //Name is optional
    $mail->CharSet = 'utf-8';
    $mail->addReplyTo('test@your-domain.de', 'Your Name');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Test';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>' . time();
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

echo '<br>Ende';exit;
