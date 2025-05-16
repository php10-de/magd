<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MAIL_DEBUG_MODE', false);
define('DU', false);


$sql = "SELECT mail.mail_id, mail.recipient, mail.recipient_name, mail.subject, mail.content, mail.attachment_media, mail_sender.from_mail, mail_sender.from_name, mail_sender.response_mail, mail_sender.response_name FROM mail 
    LEFT JOIN mail_group ON mail.mail_group=mail_group.mail_group_id
    LEFT JOIN mail_sender ON mail_group.sender=mail_sender.mail_sender_id
WHERE dsent IS NULL OR dsent='0000-00-00 00:00:00'";

if (MAIL_DEBUG_MODE) {
    echo $sql.'<br>';
}
//ini_set('display_errors', 'on');
$mailRes = mysqli_query($con, $sql) or die(mysqli_error($con));

while ($row = mysqli_fetch_array($mailRes)) {
    try {
        require_once VENDOR_ROOT . 'phpmailer/phpmailer/src/Exception.php';
        require_once VENDOR_ROOT . 'phpmailer/phpmailer/src/PHPMailer.php';
        require_once VENDOR_ROOT . 'phpmailer/phpmailer/src/SMTP.php';

//Load Composer's autoloader
        require_once VENDOR_ROOT . 'autoload.php';

//Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = MAIL_DEBUG_MODE;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            //$mail->Host       = 'smtp.strato.de';                     //Set the SMTP server to send through
            //$mail->Host       = 'cloud2-vm549.de-nserver.de';                     //Set the SMTP server to send through
            $mail->Host       = SMTP_SERVER;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            //$mail->Username   = 'foerderverein@paula-fuerst-gemeinschaftsschule.de';                     //SMTP username
            //$mail->Username   = 'q@fv-kls.de';                     //SMTP username
            $mail->Username   = SMTP_USER;                     //SMTP username
            //$mail->Password   = 'paulaf...';                               //SMTP password
            //$mail->Password   = 'Mukootity50$';                               //SMTP password
            $mail->Password   = SMTP_PASS;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            //$mail->setFrom('foerderverein@paula-fuerst-gemeinschaftsschule.de', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
            $mail->setFrom($row['from_mail'], $row['from_name']);
            $mail->addAddress($row['recipient'], $row['recipient_name']);     //Add a recipient
            //$mail->addAddress('fraunholz@mac.com');               //Name is optional
            $mail->CharSet = 'utf-8';
            $mail->addReplyTo($row['response_mail'], $row['response_name']);
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            if ($row['attachment_media'] && file_exists(MEDIA_PRIV_ROOT . $row['attachment_media'])) {
                $saveLastName = $name = preg_replace('/[^\w\-\.]/u', '_', $row['name']);
                $mail->addAttachment(MEDIA_PRIV_ROOT . $row['attachment_media'], 'sal-a.pdf');
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $row['subject'];
            $mail->Body    = $row['content'];
            //mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $succ = $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        flush();
        sleep(rand(1,4));

        if ($succ) {
            if (MAIL_DEBUG_MODE) {
                echo 'Mail sent to ' . $row['recipient'] . '<br>';
            }
            $sql = "UPDATE mail set dsent=now() WHERE mail_id = " . (int) $row['mail_id'];
            $data = mysqli_query($con, $sql);
            $_GET['ok'] = 'done';
            if (CRONRUN) {
                echo 'done';
                exit;
            }
        } else {
            $sql = "UPDATE mail set dsent='0000-00-00' WHERE mail_id = " . (int) $row['mail_id'];
            $data = mysqli_query($con, $sql);
            echo 'Fehler beim Versand';
        }
    } catch (Exception $e) {
        if (CRONRUN) {
            echo $e->getMessage();
            exit;
        }
        $error[] = $e->getMessage();
    }
}
if (MAIL_DEBUG_MODE) exit;
?>
