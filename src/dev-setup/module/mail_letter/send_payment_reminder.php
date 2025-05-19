<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$sql = 'select * from mail_letter where mail_letter_id='.(int) $_REQUEST['mail_letter_id'];
$res = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($res);
$html = $row['html'];
$subject = $row['title'];

$sql='select user.email, user.lastname, user.lastname, parent.salutation, parent.musik_offen 
from parent
join user on user.user_id = parent.user
where musik_offen > 0';
$res = mysqli_query($con,$sql);


require VENDOR_ROOT . 'phpmailer/phpmailer/src/Exception.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/PHPMailer.php';
require VENDOR_ROOT . 'phpmailer/phpmailer/src/SMTP.php';

//Load Composer's autoloader
require VENDOR_ROOT . 'autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

while($row = mysqli_fetch_assoc($res)){
    try {
        echo $row['email'].'<br>';
        //Server settings
        $mail->SMTPDebug = false;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        //$mail->Host       = 'smtp.strato.de';                     //Set the SMTP server to send through
        $mail->Host       = 's155.goserver.host';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        //$mail->Username   = 'foerderverein@paula-fuerst-gemeinschaftsschule.de';                     //SMTP username
        $mail->Username   = 'web60p7';                     //SMTP username
        $mail->Password   = '233BB332!';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        //$mail->setFrom('foerderverein@paula-fuerst-gemeinschaftsschule.de', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
        $mail->setFrom('b332@fraunholz.technology', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
        $mail->addAddress('ckf@fraunholz.technology', 'Christian Fraunholz');     //Add a recipient
        $mail->addAddress($row['email'],$row['lastname']);               //Name is optional
        $mail->CharSet = 'utf-8';
        //$mail->addReplyTo('foerderverein@paula-fuerst-gemeinschaftsschule.de', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
        $mail->addReplyTo('b332@fraunholz.technology', 'Fö&uuml;rderverein der Paula-Fürst-Gemeinschaftsschule e.V.');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    
        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mailTemplate = file_get_contents(INC_ROOT . 'mail_template.html');
        $msg = 'This is the HTML message body <b>in bold!</b>';
        $Betrag=number_format($row['musik_offen'],2,',');
        if($row['salutation'] == 'Herr'){
            $Anrede='Lieber Herr '.$row['lastname'];
        } else {
            $Anrede='Liebe Frau '.$row['lastname'];
        }
        $html=str_replace('%Anrede%',$Anrede,$html);
        $html=str_replace('%Betrag%',$Betrag,$html);
        $mailMsg = str_replace(['%SIGNATURE%', '%MESSAGE%'], [MAIL_SIGNATURE, $msg], $html);
        $mail->Body    = $mailMsg;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }    
}


echo '<br>Ende';exit;