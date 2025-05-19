<?php
$modul="bill";

require("inc/req.php");
require("module/imap/Mailbox.php");

define('DEBUG_MAIL', isset($_REQUEST['debug']));

// Bsp.

// 4. argument is the directory into which attachments are to be saved:
//$mailbox = new PhpImap\Mailbox('{s155.goserver.host:993/imap/ssl}INBOX', 'newsletter@fraunholz.technology', '', __DIR__);
$mailbox = new PhpImap\Mailbox('{172.16.1.15:993/imap/ssl/novalidate-cert}INBOX', 'sal-a\magd', 'Matr!x42', __DIR__);

//die(print_r(parseSubjectCmd('461,30 Riccardo 25,10. sami')));


// Read all messaged into an array:

$mailsIds = $mailbox->searchMailbox('ALL', SE_UID, 'UTF-8');
if(!$mailsIds) {
    if (DEBUG_MAIL) print('Mailbox has no unseen mails');
}
$mailParseError = [];
foreach ($mailsIds as $mailId) {
// Get the message and save its attachment(s) to disk:
    $mail = $mailbox->getMail($mailId);
    if ($mail->seen) {
        continue;
    }
    $subject = $mail->subject;
    $date = date('Y-m-d H:i:s', strtotime($mail->date));
    $fromName = $mail->fromName;
    $fromAddress = $mail->fromAddress;
    $textContent = $mail->textPlain;
    $htmlContent = $mail->textHtml;
    if (DEBUG_MAIL) {
        echo '<hr>';
        echo '<b>' . $subject . '<b><br>';
        echo '<br>';
        //var_dump($mail);
    }

    $sql = "INSERT INTO inbox (from_address,from_name,subject,text_content,html_content,datum) VALUES("
        . "'" . mysqli_real_escape_string($con, $fromAddress) . "',"
        . "'" . mysqli_real_escape_string($con, $fromName) . "',"
        . "'" . mysqli_real_escape_string($con, $subject) . "',"
        . "'" . mysqli_real_escape_string($con, $textContent) . "',"
        . "'" . mysqli_real_escape_string($con, $htmlContent) . "',"
        . "'" . $date . "'"
        . ")";
    if (DEBUG_MAIL) echo $sql.'<br>';
    mysqli_query($con, $sql) or die(mysqli_error($con));
    $inboxId = mysqli_insert_id($con);


    $attachments = $mail->getAttachments();
    foreach ($attachments as $attachment) {
        if (DEBUG_MAIL) echo 'Attachment found ' . '<br>';
        if (copy($attachment->filePath, MEDIA_PRIV_ROOT
            . "inbox/attachment_media_"
            . $inboxId
            . '_' . $attachment->name)) {
            unlink($attachment->filePath);
            $u = "UPDATE inbox SET attachment_media='inbox/attachment_media_"
                . $inboxId
                . "_" . $attachment->name . "'
                WHERE inbox_id =" . $inboxId;
            if (DEBUG_MAIL) echo $u . '<br>';
            mysqli_query($con, $u) or die(mysqli_error($con));
            //error_log($u);
        }
        //echo "\n\nName: " . $attachment->name."\n";
        //echo "\n\nfilePath: " . $attachment->filePath."\n";
    }
}
if (count($mailParseError)) {
    echo implode('<br>', $mailParseError);
}
if (DEBUG_MAIL) {
    exit;
}
?>