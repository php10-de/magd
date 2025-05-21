<?php
if(isset($_REQUEST['submit_send'])) {
    if (2 === (int) $_REQUEST['mail_letter_id']){
        include MODULE_ROOT.'mail_letter/send_payment_reminder.php';
    } else if (1 === (int) $_REQUEST['mail_letter_id']){
        include MODULE_ROOT.'mail_letter/send_inspection_reminder.php';
    }
}